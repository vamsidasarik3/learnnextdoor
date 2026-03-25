<?php

namespace App\Services;

use App\Models\TransactionModel;
use App\Models\BookingModel;
use App\Models\ListingModel;
use App\Models\UserModel;
use Razorpay\Api\Api;
use Config\Database;

class SettlementService
{
    protected $db;
    protected $transactionModel;
    protected $razorpay;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->transactionModel = new TransactionModel();
        
        helper('basic_helper');

        // Load Razorpay from Settings
        $key    = setting('razorpay_key');
        $secret = setting('razorpay_secret');
        if ($key && $secret) {
            $this->razorpay = new Api($key, $secret);
        }
    }

    /**
     * Main entry point for scheduled job.
     */
    public function processAutoSettlements()
    {
        $today = date('Y-m-d');
        $isMonthEnd = (date('Y-m-d', strtotime('last day of this month')) === $today);

        // 1. Fetch all successful payments that are NOT yet settled and NOT blocked
        $builder = $this->db->table('transactions t')
            ->select('t.*, b.class_date, l.type as listing_type, l.provider_id, u.razorpay_account_id')
            ->join('bookings b', 'b.id = t.booking_id')
            ->join('listings l', 'l.id = b.listing_id')
            ->join('users u',    'u.id = l.provider_id')
            ->where('t.transaction_type', 'payment')
            ->where('t.status',           'success')
            ->where('t.settled_at',       null)
            ->where('t.is_blocked',       0);

        $results = $builder->get()->getResult();

        foreach ($results as $row) {
            $shouldSettle = false;

            switch ($row->listing_type) {
                case 'course':
                    // First day settlement
                    if ($row->class_date <= $today) {
                        $shouldSettle = true;
                    }
                    break;
                case 'workshop':
                    // End day settlement (for now assuming class_date is the event day)
                    if ($row->class_date <= $today) {
                        $shouldSettle = true;
                    }
                    break;
                case 'regular':
                    // Month-end settlement
                    if ($isMonthEnd) {
                        $shouldSettle = true;
                    }
                    break;
            }

            if ($shouldSettle) {
                $this->executeSettlement($row);
            }
        }
    }

    /**
     * Performs the actual transfer via Razorpay Route.
     */
    protected function executeSettlement($row)
    {
        if (!$this->razorpay) {
            log_message('error', 'Settlement failed: Razorpay API not initialized.');
            return;
        }

        if (empty($row->razorpay_account_id)) {
            log_message('error', "Settlement failed for Txn {$row->id}: Provider has no Razorpay Account ID.");
            return;
        }

        try {
            // Calculate amount to transfer (Platform Fee logic)
            // For now, let's assume 10% platform fee if not specified
            $platformFeePercent = (float)setting('platform_fee_percent', 10);
            $totalAmount        = (float)$row->amount;
            $transferAmount     = $totalAmount * (1 - ($platformFeePercent / 100));
            
            // Razorpay wants amounts in Paise
            $payoutAmountPaise = round($transferAmount * 100);

            // Create Razorpay Transfer
            $transfer = $this->razorpay->transfer->create([
                'account' => $row->razorpay_account_id,
                'amount'  => $payoutAmountPaise,
                'currency' => 'INR',
                'notes'    => [
                    'txn_id'     => $row->id,
                    'booking_id' => $row->booking_id
                ]
            ]);

            if ($transfer && isset($transfer->id)) {
                // Update original transaction
                $this->transactionModel->update($row->id, [
                    'settled_at'  => date('Y-m-d H:i:s'),
                    'transfer_id' => $transfer->id
                ]);

                // Create a payout record in transactions
                $this->transactionModel->insert([
                    'booking_id'       => $row->booking_id,
                    'user_id'          => $row->provider_id,
                    'amount'           => $transferAmount,
                    'transaction_type' => 'payout',
                    'razorpay_id'      => $transfer->id,
                    'status'           => 'success',
                    'settled_at'       => date('Y-m-d H:i:s')
                ]);

                log_message('info', "Settlement successful for Txn {$row->id}: Transfer {$transfer->id}");
            }

        } catch (\Exception $e) {
            log_message('error', "Settlement exception for Txn {$row->id}: " . $e->getMessage());
        }
    }
}
