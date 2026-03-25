<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * TransactionModel
 * Table: transactions
 * Tracks all Razorpay payment events and provider payouts.
 */
class TransactionModel extends BaseModel
{
    protected $table      = 'transactions';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'booking_id', 'user_id', 'amount', 'transaction_type',
        'razorpay_id', 'status', 'settled_at', 'is_blocked', 'block_reason', 'transfer_id'
    ];

    // ── Query helpers ───────────────────────────────────────────

    /**
     * All transactions for a booking.
     */
    public function getByBooking(int $bookingId): array
    {
        return $this->where('booking_id', $bookingId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * All transactions for a user (parent: payments; provider: payouts).
     */
    public function getByUser(int $userId, ?string $type = null): array
    {
        $q = $this->where('user_id', $userId);
        if ($type !== null) {
            $q->where('transaction_type', $type);
        }
        return $q->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Successful payments pending provider payout (settled_at IS NULL).
     * Used by admin payout processing job.
     */
    public function getPendingPayouts(): array
    {
        return $this->db->table('transactions t')
            ->select('t.*, b.listing_id, l.provider_id, u.name AS provider_name, u.phone AS provider_phone')
            ->join('bookings b',  'b.id = t.booking_id',   'left')
            ->join('listings l',  'l.id = b.listing_id',   'left')
            ->join('users u',     'u.id = l.provider_id',  'left')
            ->where('t.transaction_type', 'payment')
            ->where('t.status',           'success')
            ->where('t.settled_at',       null)
            ->orderBy('t.created_at', 'ASC')
            ->get()
            ->getResultObject();
    }

    /**
     * Mark a transaction as settled (payout complete).
     */
    public function markSettled(int $id): void
    {
        $this->updateById($id, [
            'settled_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Find transaction by Razorpay ID (for webhook verification).
     */
    public function findByRazorpayId(string $razorpayId): ?object
    {
        return $this->where('razorpay_id', $razorpayId)->first();
    }
}
