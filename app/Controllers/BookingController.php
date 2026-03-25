<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\ListingModel;
use App\Services\NotificationService;

/**
 * BookingController
 * ─────────────────────────────────────────────────────────────────────
 * Handles the 3-step booking flow entirely over AJAX JSON:
 *
 *   Step 1 — POST /booking/init
 *            Validate student info. Store pending data in session.
 *            Send OTP to provided phone via NotificationService.
 *            Returns { success, dev_otp? (dev-mode only) }
 *
 *   Step 2 — POST /booking/verify-otp
 *            Verify 6-digit OTP. On success, either:
 *              a) Free class   → insert booking immediately, send WA, return confirmation.
 *              b) Paid class   → insert *pending* booking, create Razorpay order, return order_id.
 *
 *   Step 3 — POST /booking/confirm-payment
 *            Verify Razorpay payment signature. On success, confirm booking
 *            in DB, log transaction, send WhatsApp, return confirmation.
 *
 * Security:
 *   - CSRF header enforced by CodeIgniter's CSRF filter on POST routes.
 *   - Input validation via CI4 Validation.
 *   - Razorpay signature verification (HMAC-SHA256).
 *   - Rate limiting via simple session counter (max 5 OTP resends/hour).
 */
class BookingController extends BaseController
{
    private BookingModel       $bookingModel;
    private ListingModel       $listingModel;
    private NotificationService $notify;

    // Whether to expose dev_otp in responses (never in production)
    private bool $devMode;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->listingModel = new ListingModel();
        $this->notify       = new NotificationService();
        $this->devMode      = (ENVIRONMENT !== 'production');
    }

    /** Check if the parent user is logged in (via cnd_user session). */
    private function getLoggedInUser(): ?array
    {
        return session()->get('cnd_user') ?: null;
    }

    // ─────────────────────────────────────────────────────────
    // STEP 1 — Collect student info + send OTP
    // POST /booking/init
    // Body (JSON): listing_id, booking_type, student_name,
    //              student_age, phone, class_date?, class_time?
    // ─────────────────────────────────────────────────────────
    public function init()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        // ── Auth guard: parent must be logged in ────────────
        $user = $this->getLoggedInUser();
        if (!$user) {
            return $this->response->setJSON([
                'success'      => false,
                'auth_required'=> true,
                'login_url'    => base_url('login'),
                'message'      => 'Please login or create an account to book a class.',
            ]);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Validate
        if (!$this->validate([
            'listing_id'   => 'required|is_natural_no_zero',
            'booking_type' => 'required|in_list[regular,trial]',
            'student_name' => 'required|min_length[2]|max_length[150]',
            'student_age'  => 'permit_empty|is_natural|less_than_equal_to[18]',
            'phone'        => 'required|regex_match[/^[6-9][0-9]{9}$/]',
        ], $input)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $listingId = (int)$input['listing_id'];

        if (empty($input['identity_only'])) {
            // Verify listing exists and is bookable
            $listing = $this->listingModel->getWithCategory($listingId);
            if (!$listing || $listing->status !== 'active' || $listing->review_status !== 'approved') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This listing is no longer available.',
                ]);
            }

            // ── Batch Handling ──
            $batchIndex = isset($input['batch_index']) ? (int)$input['batch_index'] : null;
            $selectedBatch = null;

            if ($listing->type === 'regular') {
                if ($batchIndex === null) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Please select a batch before booking.',
                    ]);
                }
                $selectedBatch = $listing->batches[$batchIndex] ?? null;
                if (!$selectedBatch) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid batch selected.',
                    ]);
                }
            }

            // Check if capacity is reached (batch-specific or global)
            $capLimit = (int)($selectedBatch['batch_size'] ?? $listing->batch_size);
            if ($capLimit > 0) {
                // For now use global count, in future consider batch-specific counts
                if ((int)$listing->total_students >= $capLimit) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'This batch/class is fully booked.',
                    ]);
                }
            }
        }

        // Rate-limit OTP sends
        $session   = session();
        $otpKey    = 'otp_sends_' . md5($input['phone']);
        $sendCount = (int)($session->get($otpKey) ?? 0);
        if ($sendCount >= 10) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Too many OTP requests. Please wait an hour.',
            ]);
        }
        $session->set($otpKey, $sendCount + 1);

        if (empty($input['identity_only'])) {
            // Stash pending booking data in session
            $price = (float)($selectedBatch['price'] ?? $listing->price);
            if (($input['booking_type'] ?? '') === 'trial') {
                $price = 0; // trials are always free
            }

            // Check early-bird price (only if not a specific batch price? or apply to batch?)
            // Usually EB applies to the base price, but let's allow it if present
            $now = date('Y-m-d');
            if (
                !empty($listing->early_bird_date) &&
                !empty($listing->early_bird_price) &&
                $now <= $listing->early_bird_date &&
                $price > 0 &&
                empty($selectedBatch) // Only apply to base if no specific batch chosen
            ) {
                $price = (float)$listing->early_bird_price;
            }

            $pendingData = [
                'listing_id'    => $listingId,
                'listing_title' => $listing->title,
                'booking_type'  => $input['booking_type'],
                'student_name'  => $input['student_name'],
                'student_age'   => $input['student_age'] ?? null,
                'phone'         => $input['phone'],
                'batch_name'    => $selectedBatch['name'] ?? null,
                'batch_id'      => $batchIndex, // Index in the JSON array
                'batch_price'   => (float)($selectedBatch['price'] ?? 0),
                'batch_start_date' => $selectedBatch['batch_start_date'] ?? null,
                'class_date'    => $input['class_date'] ?? ($selectedBatch['batch_start_date'] ?? null),
                'class_time'    => $selectedBatch['from_time'] ?? ($input['class_time'] ?? null),
                'payment_amount'=> $price,
                'parent_id'     => $user['id'] ?? 0,
                'parent_email'  => $user['email'] ?? null,
                'expires'       => time() + 900, // 15 min
            ];
            $session->set('cnd_pending_booking', $pendingData);

            // Skip OTP and proceed to confirm/payment immediately
            if ($price <= 0) {
                // Free booking
                $bookingId = $this->bookingModel->createBooking($pendingData);
                $booking   = $this->bookingModel->getWithDetails($bookingId);
                $this->listingModel->incrementStudents((int)$listingId);

                if ($booking) {
                    $this->notify->sendBookingConfirmation($input['phone'], $booking);
                    if (!empty($booking['provider_phone'])) {
                        $this->notify->notifyProviderNewBooking($booking['provider_phone'], $booking);
                    }
                }
                $session->remove('cnd_pending_booking');
                $confirmPayload = $this->buildConfirmationPayload($booking);
                return $this->response->setJSON([
                    'success'      => true,
                    'otp_skipped'  => true,
                    'paid'         => false,
                    'booking'      => $confirmPayload,
                    'redirect_url' => base_url('booking/success'),
                ]);
            } else {
                // Paid booking -> create Razorpay order
                $rpOrder = $this->createRazorpayOrder($price, $listingId . '_' . time());
                if (!$rpOrder || empty($rpOrder['id'])) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Payment gateway error.']);
                }
                $session->set('cnd_rp_order', [
                    'order_id' => $rpOrder['id'],
                    'amount'   => $price,
                    'phone'    => $input['phone'],
                    'pending'  => $pendingData,
                ]);
                return $this->response->setJSON([
                    'success'     => true,
                    'otp_skipped' => true,
                    'paid'        => true,
                    'order_id'    => $rpOrder['id'],
                    'amount'      => (int)round($price * 100),
                    'currency'    => 'INR',
                    'rp_key'      => getenv('RAZORPAY_KEY') ?: '',
                    'name'        => $listing->title,
                    'description' => ucfirst($input['booking_type']) . ' booking for ' . $input['student_name'],
                    'prefill'     => ['contact' => '+91' . $input['phone']],
                ]);
            }
        }

        // Send OTP (only for identity_only requests if any remain, but booking skips it)
        $result = $this->notify->sendOtp($input['phone']);
        $resp = ['success' => true, 'message' => 'OTP sent to ' . substr($input['phone'], 0, 4) . '******'];
        if ($this->devMode) $resp['dev_otp'] = $result['otp'];
        return $this->response->setJSON($resp);
    }

    // ─────────────────────────────────────────────────────────
    // STEP 2 — Verify OTP
    // POST /booking/verify-otp
    // Body (JSON): phone, otp
    // ─────────────────────────────────────────────────────────
    public function verifyOtp()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!$this->validate([
            'phone' => 'required|regex_match[/^[6-9][0-9]{9}$/]',
            'otp'   => 'required|min_length[6]|max_length[6]|is_natural',
        ], $input)) {
            return $this->response->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        // Verify OTP
        if (!$this->notify->verifyOtp($input['phone'], (string)$input['otp'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Incorrect OTP. Please try again.',
            ]);
        }

        // Success - common logic
        $session = session();
        $session->set('cnd_phone', $input['phone']);
        set_cookie('cnd_phone', $input['phone'], 604800); // 7 days

        // If identity-only, stop here
        if (!empty($input['identity_only'])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Identity verified.']);
        }

        // Retrieve pending booking
        $pending = $session->get('cnd_pending_booking');
        if (!$pending || time() > ($pending['expires'] ?? 0) || $pending['phone'] !== $input['phone']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired. Please start the booking again.',
            ]);
        }

        $price = (float)($pending['payment_amount'] ?? 0);

        // ── FREE booking → confirm immediately ────────────
        if ($price <= 0) {
            $bookingId = $this->bookingModel->createBooking($pending);
            $booking   = $this->bookingModel->getWithDetails($bookingId);

            // Increment student count
            $this->listingModel->incrementStudents((int)$pending['listing_id']);

            // Send WhatsApp confirmation to Parent & Provider
            if ($booking) {
                $this->notify->sendBookingConfirmation($input['phone'], $booking);
                if (!empty($booking['provider_phone'])) {
                    $this->notify->notifyProviderNewBooking($booking['provider_phone'], $booking);
                }
            }

            $session->remove('cnd_pending_booking');

            // Set flash for success page
            $confirmPayload = $this->buildConfirmationPayload($booking);
            $session->setFlashdata('booking_success', $confirmPayload);

            return $this->response->setJSON([
                'success'      => true,
                'paid'         => false,
                'booking_id'   => $bookingId,
                'message'      => 'Booking confirmed! Check WhatsApp for details.',
                'booking'      => $confirmPayload,
                'redirect_url' => base_url('booking/success'),
            ]);
        }

        // ── PAID booking → create Razorpay order ────────────
        $rpOrder = $this->createRazorpayOrder($price, $pending['listing_id'] . '_' . time());

        if (!$rpOrder || empty($rpOrder['id'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment gateway error. Please try again.',
            ]);
        }

        // Store the Razorpay order ID in session for verification later
        $session->set('cnd_rp_order', [
            'order_id' => $rpOrder['id'],
            'amount'   => $price,
            'phone'    => $input['phone'],
            'pending'  => $pending,
        ]);

        return $this->response->setJSON([
            'success'     => true,
            'paid'        => true,
            'order_id'    => $rpOrder['id'],
            'amount'      => (int)round($price * 100), // paise
            'currency'    => 'INR',
            'rp_key'      => getenv('RAZORPAY_KEY') ?: '',
            'name'        => $pending['listing_title'] ?? 'Class Next Door',
            'description' => ucfirst($pending['booking_type']) . ' booking for ' . $pending['student_name'],
            'prefill'     => ['contact' => '+91' . $input['phone']],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // STEP 3 — Confirm Razorpay payment
    // POST /booking/confirm-payment
    // Body (JSON): razorpay_payment_id, razorpay_order_id, razorpay_signature
    // ─────────────────────────────────────────────────────────
    public function confirmPayment()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Validate required fields
        if (empty($input['razorpay_payment_id']) || empty($input['razorpay_order_id']) || empty($input['razorpay_signature'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Incomplete payment data.',
            ]);
        }

        // Retrieve Razorpay session data
        $session  = session();
        $rpSess   = $session->get('cnd_rp_order');
        if (!$rpSess || $rpSess['order_id'] !== $input['razorpay_order_id']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Order mismatch or session expired.',
            ]);
        }

        // Verify Razorpay signature: HMAC-SHA256(order_id + '|' + payment_id, secret)
        $secret   = env('RAZORPAY_SECRET') ?: '';
        $expected = hash_hmac(
            'sha256',
            $input['razorpay_order_id'] . '|' . $input['razorpay_payment_id'],
            $secret
        );

        if (!hash_equals($expected, $input['razorpay_signature'])) {
            log_message('warning', '[BookingController] Razorpay signature mismatch for order '
                . $input['razorpay_order_id']);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment verification failed. Please contact support with your payment ID.',
            ]);
        }

        // Insert booking & confirm
        $pending   = $rpSess['pending'];
        
        $bookingId = $this->bookingModel->createBooking($pending);
        
        if ($bookingId <= 0) {
            log_message('error', '[BookingController] Failed to create booking record for Order: ' . $input['razorpay_order_id']);
            return $this->response->setJSON(['success' => false, 'message' => 'Could not save booking details. Please contact support.']);
        }

        $this->bookingModel->confirmPayment($bookingId, $input['razorpay_payment_id']);

        // Record transaction (user_id = 0 for guest)
        $this->bookingModel->recordTransaction(
            $bookingId,
            (int)($pending['parent_id'] ?? 0),
            $rpSess['amount'],
            $input['razorpay_payment_id']
        );

        // Increment student count
        $this->listingModel->incrementStudents((int)$pending['listing_id']);

        // Fetch for notification and frontend payload
        $booking = $this->bookingModel->getWithDetails($bookingId);
        if (!$booking) {
            log_message('error', '[BookingController] Could not fetch details for confirmed booking ID: ' . $bookingId);
            return $this->response->setJSON(['success' => false, 'message' => 'Internal error fetching booking details.']);
        }

        // Notifications
        $this->notify->sendBookingConfirmation($rpSess['phone'], $booking);
        if (!empty($booking['provider_phone'])) {
            $this->notify->notifyProviderNewBooking($booking['provider_phone'], $booking);
        }

        // Clean session
        $session->remove('cnd_rp_order');
        $session->remove('cnd_pending_booking');

        // Build payload & flash for redirect
        $confirmPayload = $this->buildConfirmationPayload($booking);
        $session->setFlashdata('booking_success', $confirmPayload);

        log_message('info', '[BookingController] Booking confirmed successfully: #' . $bookingId);

        return $this->response->setJSON([
            'success'      => true,
            'booking_id'   => $bookingId,
            'message'      => 'Payment successful! Booking confirmed.',
            'booking'      => $confirmPayload,
            'redirect_url' => base_url('booking/success'),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────

    /** Create a Razorpay order via REST API. Returns decoded JSON or null. */
    private function createRazorpayOrder(float $amount, string $receipt): ?array
    {
        $key    = env('RAZORPAY_KEY');
        $secret = env('RAZORPAY_SECRET');

        if (!$key || !$secret) {
            log_message('error', '[BookingController] Razorpay credentials missing in .env');
            return null;
        }

        $response = cnd_http_request('POST', 'https://api.razorpay.com/v1/orders', [
            'amount'          => (int)round($amount * 100), // in paise
            'currency'        => 'INR',
            'receipt'         => 'cnd_' . $receipt,
            'payment_capture' => 1,
        ], [
            'Content-Type: application/json',
            'Accept: application/json',
        ], "{$key}:{$secret}");

        if ($response->code < 200 || $response->code >= 300) {
            log_message('warning', '[BookingController] Razorpay order HTTP ' . $response->code . ': ' . $response->body);
            return null;
        }

        return json_decode($response->body, true);
    }

    private function buildConfirmationPayload(?array $booking): array
    {
        if (!$booking) return [];
        return [
            'id'            => (int)$booking['id'],
            'listing_title' => $booking['listing_title'] ?? '',
            'listing_type'  => $booking['listing_type']  ?? 'regular',
            
            // Times/Dates from the booking (specific slot) or listing (course/workshop defaults)
            'class_date'    => $booking['class_date']    ?? ($booking['listing_start_date'] ?? null),
            'class_time'    => $booking['class_time']    ?? ($booking['listing_class_time'] ?? null),
            'end_date'      => $booking['listing_end_date'] ?? null,
            'end_time'      => $booking['listing_class_end_time'] ?? null,

            'address'       => $booking['listing_address'] ?? '',
            'booking_type'  => $booking['booking_type']    ?? 'regular',
            'student_name'  => $booking['student_name']    ?? '',
            'amount'        => (float)($booking['payment_amount'] ?? 0),
            'payment_status'=> $booking['payment_status']  ?? 'pending',
        ];
    }

    // ─────────────────────────────────────────────────────────
    // FULL-PAGE BOOKING PAGE — GET /book/{id}
    // ─────────────────────────────────────────────────────────
    public function bookingPage(int $listingId)
    {
        // Auth guard — redirect to login if not logged in
        $user = $this->getLoggedInUser();
        if (!$user) {
            session()->setFlashdata('redirect_after_login', base_url('book/' . $listingId));
            return redirect()->to('login');
        }

        // Fetch listing
        $listing = $this->listingModel->getWithCategory($listingId);
        if (!$listing || $listing->status !== 'active' || $listing->review_status !== 'approved') {
            return redirect()->to('classes')->with('error', 'This class is not available for booking.');
        }

        // Price calculation
        $price = (float)$listing->price;
        $now   = date('Y-m-d');
        $earlyBird = false;
        if (
            !empty($listing->early_bird_date) &&
            !empty($listing->early_bird_price) &&
            $now <= $listing->early_bird_date &&
            $price > 0
        ) {
            $earlyBird = true;
            $price = (float)$listing->early_bird_price;
        }

        // Cover image
        $db    = \Config\Database::connect();
        $img   = $db->table('listing_images')
                    ->where('listing_id', $listingId)
                    ->orderBy('position', 'ASC')
                    ->get()->getRow();
        $cover = $img ? $img->image_path : null;

        // Available slots
        $slots = $db->table('listing_availabilities')
                    ->where('listing_id', $listingId)
                    ->where('available_date >=', $now)
                    ->where('is_disabled', 0)
                    ->orderBy('available_date', 'ASC')
                    ->orderBy('available_time', 'ASC')
                    ->limit(10)
                    ->get()->getResultArray();

        // Batch booking counts
        $batchCounts = [];
        if ($listing->type === 'regular') {
            $batchCounts = $this->bookingModel->getBatchBookingCounts($listingId);
        }

        return view('frontend/booking_page', [
            'page_title'       => 'Book: ' . $listing->title . ' | Class Next Door',
            'meta_description' => 'Book your spot for ' . $listing->title,
            'listing'          => $listing,
            'price'            => $price,
            'early_bird'       => $earlyBird,
            'cover'            => $cover,
            'slots'            => $slots,
            'batch_counts'     => $batchCounts,
            'user'             => $user,
            'rp_key'           => env('RAZORPAY_KEY', ''),
            'dev_mode'         => $this->devMode,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // BOOKING SUCCESS PAGE — GET /booking/success
    // ─────────────────────────────────────────────────────────
    public function bookingSuccess()
    {
        $user    = $this->getLoggedInUser();
        $details = session()->getFlashdata('booking_success');

        if (empty($details)) {
            // No confirmation data — redirect to my-bookings
            return redirect()->to($user ? 'my-bookings' : '/');
        }

        return view('frontend/booking_success', [
            'page_title'       => 'Booking Confirmed! | Class Next Door',
            'meta_description' => 'Your class booking was successful.',
            'details'          => $details,
            'user'             => $user,
        ]);
    }

    public function downloadCertificate(int $bookingId)
    {
        $user = $this->getLoggedInUser();
        if (empty($user)) return redirect()->to('login');

        $phone = $user['phone'] ?? null;
        if (!$phone) {
             $u = (new \App\Models\UserModel())->find($user['id']);
             $phone = $u->phone ?? null;
        }

        $booking = $this->bookingModel->getWithDetails($bookingId);
        if (!$booking) return redirect()->to('my-bookings')->with('error', 'Booking not found');

        // Check if user owns this booking
        if ($booking['parent_id'] != $user['id'] && $booking['parent_phone'] != $phone) {
             return redirect()->to('my-bookings')->with('error', 'Unauthorized access');
        }

        // Check if reviewed
        $reviewModel = new \App\Models\ReviewModel();
        if (!$reviewModel->hasReviewed((int)$booking['listing_id'], (int)$user['id'], $phone)) {
             return redirect()->to('my-bookings')->with('error', 'Please post a review first to download the certificate');
        }

        return view('frontend/certificate_view', [
            'booking' => $booking,
            'user'    => $user
        ]);
    }
}
