<?php

namespace App\Controllers;

use App\Controllers\AdminBaseController;

class Dashboard extends AdminBaseController
{

    /**
     * API: Individual count endpoints for Admin
     */
    public function apiUserCount() {
        $db = \Config\Database::connect();
        return $this->response->setJSON(['success' => true, 'count' => $db->table('users')->countAllResults()]);
    }

    public function apiProviderCount() {
        $db = \Config\Database::connect();
        return $this->response->setJSON(['success' => true, 'count' => $db->table('users')->where('role', 2)->countAllResults()]);
    }

    public function apiBookingCount() {
        $db = \Config\Database::connect();
        return $this->response->setJSON(['success' => true, 'count' => $db->table('bookings')->where('booking_status', 'confirmed')->countAllResults()]);
    }

    public function apiRevenueTotal() {
        $db = \Config\Database::connect();
        $total = $db->table('bookings')->where('payment_status', 'paid')->selectSum('payment_amount')->get()->getRow()->payment_amount ?? 0;
        return $this->response->setJSON(['success' => true, 'total' => (float)$total]);
    }


    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Core Summary Metrics
        $stats = [
            'total_listings' => $db->table('listings')->countAllResults(),
            'total_bookings' => $db->table('bookings')->where('booking_status', 'confirmed')->countAllResults(),
            'total_revenue'  => $db->table('bookings')->where('payment_status', 'paid')->selectSum('payment_amount')->get()->getRow()->payment_amount ?? 0,
            'total_parents'  => $db->table('users')->where('role', 3)->countAllResults(),
            'total_providers'=> $db->table('users')->where('role', 2)->countAllResults(),
            // New metrics
            'pending_kyc'    => $db->table('users')->whereIn('role', [2, 3])->groupStart()->where('provider_verification_status', 'pending')->orWhere('provider_verification_status', 'submit')->groupEnd()->countAllResults(),
            'pending_listings'=> $db->table('listings')->where('review_status', 'pending')->countAllResults(),
        ];

        // 2. Growth Calculation (This month vs Last month)
        $thisMonth = date('Y-m-01');
        
        $stats['revenue_this_month'] = $db->table('bookings')
            ->where('payment_status', 'paid')
            ->where('created_at >=', $thisMonth)
            ->selectSum('payment_amount')->get()->getRow()->payment_amount ?? 0;
            
        $stats['users_this_month'] = $db->table('users')
            ->where('created_at >=', $thisMonth)
            ->countAllResults();

        // 3. Category Distribution
        $categoryCounts = $db->table('listings l')
            ->select('c.name, COUNT(l.id) as count')
            ->join('categories c', 'c.id = l.category_id')
            ->groupBy('l.category_id')
            ->orderBy('count', 'DESC')
            ->get()->getResultArray();

        // 4. Booking Trend (Last 30 Days)
        $trendData = $db->table('bookings')
            ->select("DATE(created_at) as date, COUNT(id) as count")
            ->where('booking_status', 'confirmed')
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()->getResultArray();

        // 5. Listing Type Distribution
        $typeStats = $db->table('listings')
            ->select('type, COUNT(id) as count')
            ->groupBy('type')
            ->get()->getResultArray();

        // 6. Alert System
        $now = date('Y-m-d H:i:s');
        $alerts = [];
        
        // 6.1 Pending KYC > 48 hours
        $kycAlerts = $db->table('users')
            ->whereIn('provider_verification_status', ['pending', 'submit'])
            ->where('updated_at <=', date('Y-m-d H:i:s', strtotime('-48 hours')))
            ->countAllResults();
        if ($kycAlerts > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-user-shield',
                'title' => "$kycAlerts KYC applications",
                'desc' => 'Pending for over 48 hours',
                'link' => url('admin/verifications')
            ];
        }

        // 6.2 Pending Settlements > 3 days
        $settlementAlerts = $db->table('settlements')
            ->where('status', 'pending')
            ->where('created_at <=', date('Y-m-d H:i:s', strtotime('-3 days')))
            ->countAllResults();
        if ($settlementAlerts > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-money-bill-wave',
                'title' => "$settlementAlerts Settlements",
                'desc' => 'Overdue by 3+ days',
                'link' => url('admin/settlements')
            ];
        }

        // 6.3 Unresolved Concerns > 72 hours
        $concernAlerts = $db->table('feedbacks')
            ->where('status', 'new')
            ->where('created_at <=', date('Y-m-d H:i:s', strtotime('-72 hours')))
            ->countAllResults();
        if ($concernAlerts > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-envelope-open-text',
                'title' => "$concernAlerts Support Tickets",
                'desc' => 'Unresolved for more than 72 hours',
                'link' => url('admin/feedback') // Assuming this route exists or is coming
            ];
        }

        // 7. Latest Activity
        $latestBookings = $db->table('bookings b')
            ->select('b.*, l.title as listing_title, u.name as parent_name')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->join('users u', 'u.id = b.parent_id', 'left')
            ->orderBy('b.created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $latestUsers = $db->table('users')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return view('admin/dashboard', [
            'stats'          => (object) $stats,
            'categoryCounts' => $categoryCounts,
            'trendData'      => $trendData,
            'typeStats'      => $typeStats,
            'latestBookings' => $latestBookings,
            'latestUsers'    => $latestUsers,
            'alerts'         => $alerts,
            'title'          => 'Admin Dashboard',
            '_page'          => (object)[
                'title' => 'Admin Dashboard',
                'menu'  => 'dashboard',
                'submenu' => ''
            ]
        ]);
    }

    /**
     * API: Get Dashboard Stats strictly via JSON
     * GET /admin/api/stats
     */
    public function apiStats()
    {
        $db = \Config\Database::connect();
        $stats = [
            'bookings'  => $db->table('bookings')->where('booking_status', 'confirmed')->countAllResults(),
            'revenue'   => (float)$db->table('bookings')->where('payment_status', 'paid')->selectSum('payment_amount')->get()->getRow()->payment_amount ?? 0,
            'users'     => $db->table('users')->countAllResults(),
            'providers' => $db->table('users')->where('role', 2)->countAllResults(),
        ];

        return $this->response->setJSON([
            'success' => true,
            'data'    => $stats
        ]);
    }
}
