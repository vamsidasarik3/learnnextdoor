<?php

namespace App\Models;

use CodeIgniter\Model;

class SettlementModel extends Model
{
    protected $table            = 'settlements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'provider_id', 'listing_id', 'period_start', 'period_end', 'payout_date', 
        'gross_amount', 'commission_amount', 'refund_deductions', 'net_amount', 
        'upi_id', 'status', 'retry_count', 'student_breakdown', 'batch_aggregation', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get Pending Settlements
     */
    public function getPending()
    {
        return $this->db->table('settlements s')
            ->select('s.*, u.name as provider_name, l.title as listing_title')
            ->join('users u', 'u.id = s.provider_id', 'left')
            ->join('listings l', 'l.id = s.listing_id', 'left')
            ->where('s.status', 'pending')
            ->where('s.payout_date <=', date('Y-m-d'))
            ->orderBy('s.payout_date', 'ASC')
            ->get()->getResultObject();
    }

    /**
     * Get Completed Settlements
     */
    public function getCompleted()
    {
        return $this->db->table('settlements s')
            ->select('s.*, u.name as provider_name, l.title as listing_title')
            ->join('users u', 'u.id = s.provider_id', 'left')
            ->join('listings l', 'l.id = s.listing_id', 'left')
            ->where('s.status', 'completed')
            ->orderBy('s.updated_at', 'DESC')
            ->get()->getResultObject();
    }

    /**
     * Get Future Settlements
     */
    public function getFuture()
    {
        return $this->db->table('settlements s')
            ->select('s.*, u.name as provider_name, l.title as listing_title')
            ->join('users u', 'u.id = s.provider_id', 'left')
            ->join('listings l', 'l.id = s.listing_id', 'left')
            ->where('s.status', 'pending')
            ->where('s.payout_date >', date('Y-m-d'))
            ->orderBy('s.payout_date', 'ASC')
            ->get()->getResultObject();
    }

    /**
     * Get Current Settlement Period Info
     * Bi-monthly: 1st-15th (Payout 16th), 16th-End (Payout 1st)
     */
    public function getPeriodInfo()
    {
        $day = (int)date('d');
        $month = date('m');
        $year = date('Y');

        if ($day <= 15) {
            // First half of the month
            $start = "$year-$month-01";
            $end   = "$year-$month-15";
            $payout = "$year-$month-16";
        } else {
            // Second half of the month
            $start = "$year-$month-16";
            $lastDay = date('t', strtotime("$year-$month-01"));
            $end   = "$year-$month-$lastDay";
            
            // Payout is 1st of next month
            $nextMonth = date('m', strtotime("+1 month", strtotime("$year-$month-01")));
            $nextYear = date('Y', strtotime("+1 month", strtotime("$year-$month-01")));
            $payout = "$nextYear-$nextMonth-01";
        }

        return [
            'start'  => $start,
            'end'    => $end,
            'payout' => $payout
        ];
    }
}
