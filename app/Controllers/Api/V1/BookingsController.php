<?php

namespace App\Controllers\Api\V1;

use App\Models\BookingModel;
use App\Models\ListingModel;
use App\Services\NotificationService;

/**
 * BookingsController — /v1/bookings
 * ─────────────────────────────────────────────────────────────────────
 * Versioned RESTful API for the booking flow.
 *
 * Routes (all under /v1/):
 *   POST bookings/init            Step 1: Validate + send OTP
 *   POST bookings/verify-otp      Step 2: Verify OTP; create order or free booking
 *   POST bookings/confirm-payment Step 3: Verify Razorpay signature + confirm
 *   GET  bookings                 My bookings (requires parent login)
 *
 * All endpoints return the standard ApiBaseController envelope:
 *   { success, api_version, timestamp, data?, meta?, error?, message? }
 */
class BookingsController extends ApiBaseController
{
    protected $helpers = ['basic', 'url', 'cookie'];

    private BookingModel       $bookingModel;
    private ListingModel       $listingModel;
    private NotificationService $notify;
    private bool               $devMode;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->listingModel = new ListingModel();
        $this->notify       = new NotificationService();
        $this->devMode      = (ENVIRONMENT !== 'production');
    }

    // ─── GET /v1/bookings ─────────────────────────────────────────────

    /**
     * @OA\Get(
     *   path="/v1/bookings",
     *   summary="Get my bookings (parent must be logged in)",
     *   tags={"Bookings"},
     *   security={{"sessionAuth":{}}},
     *   @OA\Response(response=200, description="Array of booking objects"),
     *   @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index()
    {
        $user = $this->getParentUser();
        if (!$user) {
            return $this->unauthorized('Please login to view your bookings.');
        }

        $db       = \Config\Database::connect();
        $bookings = $db->table('bookings b')
            ->select('b.*, l.title AS listing_title, l.address AS listing_address, l.type AS listing_type')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->where('b.parent_phone', $user['phone'])
            ->orderBy('b.created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        return $this->success(
            $bookings,
            ['total' => count($bookings)]
        );
    }

    // ─── POST /v1/bookings/init ───────────────────────────────────────

    /**
     * @OA\Post(
     *   path="/v1/bookings/init",
     *   summary="Step 1 — Validate student info and send OTP",
     *   tags={"Bookings"},
     *   security={{"sessionAuth":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"listing_id","booking_type","student_name","phone"},
     *       @OA\Property(property="listing_id",   type="integer"),
     *       @OA\Property(property="booking_type", type="string", enum={"regular","trial"}),
     *       @OA\Property(property="student_name", type="string"),
     *       @OA\Property(property="student_age",  type="integer"),
     *       @OA\Property(property="phone",        type="string"),
     *       @OA\Property(property="class_date",   type="string", format="date"),
     *       @OA\Property(property="class_time",   type="string")
     *     )
     *   ),
     *   @OA\Response(response=200, description="OTP sent"),
     *   @OA\Response(response=401, description="Login required"),
     *   @OA\Response(response=422, description="Validation error")
     * )
     */
    public function init()
    {
        $user = $this->getParentUser();
        if (!$user) {
            return $this->unauthorized('Please login to book a class.');
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->validate([
            'listing_id'   => 'required|is_natural_no_zero',
            'booking_type' => 'required|in_list[regular,trial]',
            'student_name' => 'required|min_length[2]|max_length[150]',
            'student_age'  => 'permit_empty|is_natural|less_than_equal_to[18]',
            'phone'        => 'required|regex_match[/^[6-9][0-9]{9}$/]',
        ], $input)) {
            return $this->validationError($this->validator->getErrors());
        }

        $listingId = (int) $input['listing_id'];
        $listing   = $this->listingModel->getWithCategory($listingId);

        if (!$listing || $listing->status !== 'active' || $listing->review_status !== 'approved') {
            return $this->fail('This listing is no longer available.', 'listing_unavailable');
        }

        // ── Batch Handling ──
        $batchIndex = isset($input['batch_index']) ? (int)$input['batch_index'] : null;
        $selectedBatch = null;
        if ($listing->type === 'regular') {
            if ($batchIndex === null) {
                return $this->fail('Please select a batch before booking.', 'batch_required');
            }
            $selectedBatch = $listing->batches[$batchIndex] ?? null;
            if (!$selectedBatch) {
                return $this->fail('Invalid batch selected.', 'batch_invalid');
            }
        }

        // Rate-limit OTP
        $session   = session();
        $otpKey    = 'otp_sends_' . md5($input['phone']);
        $sendCount = (int) ($session->get($otpKey) ?? 0);
        if ($sendCount >= 5) {
            return $this->fail('Too many OTP requests. Please wait an hour and try again.', 'rate_limited', 429);
        }
        $session->set($otpKey, $sendCount + 1);

        // Build pending booking payload
        $price = (float)($selectedBatch['price'] ?? $listing->price);
        if (($input['booking_type'] ?? '') === 'trial') {
            $price = 0;
        }
        $now = date('Y-m-d');
        if (!empty($listing->early_bird_date) && !empty($listing->early_bird_price)
            && $now <= $listing->early_bird_date && $price > 0 && empty($selectedBatch)) {
            $price = (float) $listing->early_bird_price;
        }

        $session->set('cnd_pending_booking', [
            'listing_id'       => $listingId,
            'listing_title'    => $listing->title,
            'booking_type'     => $input['booking_type'],
            'student_name'     => $input['student_name'],
            'student_age'      => $input['student_age'] ?? null,
            'phone'            => $input['phone'],
            'batch_name'       => $selectedBatch['name'] ?? null,
            'batch_id'         => $batchIndex,
            'batch_price'      => (float)($selectedBatch['price'] ?? 0),
            'batch_start_date' => $selectedBatch['batch_start_date'] ?? null,
            'class_date'       => $input['class_date'] ?? ($selectedBatch['batch_start_date'] ?? null),
            'class_time'       => $selectedBatch['from_time'] ?? ($input['class_time'] ?? null),
            'payment_amount'   => $price,
            'parent_id'        => $user['id'] ?? 0,
            'expires'          => time() + 900,
        ]);

        $result = $this->notify->sendOtp($input['phone']);

        $data = ['message' => 'OTP sent to ' . substr($input['phone'], 0, 4) . '******'];
        if ($this->devMode) {
            $data['dev_otp'] = $result['otp'];
        }

        return $this->success($data);
    }

    // ─── POST /v1/bookings/verify-otp ────────────────────────────────

    /**
     * @OA\Post(
     *   path="/v1/bookings/verify-otp",
     *   summary="Step 2 — Verify OTP, create free booking or Razorpay order",
     *   tags={"Bookings"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"phone","otp"},
     *       @OA\Property(property="phone", type="string"),
     *       @OA\Property(property="otp",   type="string", minLength=6, maxLength=6)
     *     )
     *   ),
     *   @OA\Response(response=200, description="Booking confirmed (free) or Razorpay order (paid)"),
     *   @OA\Response(response=422, description="Validation / OTP error")
     * )
     */
    public function verifyOtp()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->validate([
            'phone' => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'otp'   => 'required|min_length[6]|max_length[6]|is_natural',
        ], $input)) {
            return $this->validationError($this->validator->getErrors());
        }

        if (!$this->notify->verifyOtp($input['phone'], (string) $input['otp'])) {
            return $this->fail('Incorrect OTP. Please try again.', 'invalid_otp');
        }

        $session = session();
        $session->set('cnd_phone', $input['phone']);
        set_cookie('cnd_phone', $input['phone'], 604800);

        $pending = $session->get('cnd_pending_booking');
        if (!$pending || time() > ($pending['expires'] ?? 0) || $pending['phone'] !== $input['phone']) {
            return $this->fail('Session expired. Please start the booking again.', 'session_expired');
        }

        $price = (float) ($pending['payment_amount'] ?? 0);

        // ── FREE booking
        if ($price <= 0) {
            $bookingId = $this->bookingModel->createBooking($pending);
            $booking   = $this->bookingModel->getWithDetails($bookingId);

            $this->listingModel->incrementStudents((int) $pending['listing_id']);

            if ($booking) {
                $this->notify->sendBookingConfirmation($input['phone'], $booking);
                if (!empty($booking['provider_phone'])) {
                    $this->notify->notifyProviderNewBooking($booking['provider_phone'], $booking);
                }
            }

            $session->remove('cnd_pending_booking');

            $payload = $this->buildPayload($booking);
            $session->setFlashdata('booking_success', $payload);

            return $this->success(array_merge($payload, [
                'paid'         => false,
                'redirect_url' => base_url('booking/success'),
            ]));
        }

        // ── PAID booking → create Razorpay order
        $rpOrder = $this->createRazorpayOrder($price, $pending['listing_id'] . '_' . time());
        if (!$rpOrder || empty($rpOrder['id'])) {
            return $this->fail('Payment gateway error. Please try again.', 'payment_gateway_error', 503);
        }

        $session->set('cnd_rp_order', [
            'order_id' => $rpOrder['id'],
            'amount'   => $price,
            'phone'    => $input['phone'],
            'pending'  => $pending,
        ]);

        return $this->success([
            'paid'        => true,
            'order_id'    => $rpOrder['id'],
            'amount'      => (int) round($price * 100),
            'currency'    => 'INR',
            'rp_key'      => getenv('RAZORPAY_KEY') ?: '',
            'name'        => $pending['listing_title'] ?? 'Class Next Door',
            'description' => ucfirst($pending['booking_type']) . ' booking for ' . $pending['student_name'],
            'prefill'     => ['contact' => '+91' . $input['phone']],
        ]);
    }

    // ─── POST /v1/bookings/confirm-payment ───────────────────────────

    /**
     * @OA\Post(
     *   path="/v1/bookings/confirm-payment",
     *   summary="Step 3 — Verify Razorpay signature and confirm booking",
     *   tags={"Bookings"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       required={"razorpay_payment_id","razorpay_order_id","razorpay_signature"},
     *       @OA\Property(property="razorpay_payment_id",  type="string"),
     *       @OA\Property(property="razorpay_order_id",    type="string"),
     *       @OA\Property(property="razorpay_signature",   type="string")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Booking confirmed"),
     *   @OA\Response(response=400, description="Signature mismatch")
     * )
     */
    public function confirmPayment()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (empty($input['razorpay_payment_id']) || empty($input['razorpay_order_id']) || empty($input['razorpay_signature'])) {
            return $this->fail('Incomplete payment data.', 'missing_fields');
        }

        $session = session();
        $rpSess  = $session->get('cnd_rp_order');

        if (!$rpSess || $rpSess['order_id'] !== $input['razorpay_order_id']) {
            return $this->fail('Order mismatch or session expired.', 'session_expired');
        }

        $secret   = env('RAZORPAY_SECRET') ?: '';
        $expected = hash_hmac('sha256', $input['razorpay_order_id'] . '|' . $input['razorpay_payment_id'], $secret);

        if (!hash_equals($expected, $input['razorpay_signature'])) {
            return $this->fail('Payment verification failed.', 'signature_mismatch', 400);
        }

        $pending   = $rpSess['pending'];
        $bookingId = $this->bookingModel->createBooking($pending);
        $this->bookingModel->confirmPayment($bookingId, $input['razorpay_payment_id']);
        $this->bookingModel->recordTransaction($bookingId, (int)($pending['parent_id'] ?? 0), $rpSess['amount'], $input['razorpay_payment_id']);
        $this->listingModel->incrementStudents((int)$pending['listing_id']);

        $booking = $this->bookingModel->getWithDetails($bookingId);
        if ($booking) {
            $this->notify->sendBookingConfirmation($rpSess['phone'], $booking);
            if (!empty($booking['provider_phone'])) {
                $this->notify->notifyProviderNewBooking($booking['provider_phone'], $booking);
            }
        }

        $session->remove('cnd_rp_order');
        $session->remove('cnd_pending_booking');

        $payload = $this->buildPayload($booking);
        $session->setFlashdata('booking_success', $payload);

        log_message('info', '[API BookingsController] Booking confirmed successfully: #' . $bookingId);

        return $this->success(array_merge($payload, [
            'redirect_url' => base_url('booking/success'),
        ]));
    }

    // ─── Helpers ──────────────────────────────────────────────────────

    private function buildPayload(?array $b): array
    {
        if (!$b) return [];
        return [
            'booking_id'     => (int) $b['id'],
            'id'             => (int) $b['id'], // aliases for compatibility
            'listing_title'  => $b['listing_title']   ?? '',
            'listing_type'   => $b['listing_type']    ?? 'regular',

            'class_date'     => $b['class_date']      ?? ($b['listing_start_date'] ?? null),
            'class_time'     => $b['class_time']      ?? ($b['listing_class_time'] ?? null),
            'end_date'       => $b['listing_end_date'] ?? null,
            'end_time'       => $b['listing_class_end_time'] ?? null,

            'address'        => $b['listing_address'] ?? '',
            'booking_type'   => $b['booking_type']    ?? 'regular',
            'student_name'   => $b['student_name']    ?? '',
            'amount'         => (float)($b['payment_amount'] ?? 0),
            'payment_status' => $b['payment_status']  ?? 'pending',
        ];
    }

    private function createRazorpayOrder(float $amountRupees, string $receipt): ?array
    {
        $key    = env('RAZORPAY_KEY');
        $secret = env('RAZORPAY_SECRET');
        if (!$key || !$secret) return null;

        $response = cnd_http_request('POST', 'https://api.razorpay.com/v1/orders', [
            'amount'          => (int) round($amountRupees * 100),
            'currency'        => 'INR',
            'receipt'         => 'cnd_' . $receipt,
            'payment_capture' => 1,
        ], [
            'Content-Type: application/json',
            'Accept: application/json',
        ], "{$key}:{$secret}");

        if ($response->code < 200 || $response->code >= 300) return null;
        return json_decode($response->body, true);
    }
}
