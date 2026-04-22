<?php

namespace App\Models;

use App\Models\BaseModel;

class RefundModel extends BaseModel
{
    protected $table      = 'refund_queue';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'booking_id', 'amount', 'status', 'refund_type', 'reason', 
        'razorpay_refund_id', 'transaction_reference', 'error_message', 
        'admin_id', 'processed_at'
    ];

    /**
     * Get Pending Refunds
     */
    public function getPending()
    {
        return $this->db->table('refund_queue r')
            ->select('r.*, b.student_name, b.payment_amount, l.title as listing_title, u.name as parent_name')
            ->join('bookings b', 'b.id = r.booking_id', 'left')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->join('users u',    'u.id = b.parent_id',  'left')
            ->where('r.status', 'pending')
            ->orderBy('r.created_at', 'DESC')
            ->get()->getResultObject();
    }

    /**
     * Get Processed Refunds
     */
    public function getCompleted()
    {
        return $this->db->table('refund_queue r')
            ->select('r.*, b.student_name, l.title as listing_title, u.name as parent_name')
            ->join('bookings b', 'b.id = r.booking_id', 'left')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->join('users u',    'u.id = b.parent_id',  'left')
            ->where('r.status', 'processed')
            ->orderBy('r.processed_at', 'DESC')
            ->get()->getResultObject();
    }
}
