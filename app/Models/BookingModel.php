<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * BookingModel
 * Table: bookings
 */
class BookingModel extends BaseModel
{
    protected $table      = 'bookings';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'listing_id', 'parent_id', 'parent_phone', 'student_name', 'student_age',
        'booking_type', 'batch_name', 'batch_id', 'batch_price', 'batch_start_date', 'class_date', 'class_time',
        'payment_amount', 'payment_id', 'payment_status',
        'booking_status', 'reminder_sent', 'review_reminders', 'completed_at',
        'parent_email', 'email_verified'
    ];

    // ── Parent-facing queries ───────────────────────────────────

    /**
     * Upcoming confirmed bookings for a parent (future dates).
     */
    public function getUpcoming(?int $parentId = null, ?string $phone = null): array
    {
        $builder = $this->db->table('bookings b')
            ->select('b.*, l.title AS listing_title, l.type AS listing_type, l.address AS listing_address,
                      l.start_date AS listing_start_date, l.end_date AS listing_end_date, 
                      l.class_time AS listing_class_time, l.class_end_time AS listing_class_end_time')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->where('b.booking_status', 'confirmed')
            ->where('b.payment_status', 'paid')
            ->groupStart()
                ->where('b.class_date >=', date('Y-m-d'))
                ->orWhere('l.end_date >=', date('Y-m-d'))
            ->groupEnd();

        if ($parentId) {
            $builder->where('b.parent_id', $parentId);
        } elseif ($phone) {
            $builder->where('b.parent_phone', $phone);
        } else {
            return [];
        }

        return $builder->orderBy('b.class_date', 'ASC')
            ->orderBy('b.class_time', 'ASC')
            ->get()
            ->getResultObject();
    }

    /**
     * Completed bookings for a parent (for activity history + review prompts).
     */
    public function getCompleted(?int $parentId = null, ?string $phone = null): array
    {
        $identityClause = $parentId 
            ? "r.user_id = " . (int)$parentId 
            : "r.parent_phone = " . $this->db->escape($phone);

        $builder = $this->db->table('bookings b')
            ->select('b.*, l.title AS listing_title, l.category_id, cat.name AS category_name,
                      l.address AS listing_address, l.start_date AS listing_start_date, l.end_date AS listing_end_date, 
                      l.class_time AS listing_class_time, l.class_end_time AS listing_class_end_time,
                      (SELECT COUNT(r.id) FROM reviews r WHERE r.listing_id = l.id AND ' . $identityClause . ') AS has_reviewed')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->join('categories cat', 'cat.id = l.category_id', 'left')
            ->where('b.booking_status', 'completed');

        if ($parentId) {
            $builder->where('b.parent_id', $parentId);
        } elseif ($phone) {
            $builder->where('b.parent_phone', $phone);
        } else {
            return [];
        }

        return $builder->orderBy('b.completed_at', 'DESC')
            ->get()
            ->getResultObject();
    }

    // ── Provider-facing queries ─────────────────────────────────

    /**
     * All bookings for a given listing (provider view).
     */
    public function getByListing(int $listingId): array
    {
        return $this->where('listing_id', $listingId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * All bookings for a given provider (across all their listings).
     */
    public function getByProvider(int $providerId): array
    {
        return $this->db->table('bookings b')
            ->select('b.*, l.title AS listing_title, l.type AS listing_type, l.address AS listing_address')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->where('l.provider_id', $providerId)
            ->orderBy('b.created_at', 'DESC')
            ->get()
            ->getResultObject();
    }

    public function countSlotBookings(int $listingId, string $date, string $time): int
    {
        return $this->where('listing_id',     $listingId)
                    ->where('class_date',     $date)
                    ->where('class_time',     $time)
                    ->where('booking_status', 'confirmed')
                    ->where('payment_status', 'paid')
                    ->countAllResults();
    }

    /**
     * Get booking counts for each batch of a listing.
     * Returns array mapping batch_index => count.
     */
    public function getBatchBookingCounts(int $listingId): array
    {
        $rows = $this->db->table('bookings')
            ->select('batch_id, COUNT(*) as total')
            ->where('listing_id', $listingId)
            ->where('booking_status', 'confirmed')
            ->where('payment_status', 'paid')
            ->where('batch_id IS NOT NULL')
            ->groupBy('batch_id')
            ->get()
            ->getResultArray();
            
        $counts = [];
        foreach ($rows as $row) {
            if (isset($row['batch_id'])) {
                $counts[(int)$row['batch_id']] = (int)$row['total'];
            }
        }
        return $counts;
    }

    // ── Admin queries ───────────────────────────────────────────

    /**
     * All bookings with listing + parent info (admin dashboard).
     */
    public function getAllWithDetails(int $limit = 50, int $offset = 0): array
    {
        return $this->db->table('bookings b')
            ->select('b.*, l.title AS listing_title, u.name AS parent_name, u.phone AS parent_phone')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->join('users u',    'u.id = b.parent_id',  'left')
            ->orderBy('b.created_at', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultObject();
    }

    /**
     * Mark a booking as completed and set completed_at timestamp.
     */
    public function markCompleted(int $id): void
    {
        $this->updateById($id, [
            'booking_status' => 'completed',
            'completed_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    // ── Booking flow ────────────────────────────────────────────

    /**
     * Insert a new pending booking. parent_id = 0 for guest/phone-only bookings.
     * Returns the new booking ID.
     */
    public function createBooking(array $data): int
    {
        $db = \Config\Database::connect();
        $payload = [
            'listing_id'     => (int)$data['listing_id'],
            'parent_id'      => (int)($data['parent_id'] ?? 0),
            'parent_phone'   => $data['phone'] ?? ($data['parent_phone'] ?? null),
            'parent_email'   => $data['parent_email'] ?? ($data['email'] ?? null),
            'student_name'   => $data['student_name'],
            'student_age'    => (isset($data['student_age']) && $data['student_age'] !== '')
                                    ? (int)$data['student_age'] : null,
            'booking_type'   => $data['booking_type'] ?? 'regular',
            'batch_name'     => $data['batch_name'] ?? null,
            'batch_id'       => isset($data['batch_id']) ? (int)$data['batch_id'] : null,
            'batch_price'    => isset($data['batch_price']) ? (float)$data['batch_price'] : null,
            'batch_start_date' => $data['batch_start_date'] ?? null,
            'class_date'     => $data['class_date'] ?? null,
            'class_time'     => $data['class_time'] ?? null,
            'payment_amount' => (float)($data['payment_amount'] ?? 0),
            'payment_id'     => null,
            'payment_status' => 'pending',
            'booking_status' => 'confirmed',
            'reminder_sent'  => 0,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ];

        if (!$db->table('bookings')->insert($payload)) {
            $err = $db->error();
            log_message('error', '[BookingModel] Insert failed: ' . ($err['message'] ?? 'Unknown error'));
            return 0;
        }

        return (int)$db->insertID();
    }

    /**
     * Confirm payment after Razorpay verification.
     * Sets payment_id + payment_status = 'paid'.
     */
    public function confirmPayment(int $bookingId, string $razorpayPaymentId): bool
    {
        return (bool) $this->db->table('bookings')
            ->where('id', $bookingId)
            ->update([
                'payment_id'     => $razorpayPaymentId,
                'payment_status' => 'paid',
                'booking_status' => 'confirmed',
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Fetch a booking with listing/provider/category details joined (used for
     * confirmation page + WhatsApp notification).
     */
    public function getWithDetails(int $bookingId): ?array
    {
        $row = $this->db->table('bookings b')
            ->select('b.*, l.title AS listing_title, l.address AS listing_address,
                      l.latitude, l.longitude, l.type AS listing_type,
                      l.start_date AS listing_start_date, l.end_date AS listing_end_date,
                      l.class_time AS listing_class_time, l.class_end_time AS listing_class_end_time,
                      u.name AS provider_name, u.phone AS provider_phone,
                      c.name AS category_name')
            ->join('listings l',   'l.id = b.listing_id',  'left')
            ->join('users u',      'u.id = l.provider_id', 'left')
            ->join('categories c', 'c.id = l.category_id', 'left')
            ->where('b.id', $bookingId)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    /**
     * Record a Razorpay transaction after successful payment.
     */
    public function recordTransaction(
        int    $bookingId,
        int    $userId,
        float  $amount,
        string $razorpayId,
        string $status = 'success'
    ): void {
        $this->db->table('transactions')->insert([
            'booking_id'       => $bookingId,
            'user_id'          => max(1, $userId), // FK requires > 0
            'amount'           => $amount,
            'transaction_type' => 'payment',
            'razorpay_id'      => $razorpayId,
            'status'           => $status,
            'settled_at'       => $status === 'success' ? date('Y-m-d H:i:s') : null,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Check if a parent/user is enrolled (has a confirmed booking) in a listing.
     */
    public function checkEnrolment(int $listingId, ?int $parentId = null, ?string $phone = null): ?object
    {
        $builder = $this->where('listing_id', $listingId)
                        ->groupStart()
                            ->where('booking_status', 'confirmed')
                            ->orWhere('booking_status', 'completed')
                        ->groupEnd();

        if ($parentId) {
            $builder->where('parent_id', $parentId);
        } elseif ($phone) {
            $builder->where('parent_phone', $phone);
        } else {
            return null;
        }

        return $builder->first();
    }
}
