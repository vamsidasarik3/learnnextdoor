<?php

namespace App\Controllers;

use App\Controllers\AdminBaseController;

class Dashboard extends AdminBaseController
{

    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Summary Metrics
        $stats = [
            'total_listings' => $db->table('listings')->countAllResults(),
            'total_bookings' => $db->table('bookings')->where('payment_status', 'paid')->countAllResults(),
            'total_revenue'  => $db->table('bookings')->where('payment_status', 'paid')->selectSum('payment_amount')->get()->getRow()->payment_amount ?? 0,
            'total_parents'  => $db->table('users')->where('role', 3)->countAllResults(),
            'total_providers'=> $db->table('users')->where('role', 2)->countAllResults(),
        ];

        // 2. Category Distribution (Listings count per category)
        $categoryCounts = $db->table('listings l')
            ->select('c.name, COUNT(l.id) as count')
            ->join('categories c', 'c.id = l.category_id')
            ->groupBy('l.category_id')
            ->get()->getResultArray();

        // 3. Booking Trend (Last 30 Days)
        $trendData = $db->table('bookings')
            ->select("DATE(created_at) as date, COUNT(id) as count")
            ->where('payment_status', 'paid')
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()->getResultArray();

        // 4. Listing Type Distribution
        $typeStats = $db->table('listings')
            ->select('type, COUNT(id) as count')
            ->groupBy('type')
            ->get()->getResultArray();

        return view('admin/dashboard', [
            'stats'          => (object) $stats,
            'categoryCounts' => $categoryCounts,
            'trendData'      => $trendData,
            'typeStats'      => $typeStats,
            'title'          => 'Admin Dashboard'
        ]);
    }
}
