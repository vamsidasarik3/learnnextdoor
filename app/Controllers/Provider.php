<?php

namespace App\Controllers;

use App\Models\ListingModel;
use DateTime;
use DateInterval;
use DatePeriod;

class Provider extends BaseController
{
    /**
     * Provider Dashboard — MAIN HUB
     * Redirected here after role change or landing.
     */
    public function dashboard()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();

        $dbUser = $userModel->getById($userId);
        
        // Sync session with DB to ensure navbar and role-based logic are current
        $cndUser = session()->get('cnd_user');
        if ($cndUser && $dbUser) {
            $cndUser['role'] = $dbUser->role;
            $cndUser['provider_verification_status'] = $dbUser->provider_verification_status;
            $cndUser['provider_verification_message'] = $dbUser->provider_verification_message;
            session()->set('cnd_user', $cndUser);
            session()->set('user_role', $dbUser->role);
        }

        // Default to Provider mode when hitting the dashboard
        session()->set('cnd_provider_mode', true);

        // ── Stats Calculation ──────────────────────────────────────────
        $listingModel = new ListingModel();
        $bookingModel = new \App\Models\BookingModel();
        $settlementModel = new \App\Models\SettlementModel();

        // 1. Active Listings (Published & Approved)
        $activeListingsCount = $listingModel->where('provider_id', $userId)
                                            ->where('status', 'active')
                                            ->where('review_status', 'approved')
                                            ->countAllResults();

        // 2. Total Enrollments (MTD) - CONFIRMED/COMPLETED in current calendar month
        $mtdEnrollmentsCount = $bookingModel->db->table('bookings b')
            ->join('listings l', 'l.id = b.listing_id')
            ->where('l.provider_id', $userId)
            ->where('b.payment_status', 'paid')
            ->where('b.created_at >=', date('Y-m-01 00:00:00'))
            ->countAllResults();

        // 3. Upcoming Settlement (Sum of pending settlements)
        $upcomingSettlement = $settlementModel->where('provider_id', $userId)
                                              ->where('status', 'pending')
                                              ->selectSum('net_amount')
                                              ->get()
                                              ->getRow();
        $settlementAmount = $upcomingSettlement->net_amount ?? 0;

        // 4. Pending Actions (Actions requiring provider attention)
        $pendingActionsCount = 0;
        if (!$dbUser->phone_verified) $pendingActionsCount++;
        if (!$dbUser->email_verified) $pendingActionsCount++;
        if ($dbUser->provider_verification_status === 'rejected') $pendingActionsCount++;
        
        $rejectedListings = $listingModel->where('provider_id', $userId)
                                         ->where('review_status', 'rejected')
                                         ->countAllResults();
        $pendingActionsCount += $rejectedListings;

        // 5. Today's Schedule (Active listings with sessions today)
        $todayRaw = $listingModel->where('provider_id', $userId)
                                 ->where('status', 'active')
                                 ->findAll();
        
        $scheduleToday = [];
        $todayDate = date('Y-m-d');
        $todayDay = date('D'); // Mon, Tue, etc.
        $dayMap = ['Sun'=>'S', 'Mon'=>'M', 'Tue'=>'T', 'Wed'=>'W', 'Thu'=>'Th', 'Fri'=>'F', 'Sat'=>'Sa'];
        $currentDayKey = $dayMap[$todayDay] ?? '';

        foreach ($todayRaw as $l) {
            $isToday = false;
            $displayTime = $l->class_time . ' - ' . ($l->class_end_time ?: 'N/A');

            if ($l->type === 'regular' && !empty($l->batches)) {
                $batches = json_decode($l->batches, true) ?: [];
                foreach ($batches as $batch) {
                    $days = $batch['days'] ?? [];
                    if (in_array($currentDayKey, $days)) {
                        $isToday = true;
                        $displayTime = ($batch['from_time'] ?? '') . ' - ' . ($batch['to_time'] ?? '');
                        break;
                    }
                }
            } elseif ($l->start_date === $todayDate) {
                $isToday = true;
            }

            if ($isToday) {
                $studentsCount = $bookingModel->where('listing_id', $l->id)
                    ->whereIn('payment_status', ['paid', 'confirmed', 'success'])
                    ->where('booking_status', 'confirmed')
                    ->countAllResults();

                $scheduleToday[] = (object)[
                    'title'    => $l->title,
                    'time'     => $displayTime,
                    'students' => $studentsCount
                ];
            }
        }

        return view('frontend/provider/dashboard', [
            'page_title'        => 'Provider Dashboard | Class Next Door',
            'user'              => $dbUser,
            'show_location_bar' => false,
            'scheduleToday'     => $scheduleToday,
            'stats'             => (object)[
                'active_listings'    => $activeListingsCount,
                'mtd_enrollments'    => $mtdEnrollmentsCount,
                'upcoming_settlement' => $settlementAmount,
                'pending_actions'    => $pendingActionsCount,
                'classes_today'      => count($scheduleToday)
            ]
        ]);
    }

    /**
     * Provider Support Portal
     */
    public function support()
    {
        $providerId      = logged('id');
        $db              = \Config\Database::connect();
        
        // Ensure table exists (fallback for missing schema)
        if (!$db->tableExists('provider_concerns')) {
            $sql = "CREATE TABLE IF NOT EXISTS `provider_concerns` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `provider_id` int(11) NOT NULL,
              `issue` text NOT NULL,
              `status` varchar(20) NOT NULL DEFAULT 'open',
              `resolution_note` text DEFAULT NULL,
              `resolved_at` datetime DEFAULT NULL,
              `created_at` datetime DEFAULT NULL,
              `updated_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $db->query($sql);
        }

        $concernModel    = new \App\Models\ConcernModel();
        $recentConcerns  = $concernModel->where('provider_id', $providerId)
                                        ->orderBy('created_at', 'DESC')
                                        ->limit(10)
                                        ->findAll();

        $listings = model('App\Models\ListingModel')->where('provider_id', $providerId)->orderBy('title', 'ASC')->findAll();

        return view('frontend/provider/support', [
            'page_title'     => 'Support & Help | Class Next Door',
            'recentConcerns' => $recentConcerns,
            'listings'       => $listings,
            'user'           => model('App\Models\UserModel')->getById($providerId)
        ]);
    }

    public function viewTicket($id)
    {
        $providerId   = logged('id');
        $db = \Config\Database::connect();
        
        // ── Ensure Extended Schema (On Demand) ──
        if (!$db->fieldExists('category', 'provider_concerns')) {
            $db->query("ALTER TABLE `provider_concerns` ADD COLUMN `category` varchar(100) DEFAULT NULL AFTER `provider_id`;");
            $db->query("ALTER TABLE `provider_concerns` ADD COLUMN `priority` varchar(20) DEFAULT 'Medium' AFTER `status`;");
            $db->query("ALTER TABLE `provider_concerns` ADD COLUMN `listing_id` int(11) DEFAULT NULL AFTER `category`;");
        }
        if (!$db->tableExists('support_messages')) {
            $db->query("CREATE TABLE `support_messages` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `concern_id` int(11) NOT NULL,
              `sender_id` int(11) NOT NULL,
              `role` varchar(20) NOT NULL,
              `message` text NOT NULL,
              `created_at` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        }

        $concernModel = new \App\Models\ConcernModel();
        $ticket       = $concernModel->where('provider_id', $providerId)->find($id);

        if (!$ticket) {
            return redirect()->to('provider/support')->with('error', 'Ticket not found');
        }

        $msgModel = new \App\Models\SupportMessageModel();
        $messages = $msgModel->getByConcern($id);

        return view('frontend/provider/support_view', [
            'page_title' => "Ticket REQ-" . str_pad($id, 4, '0', STR_PAD_LEFT),
            'ticket'     => $ticket,
            'messages'   => $messages,
            'user'       => model('App\Models\UserModel')->getById($providerId)
        ]);
    }

    public function replyTicket()
    {
        $providerId = logged('id');
        $ticketId   = $this->request->getPost('ticket_id');
        $message    = $this->request->getPost('message');

        if (!$providerId || !$ticketId || empty(trim($message))) {
            return redirect()->back()->with('error', 'Invalid message.');
        }

        $msgModel = new \App\Models\SupportMessageModel();
        $msgModel->insert([
            'concern_id' => $ticketId,
            'sender_id'  => $providerId,
            'role'       => 'provider',
            'message'    => $message
        ]);

        return redirect()->back()->with('success', 'Message sent.');
    }

    /**
     * Toggle between Provider Mode and User Mode
     */
    public function toggleMode()
    {
        $session = session();
        $currentMode = $session->get('cnd_provider_mode') ?? true;
        $newMode = !$currentMode;
        $session->set('cnd_provider_mode', $newMode);

        if ($newMode) {
            return redirect()->to('/provider/dashboard')->with('notifySuccess', 'Switched to Provider Mode');
        } else {
            return redirect()->to('/')->with('notifySuccess', 'Switched to User Mode (Browsing)');
        }
    }

    /**
     * Provider Listings Page — Subtask 2.1
     * Shows a list of classes published by the logged-in provider.
     */
    public function listings()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();

        return view('frontend/provider/listings', [
            'page_title'        => 'My Listings | Class Next Door',
            'user'              => $userModel->getById($userId),
            'show_location_bar' => false, // Provider pages don't need location bar
        ]);
    }

    /**
     * AJAX API: Get Provider Listings
     * Fetches listings with status and student count for the logged-in user.
     */
    public function apiListings()
    {
        $providerId = logged('id');
        if (!$providerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        $model = new ListingModel();
        $bookingModel = new \App\Models\BookingModel();
        $listings = $model->getByProviderWithStats($providerId);

        $counts = [
            'total'    => count($listings),
            'regular'  => 0,
            'course'   => 0,
            'workshop' => 0,
        ];

        foreach ($listings as &$l) {
            $type = $l['type'] ?? 'regular';
            if (isset($counts[$type])) {
                $counts[$type]++;
            }
            if ($l['type'] === 'regular' && !empty($l['batches'])) {
                $l['batches'] = json_decode($l['batches'], true) ?? [];
                
                // Fetch students for this listing grouped by batch
                $enrolled = $bookingModel->where('listing_id', $l['id'])
                    ->whereIn('payment_status', ['paid', 'free', 'confirmed'])
                    ->where('booking_status', 'confirmed')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
                
                // Group students by batch_id (index)
                $batchStudents = [];
                foreach ($enrolled as $bk) {
                    $bId = $bk->batch_id; // Index in the JSON array
                    if ($bId !== null) {
                        $batchStudents[(int)$bId][] = $bk->student_name;
                    }
                }
                $l['batch_students'] = $batchStudents;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $listings,
            'counts'  => $counts
        ]);
    }

    /**
     * AJAX API: Get Subcategories by Category
     */
    public function getSubcategories()
    {
        $catId = $this->request->getGet('category_id');
        if (!$catId) {
            return $this->response->setJSON([]);
        }

        $model = new \App\Models\SubcategoryModel();
        $subs  = $model->getByCategory((int)$catId);

        return $this->response->setJSON($subs);
    }

    public function availability()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();

        return view('frontend/provider/availability', [
            'page_title'        => 'Declare Holidays / Cancel & Refund | Class Next Door',
            'user'              => $userModel->getById($userId),
            'show_location_bar' => false,
        ]);
    }

    /**
     * AJAX API: Get Listing Details (Schedule + Holidays)
     */
    public function getListingDetails($id)
    {
        $providerId = logged('id');
        if (!$providerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        $model = new ListingModel();
        $listing = $model->getWithCategory((int)$id);

        if (!$listing || $listing->provider_id != $providerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Listing not found']);
        }

        // Prepare schedule data
        $schedule = [];
        $totalSessions = 0;
        $startDate = new DateTime($listing->start_date);
        $endDate = new DateTime($listing->end_date);

        if ($listing->type === 'regular' || $listing->type === 'course') {
            $daysMap = ['S' => 0, 'M' => 1, 'T' => 2, 'W' => 3, 'Th' => 4, 'F' => 5, 'Sa' => 6];
            
            if ($listing->type === 'regular') {
                foreach ($listing->batches as $batch) {
                    $sActive = [
                        'name' => $batch['name'],
                        'days' => $batch['days'] ?? [],
                        'start_date' => $batch['batch_start_date'] ?? $listing->start_date
                    ];
                    $schedule[] = $sActive;
                }
                $totalSessions = 8; // Default for regular cycle
            } else {
                $priceBreakdown = json_decode($listing->price_breakdown ?: '[]', true);
                $courseDays = $priceBreakdown['days'] ?? [];
                $schedule[] = [
                    'name' => $listing->title,
                    'days' => $courseDays,
                    'start_date' => $listing->start_date,
                    'end_date' => $listing->end_date
                ];

                // Calculate total sessions for Course
                $interval = new \DateInterval('P1D');
                $period = new \DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
                $targetDays = array_map(fn($d) => $daysMap[$d], $courseDays);
                
                foreach ($period as $date) {
                    if (in_array((int)$date->format('w'), $targetDays)) {
                        $totalSessions++;
                    }
                }
                $endDate->modify('-1 day'); // Reset modify
            }
        } elseif ($listing->type === 'workshop') {
            $schedule[] = [
                'name' => $listing->title,
                'days' => [], 
                'date' => $listing->start_date
            ];
            $totalSessions = 1;
        }

        // Get booked students count
        $bookingModel = new \App\Models\BookingModel();
        $activeStudentsCount = $bookingModel->where('listing_id', $id)
            ->whereIn('payment_status', ['paid', 'confirmed', 'success'])
            ->where('booking_status', 'confirmed')
            ->countAllResults();

        return $this->response->setJSON([
            'success'   => true,
            'data'      => $listing,
            'schedule'  => $schedule,
            'holidays'  => json_decode($listing->holidays ?: '[]', true),
            'students'  => $activeStudentsCount,
            'total_sessions' => $totalSessions
        ]);
    }
    
    /**
     * AJAX API: Cancellation Preview Breakdown
     */
    public function cancellationPreview($id)
    {
        $providerId = logged('id');
        if (!$providerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        $model = new ListingModel();
        $listing = $model->getWithCategory((int)$id);

        if (!$listing || $listing->provider_id != $providerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Listing not found']);
        }

        $bookingModel = new \App\Models\BookingModel();
        $students = $bookingModel->where('listing_id', $id)
            ->whereIn('payment_status', ['paid', 'confirmed', 'success'])
            ->where('booking_status', 'confirmed')
            ->findAll();

        $breakdown = [];
        $totalRefund = 0;
        
        // Calculate sessions for Course if applicable
        $courseTotalSessions = 0;
        $courseRemainingSessions = 0;
        if ($listing->type === 'course') {
            $daysMap = ['S' => 0, 'M' => 1, 'T' => 2, 'W' => 3, 'Th' => 4, 'F' => 5, 'Sa' => 6];
            $priceBreakdown = json_decode($listing->price_breakdown ?: '[]', true);
            $targetDays = array_map(fn($d) => $daysMap[$d], $priceBreakdown['days'] ?? []);
            
            $period = new \DatePeriod(new \DateTime($listing->start_date), new \DateInterval('P1D'), (new \DateTime($listing->end_date))->modify('+1 day'));
            foreach ($period as $date) {
                if (in_array((int)$date->format('w'), $targetDays)) {
                    $courseTotalSessions++;
                    if ($date->format('Y-m-d') >= date('Y-m-d')) {
                        $courseRemainingSessions++;
                    }
                }
            }
        }

        foreach ($students as $s) {
            $isTrial = (stripos($s->batch_name, 'trial') !== false || $s->payment_amount == 0);
            $refundAmount = 0;

            if (!$isTrial) {
                if ($listing->type === 'workshop') {
                    $refundAmount = $s->payment_amount;
                } elseif ($listing->type === 'course') {
                    if ($courseTotalSessions > 0) {
                        $refundRatio = $courseRemainingSessions / $courseTotalSessions;
                        $refundAmount = round($s->payment_amount * $refundRatio, 2);
                    }
                } else {
                    // Regular class: Pro-rata based on remaining days in the cycle
                    if (!empty($s->enrollment_end_date)) {
                        $today = new DateTime();
                        $end = new DateTime($s->enrollment_end_date);
                        if ($end > $today) {
                            $diff = $today->diff($end)->days;
                            // Assume 30 day cycle for simplicity
                            $refundRatio = min(1, $diff / 30); 
                            $refundAmount = round($s->payment_amount * $refundRatio, 2);
                        }
                    } else {
                        // Fallback: 50% if no end date found
                        $refundAmount = round($s->payment_amount * 0.5, 2);
                    }
                }
            }

            $totalRefund += $refundAmount;

            $breakdown[] = [
                'id'            => $s->id,
                'student_name'  => $s->student_name,
                'amount_paid'   => $s->payment_amount,
                'refund_amount' => $refundAmount,
                'method'        => 'Razorpay',
                'eta'           => date('d M', strtotime('+5 days')) . ' - ' . date('d M', strtotime('+7 days'))
            ];
        }

        return $this->response->setJSON([
            'success'       => true,
            'listing_title' => $listing->title,
            'listing_type'  => $listing->type,
            'enrollments'   => count($students),
            'total_refund'  => $totalRefund,
            'total_paid'    => array_sum(array_column($breakdown, 'amount_paid')),
            'balance_to_provider' => array_sum(array_column($breakdown, 'amount_paid')) - $totalRefund,
            'breakdown'     => $breakdown
        ]);
    }

    /**
     * Enrollments Page
     */
    public function enrollments()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();
        $bookingModel = new \App\Models\BookingModel();

        $listingId = $this->request->getGet('listing_id');

        // Get all enrollments for provider's listings
        $query = $bookingModel->select('bookings.*, listings.title as listing_title, listings.type as listing_type')
            ->join('listings', 'listings.id = bookings.listing_id')
            ->where('listings.provider_id', $userId)
            ->where('bookings.booking_status', 'confirmed');

        if ($listingId) {
            $query->where('bookings.listing_id', $listingId);
        }

        $query->orderBy("CASE WHEN (bookings.enrollment_end_date IS NULL OR bookings.enrollment_end_date >= CURDATE()) THEN 0 ELSE 1 END", "ASC")
              ->orderBy('bookings.created_at', 'DESC');

        return view('frontend/provider/enrollments', [
            'page_title'        => 'Student Enrollments | Class Next Door',
            'user'              => $userModel->getById($userId),
            'enrollments'       => $query->findAll(),
            'show_location_bar' => false,
        ]);
    }

    /**
     * POST: End a Class / Course early
     */
    public function endClass()
    {
        $providerId = logged('id');
        $listingId  = $this->request->getPost('listing_id');
        $displayEndDate = $this->request->getPost('end_date'); 

        if (!$providerId || !$listingId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields.']);
        }

        $model = new ListingModel();
        $listing = $model->where('id', $listingId)->where('provider_id', $providerId)->first();

        if (!$listing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Listing not found.']);
        }

        $endDate = $displayEndDate ? date('Y-m-d', strtotime($displayEndDate)) : date('Y-m-d');

        // Update listing status
        $model->update($listingId, [
            'is_cancelled'     => 1,
            'cancellation_date'=> $endDate,
            'end_date'         => $endDate 
        ]);

        $bookingModel = new \App\Models\BookingModel();
        $refundModel = new \App\Models\RefundModel();
        
        $activeStudents = $bookingModel->where('listing_id', $listingId)
            ->whereIn('payment_status', ['paid', 'confirmed', 'success'])
            ->where('booking_status', 'confirmed')
            ->findAll();

        $notify = new \App\Services\NotificationService();
        $refundsCount = 0;

        // Pre-calculate session stats for Courses
        $courseTotalSessions = 0;
        $courseRemainingSessions = 0;
        if ($listing->type === 'course') {
            $daysMap = ['S' => 0, 'M' => 1, 'T' => 2, 'W' => 3, 'Th' => 4, 'F' => 5, 'Sa' => 6];
            $priceBreakdown = json_decode($listing->price_breakdown ?: '[]', true);
            $targetDays = array_map(fn($d) => $daysMap[$d], $priceBreakdown['days'] ?? []);
            
            $period = new \DatePeriod(new \DateTime($listing->start_date), new \DateInterval('P1D'), (new \DateTime($listing->end_date))->modify('+1 day'));
            foreach ($period as $date) {
                if (in_array((int)$date->format('w'), $targetDays)) {
                    $courseTotalSessions++;
                    // Remaining sessions from NOW or from END DATE? 
                    // Usually "Cancel Remaining" means from NOW.
                    if ($date->format('Y-m-d') >= date('Y-m-d')) {
                        $courseRemainingSessions++;
                    }
                }
            }
        }

        foreach ($activeStudents as $student) {
            $isTrial = (stripos($student->batch_name, 'trial') !== false || $student->payment_amount == 0);
            $refundAmount = 0;

            if (!$isTrial) {
                if ($listing->type === 'workshop') {
                    $refundAmount = $student->payment_amount;
                } elseif ($listing->type === 'course') {
                    if ($courseTotalSessions > 0) {
                        $refundRatio = $courseRemainingSessions / $courseTotalSessions;
                        $refundAmount = round($student->payment_amount * $refundRatio, 2);
                    }
                } else {
                    // Regular class: Pro-rata based on remaining days
                    if (!empty($student->enrollment_end_date)) {
                        $today = new DateTime($endDate);
                        $end = new DateTime($student->enrollment_end_date);
                        if ($end > $today) {
                            $diff = $today->diff($end)->days;
                            $refundRatio = min(1, $diff / 30); 
                            $refundAmount = round($student->payment_amount * $refundRatio, 2);
                        }
                    } else {
                        $refundAmount = round($student->payment_amount * 0.5, 2);
                    }
                }
            }

            // Mark booking as CANCELLED
            $bookingModel->update($student->id, [
                'booking_status' => 'cancelled', 
                'payment_status' => ($refundAmount > 0 ? 'refund_pending' : $student->payment_status),
                'enrollment_end_date' => $endDate
            ]);

            // Queue Refund if amount > 0
            if ($refundAmount > 0) {
                $refundModel->queueRefund($student->id, $refundAmount);
                $refundsCount++;
            }

            // WhatsApp Notification
            $notify->sendCancellationMessage($student->id, $listing->title, $refundAmount);
        }

        return $this->response->setJSON([
            'success'   => true,
            'message'   => "Cancellation processed successfully. $refundsCount refunds queued for processing.",
            'id'        => $listingId
        ]);
    }

    /**
     * AJAX API: Holiday Preview Breakdown
     */
    public function holidayPreview()
    {
        $providerId = logged('id');
        $json = $this->request->getJSON();
        $listingId = $json->listing_id;
        $dates = $json->dates;
        $batchNames = $json->batch_ids ?? [];
        $action = $json->action;

        $model = new ListingModel();
        $listing = $model->where('id', $listingId)->where('provider_id', $providerId)->first();
        if (!$listing) return $this->response->setJSON(['success' => false, 'message' => 'Listing not found']);

        $bookingModel = new \App\Models\BookingModel();
        $builder = $bookingModel->where('listing_id', $listingId)
            ->whereIn('payment_status', ['paid', 'confirmed', 'success'])
            ->where('booking_status', 'confirmed');
        
        if (!empty($batchNames)) {
            $builder->whereIn('batch_name', $batchNames);
        }
        $students = $builder->findAll();

        $preview = [];
        $totalImpact = 0;
        $holidayCount = count($dates);

        foreach ($students as $s) {
            $isTrial = (stripos($s->batch_name, 'trial') !== false || $s->payment_amount == 0);
            $impact = 0;
            
            if ($action === 'refund' && !$isTrial) {
                if ($listing->type === 'course') {
                    // (Holidays / Total Sessions) * Price
                    // We need total sessions calculated in getListingDetails or recalculate
                    $total = $this->calculateTotalSessions($listing);
                    $impact = round(($holidayCount / ($total ?: 1)) * $s->payment_amount, 2);
                } else {
                    // Regular: Monthly Fee / 26 * Holidays
                    $dailyRate = $s->payment_amount / WORKING_DAYS;
                    $impact = round($dailyRate * $holidayCount, 2);
                }
                $totalImpact += $impact;
            } else {
                // Extension: Sessions Cancelled (mapped to days)
                // For simplified preview, we say "X days" (where X is number of sessions cancelled)
                // For Courses, it can be a duration string
                $impact = $holidayCount . " session(s)";
            }

            $preview[] = [
                'name' => $s->student_name,
                'batch' => $s->batch_name,
                'is_trial' => $isTrial,
                'impact' => $impact
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'student_count' => count($students),
            'total_impact' => $totalImpact,
            'students' => $preview
        ]);
    }

    private function calculateTotalSessions($listing)
    {
        $daysMap = ['S' => 0, 'M' => 1, 'T' => 2, 'W' => 3, 'Th' => 4, 'F' => 5, 'Sa' => 6];
        $priceBreakdown = json_decode($listing->price_breakdown ?: '[]', true);
        $targetDays = array_map(fn($d) => $daysMap[$d], $priceBreakdown['days'] ?? []);
        $total = 0;
        $period = new \DatePeriod(new \DateTime($listing->start_date), new \DateInterval('P1D'), (new \DateTime($listing->end_date))->modify('+1 day'));
        foreach ($period as $date) {
            if (in_array((int)$date->format('w'), $targetDays)) $total++;
        }
        return $total;
    }

    /**
     * POST: Declare Holidays
     */
    public function declareHoliday()
    {
        $providerId = logged('id');
        $listingId  = $this->request->getPost('listing_id');
        $dateStr    = $this->request->getPost('holiday_dates'); 
        $action     = $this->request->getPost('action'); 
        $extension  = $this->request->getPost('extension'); 
        $batchIdStr = $this->request->getPost('batch_ids'); // Comma separated names?

        if (!$providerId || !$listingId || !$dateStr || !$action) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required fields.']);
        }

        $rawDates = explode(', ', $dateStr);
        $dates = array_map(fn($d) => date('Y-m-d', strtotime(trim($d))), $rawDates);
        $batchNames = $batchIdStr ? explode(',', $batchIdStr) : [];

        $model = new ListingModel();
        $listing = $model->where('id', $listingId)->where('provider_id', $providerId)->first();
        if (!$listing) return $this->response->setJSON(['success' => false, 'message' => 'Listing not found.']);

        $currentHolidays = json_decode($listing->holidays ?: '[]', true);
        $newHolidays = array_unique(array_merge($currentHolidays, $dates));
        $model->update($listingId, ['holidays' => json_encode($newHolidays)]);

        $bookingModel = new \App\Models\BookingModel();
        $builder = $bookingModel->where('listing_id', $listingId)
            ->whereIn('payment_status', ['paid', 'confirmed', 'success'])
            ->where('booking_status', 'confirmed');
        
        if (!empty($batchNames)) {
            $builder->whereIn('batch_name', $batchNames);
        }
        $activeStudents = $builder->findAll();

        $notify = new \App\Services\NotificationService();
        $displayDates = implode(', ', array_map(fn($d) => date('d M', strtotime($d)), $dates));
        $refundModel = new \App\Models\RefundModel();
        $refundsCount = 0; $extensionCount = 0;

        $courseTotalSessions = ($listing->type === 'course') ? $this->calculateTotalSessions($listing) : 0;

        foreach ($activeStudents as $student) {
            $isTrial = (stripos($student->batch_name, 'trial') !== false || $student->payment_amount == 0);
            $refundAmount = 0;
            $newEnd = $student->enrollment_end_date ?: ($listing->end_date ?: date('Y-m-d'));

            if ($action === 'refund' && !$isTrial) {
                // Formula: Monthly Fee / 26 * Holiday Sessions Cancelled
                if ($listing->type === 'course' && $courseTotalSessions > 0) {
                    $refundAmount = round((count($dates) / $courseTotalSessions) * $student->payment_amount, 2);
                } else {
                    $refundAmount = round(($student->payment_amount / WORKING_DAYS) * count($dates), 2);
                }

                if ($refundAmount > 0) {
                    $refundModel->queueRefund($student->id, $refundAmount, true);
                    $bookingModel->update($student->id, ['payment_status' => 'refund_pending']);
                    $refundsCount++;
                }
                $txt = $refundAmount > 0 ? " A pro-rata refund of ₹{$refundAmount} has been initiated." : "";
                $msg = "Holiday declared for '{$listing->title}' ({$student->batch_name}) on {$displayDates}.{$txt}";
            } else {
                // Extension Logic: Extend by number of session days
                // $extension passed from JS (e.g. "7 days")
                $extVal = $extension ?: (count($dates) . " days");
                $newEnd = date('Y-m-d', strtotime("+$extVal", strtotime($newEnd)));
                $bookingModel->update($student->id, ['enrollment_end_date' => $newEnd]);
                $extensionCount++;
                $msg = "Holiday declared for '{$listing->title}' ({$student->batch_name}) on {$displayDates}. Your access has been extended until " . date('d M Y', strtotime($newEnd)) . ".";
            }
            $notify->sendWhatsApp($student->parent_phone, $msg);
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => "Holidays applied. " . ($action === 'refund' ? "$refundsCount refunds queued" : "$extensionCount enrollments extended") . "."
        ]);
    }

    /**
     * Show Create Listing Form — Subtask 2.2
     */
    public function create()
    {
        $userId = logged('id');
        $role   = logged('role');
        
        if ($role != 2) {
            return redirect()->to('/')
                ->with('error', 'Access denied. Only verified Class Providers can create listings.');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->getById($userId);

        if (!$user || $user->provider_verification_status === 'rejected') {
            return redirect()->to('provider/verification')
                ->with('error', 'Your provider account verification was rejected or not found. Please update your documents to proceed.');
        }

        $catModel = new \App\Models\CategoryModel();
        $listingModel = new \App\Models\ListingModel();
        
        $instrModel = new \App\Models\InstructorModel();
        $instructors = $instrModel->getByProvider($userId);
        
        return view('frontend/provider/create_listing', [
            'page_title'        => 'List a New Class | Class Next Door',
            'user'              => $user,
            'categories'        => $catModel->getDropdown(),
            'instructors'        => $instructors,
            'show_location_bar' => false,
        ]);
    }

    /**
     * POST: Store New Listing — Subtask 2.2
     */
    public function store()
    {
        $providerId = logged('id');
        $role       = logged('role');

        if (!$providerId || $role != 2) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($providerId);

        if (!$user || $user->provider_verification_status === 'rejected') {
            return $this->response->setJSON(['success' => false, 'message' => 'Account rejected or not found. Cannot create classes.']);
        }

        // Determine class status based on provider status
        $isVerified = ($user->provider_verification_status === 'approved');
        $classStatus = $isVerified ? 'active' : 'inactive';
        $reviewStatus = $isVerified ? 'approved' : 'pending';
        $paymentStatus = $isVerified ? 'success' : 'pending';

        // Basic validation
        $rules = [
            'institute_name'    => 'required|min_length[5]|max_length[100]',
            'category_id'       => 'required|is_natural_no_zero',
            'subcategory_ids'   => 'required', 
            'type'              => 'required|in_list[regular,workshop,course]',
            'manual_address'    => 'required|min_length[10]',
            'description'       => 'required|min_length[50]',
            'latitude'          => 'required',
            'longitude'         => 'required',
        ];

        // Image Count Enforcement
        $images = $this->request->getFileMultiple('images');
        $imageCount = 0;
        if ($images) {
            foreach ($images as $img) if ($img && $img->isValid()) $imageCount++;
        }
        if ($imageCount < 3 || $imageCount > 5) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please upload between 3 and 5 class photos.']);
        }

        $type = $this->request->getPost('type');
        
        if ($type === 'regular') {
            $rules['batches.*.name'] = 'required';
            $rules['batches.*.batch_start_date'] = 'required';
            $rules['batches.*.from_time'] = 'required';
            $rules['batches.*.to_time'] = 'required';
            $rules['batches.*.price'] = 'required|numeric|greater_than_equal_to[0]';
            $rules['batches.*.batch_size'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['batches.*.instructor_name'] = 'required|min_length[3]';
        } else {
            $rules['instructor_name']       = 'required|min_length[3]';
        }

        if ($type === 'workshop') {
            $rules['workshop.start_date'] = 'required';
            $rules['workshop.from_time'] = 'required';
            $rules['workshop.to_time'] = 'required';
            $rules['workshop.price'] = 'required|numeric|greater_than_equal_to[0]';
            $rules['workshop.batch_size'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['workshop.registration_end_date'] = 'required';
        } elseif ($type === 'course') {
            $rules['course.duration_number'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['course.duration_type'] = 'required|in_list[weeks,months]';
            $rules['course.start_date'] = 'required';
            $rules['course.end_date'] = 'required';
            $rules['course.from_time'] = 'required';
            $rules['course.to_time'] = 'required';
            $rules['course.price'] = 'required|numeric|greater_than_equal_to[0]';
            $rules['course.batch_size'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['course.registration_end_date'] = 'required';
        }

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(' ', $errors), 'errors' => $errors]);
        }

        // ── Custom Logical Validation ──
        $catId = (int)$this->request->getPost('category_id');
        $subIds = (array)$this->request->getPost('subcategory_ids');
        
        if (empty($subIds) || (count($subIds) == 1 && empty($subIds[0]))) {
             return $this->response->setJSON(['success' => false, 'message' => 'At least one subcategory is required.']);
        }

        $subModel = new \App\Models\SubcategoryModel();
        foreach ($subIds as $sid) {
            if (empty($sid)) continue;
            $subData = $subModel->find($sid);
            if (!$subData || $subData->category_id != $catId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid subcategory selection. Subcategories must belong to the selected category.']);
            }
        }

        if ($type === 'workshop') {
            $w = $this->request->getPost('workshop');
            if (!empty($w['early_bird_price']) && (float)$w['early_bird_price'] >= (float)$w['price']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird price must be less than the standard workshop price.']);
            }
            if (strtotime($w['registration_end_date']) > strtotime($w['start_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Registration must end on or before the workshop start date.']);
            }
            if (!empty($w['early_bird_end_date']) && strtotime($w['early_bird_end_date']) > strtotime($w['registration_end_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird offer must end on or before the registration end date.']);
            }
        } elseif ($type === 'course') {
            $c = $this->request->getPost('course');
            if (strtotime($c['start_date']) >= strtotime($c['end_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Course start date must be strictly before the end date.']);
            }
            if (strtotime($c['registration_end_date']) > strtotime($c['start_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Registration must end on or before the course start date.']);
            }
            if (!empty($c['early_bird_price']) && (float)$c['early_bird_price'] >= (float)$c['price']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird price must be less than the course price.']);
            }
            if (!empty($c['early_bird_end_date']) && strtotime($c['early_bird_end_date']) > strtotime($c['registration_end_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird offer must end on or before the registration end date.']);
            }
        } elseif ($type === 'regular') {
            $batches = $this->request->getPost('batches');
            if(!empty($batches)) {
                if (count((array)$batches) > 5) {
                    return $this->response->setJSON(['success' => false, 'message' => 'You can add up to 5 batches only.']);
                }
                foreach($batches as $idx => $b) {
                   if(strtotime($b['from_time']) >= strtotime($b['to_time'])) {
                       return $this->response->setJSON(['success' => false, 'message' => "Batch '{$b['name']}' has invalid timing."]);
                   }
                }
            }
        }

        // ── Image Count Validation (Includes Batch Images) ──
        $images = $this->request->getFileMultiple('images');
        $batchImages = $this->request->getFileMultiple('batch_images');
        
        $totalImageCount = 0;
        if($images) {
            foreach($images as $img) if($img && $img->isValid()) $totalImageCount++;
        }
        if($batchImages) {
            foreach($batchImages as $img) if($img && $img->isValid()) $totalImageCount++;
        }

        if($totalImageCount < 3 || $totalImageCount > 5) {
            return $this->response->setJSON(['success' => false, 'message' => "Total images must be between 3 and 5. You have {$totalImageCount}."]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $model = new ListingModel();
            $instrModel = new \App\Models\InstructorModel();
            
            $subcategoryIds = (array)$this->request->getPost('subcategory_ids');
            $primarySubcategory = !empty($subcategoryIds) ? $subcategoryIds[0] : null;

            $listingData = [
                'provider_id'            => $providerId,
                'category_id'            => (int)$this->request->getPost('category_id'),
                'subcategory_id'         => $primarySubcategory, 
                'institute_name'         => $this->request->getPost('institute_name'),
                'title'                  => $this->request->getPost('institute_name'),
                'description'            => $this->request->getPost('description'),
                'type'                   => $this->request->getPost('type'),
                'manual_address'         => $this->request->getPost('manual_address'),
                'address'                => $this->request->getPost('formatted_address'),
                'formatted_address'      => $this->request->getPost('formatted_address'),
                'latitude'               => $this->request->getPost('latitude'),
                'longitude'              => $this->request->getPost('longitude'),
                'city'                   => $this->request->getPost('city'),
                'locality'               => $this->request->getPost('locality'),
                'pincode'                => $this->request->getPost('pincode'),
            ];

            $instrModel = new \App\Models\InstructorModel();

            if ($type === 'regular') {
                $batches = $this->request->getPost('batches');
                if (!empty($batches) && is_array($batches)) {
                    $batches = array_values($batches);
                    $batchImages = $this->request->getFileMultiple('batch_images');
                    $batchKyc = $this->request->getFileMultiple('batch_instructor_kyc');
                    
                    foreach($batches as $idx => &$b) {
                        $b['price'] = (float)($b['price'] ?? 0);
                        $b['batch_size'] = (int)($b['batch_size'] ?? 0);
                        
                        // 1. Batch Image
                        if (isset($batchImages[$idx]) && $batchImages[$idx]->isValid()) {
                            $file = $batchImages[$idx];
                            $newName = 'batch_' . trim($idx) . '_' . $file->getRandomName();
                            $file->move(FCPATH . 'uploads/listings', $newName);
                            $b['image'] = 'uploads/listings/' . $newName;
                        }
                        
                        // 2. Instructor Handling
                        $instOpt = $b['instructor_option'] ?? 'new';
                        if ($instOpt === 'new') {
                             $newInstData = [
                                 'provider_id'  => $providerId,
                                 'name'         => $b['instructor_name'] ?? '',
                                 'experience'   => $b['experience'] ?? '',
                                 'social_links' => $b['social_links'] ?? '',
                                 'kyc_status'   => 'pending'
                             ];
                             if (isset($batchKyc[$idx]) && $batchKyc[$idx]->isValid()) {
                                 $file = $batchKyc[$idx];
                                 $newName = 'inst_kyc_' . $idx . '_' . $file->getRandomName();
                                 $file->move(FCPATH . 'uploads/kyc', $newName);
                                 $newInstData['kyc_doc'] = 'uploads/kyc/' . $newName;
                             }
                             $b['instructor_id'] = $instrModel->insert($newInstData);
                        } else {
                             $existingInst = $instrModel->where('id', $instOpt)->where('provider_id', $providerId)->first();
                             if ($existingInst) {
                                 $b['instructor_id']   = $existingInst->id;
                                 $b['instructor_name'] = $existingInst->name;
                                 $b['experience']      = $existingInst->experience;
                                 $b['social_links']    = $existingInst->social_links;
                             } else {
                                 throw new \Exception("Unauthorized instructor selection for batch.");
                             }
                        }
                    }
                    $listingData['batches'] = json_encode($batches);
                    
                    if(!empty($batches[0])) {
                        $listingData['instructor_id']   = $batches[0]['instructor_id'];
                        $listingData['instructor_name'] = $batches[0]['instructor_name'] ?? '';
                        $listingData['experience']      = $batches[0]['experience'] ?? '';
                    }

                    $minPrice = 0;
                    $minPriceType = 'monthly';
                    if (!empty($batches)) {
                        $minBatch = $batches[0];
                        foreach($batches as $b) {
                            if ((float)($b['price'] ?? 0) < (float)($minBatch['price'] ?? 0)) {
                                $minBatch = $b;
                            }
                        }
                        $minPrice = (float)($minBatch['price'] ?? 0);
                        $minPriceType = $minBatch['price_type'] ?? 'monthly';
                    }
                    $listingData['price'] = $minPrice;
                    $listingData['price_type'] = $minPriceType;
                    $dates = array_column($batches, 'batch_start_date');
                    $listingData['start_date'] = !empty($dates) ? min($dates) : null;
                }
            } else {
                // Shared Instructor (Workshop/Course)
                $instOpt = $this->request->getPost('instructor_option');
                if ($instOpt === 'new') {
                     $newInstData = [
                         'provider_id'  => $providerId,
                         'name'         => $this->request->getPost('instructor_name'),
                         'experience'   => $this->request->getPost('experience'),
                         'social_links' => $this->request->getPost('social_links'),
                         'kyc_status'   => 'pending'
                     ];
                     $kycFile = $this->request->getFile('instructor_kyc_doc');
                     if ($kycFile && $kycFile->isValid()) {
                         $newName = 'kyc_' . $kycFile->getRandomName();
                         $kycFile->move(FCPATH . 'uploads/kyc', $newName);
                         $newInstData['kyc_doc'] = 'uploads/kyc/' . $newName;
                     }
                     $listingData['instructor_id'] = $instrModel->insert($newInstData);
                     $listingData['instructor_name'] = $newInstData['name'];
                     $listingData['experience'] = $newInstData['experience'];
                     $listingData['social_links'] = $newInstData['social_links'];
                } else {
                     $existingInst = $instrModel->where('id', $instOpt)->where('provider_id', $providerId)->first();
                     if ($existingInst) {
                         $listingData['instructor_id']   = $existingInst->id;
                         $listingData['instructor_name'] = $existingInst->name;
                         $listingData['experience']      = $existingInst->experience;
                         $listingData['social_links']    = $existingInst->social_links;
                     } else {
                         throw new \Exception("Unauthorized instructor selection.");
                     }
                }

                if ($type === 'workshop') {
                    $w = $this->request->getPost('workshop');
                    $listingData['start_date']           = $w['start_date'] ?? null;
                    $listingData['class_time']           = $w['from_time'] ?? null;
                    $listingData['class_end_time']       = $w['to_time'] ?? null;
                    $listingData['price']                = (float)($w['price'] ?? 0);
                    $listingData['batch_size']           = (int)($w['batch_size'] ?? 0);
                    $listingData['registration_end_date'] = $w['registration_end_date'] ?? null;
                    $listingData['early_bird_date']      = $w['early_bird_end_date'] ?? null;
                    $listingData['early_bird_slots']     = (int)($w['early_bird_count'] ?? null);
                    $listingData['early_bird_price']     = (float)($w['early_bird_price'] ?? null);
                } elseif ($type === 'course') {
                    $c = $this->request->getPost('course');
                    $listingData['course_duration']      = (int)($c['duration_number'] ?? 0);
                    $listingData['course_duration_type'] = $c['duration_type'] ?? 'weeks';
                    $listingData['start_date']           = $c['start_date'] ?? null;
                    $listingData['end_date']             = $c['end_date'] ?? null;
                    $listingData['class_time']           = $c['from_time'] ?? null;
                    $listingData['class_end_time']       = $c['to_time'] ?? null;
                    $listingData['price']                = (float)($c['price'] ?? 0);
                    $listingData['batch_size']           = (int)($c['batch_size'] ?? 0);
                    $listingData['registration_end_date'] = $c['registration_end_date'] ?? null;
                    $listingData['early_bird_date']      = $c['early_bird_end_date'] ?? null;
                    $listingData['early_bird_slots']     = (int)($c['early_bird_count'] ?? null);
                    $listingData['early_bird_price']     = (float)($c['early_bird_price'] ?? null);
                    if (!empty($c['days'])) {
                        $listingData['price_breakdown'] = json_encode(['days' => $c['days']]);
                    }
                }
            }

            $listingId = $model->insert($listingData);
            $model->saveSubcategories((int)$listingId, $subcategoryIds);


            // Handle Image Uploads with positions & image count enforcement
            $images = $this->request->getFileMultiple('images');
            $imgModel = new \App\Models\ListingImageModel();
            
            $validImages = [];
            if ($images) {
                foreach ($images as $file) {
                    if ($file->isValid() && !$file->hasMoved()) $validImages[] = $file;
                }
            }
            
            if (count($validImages) < 3 || count($validImages) > 5) {
                throw new \Exception("Please upload between 3 and 5 class photos.");
            }

            $pos = 0;
            foreach ($validImages as $file) {
                $newName = $file->getRandomName();
                $file->move(FCPATH . 'uploads/listings', $newName);
                $imgModel->insert([
                    'listing_id' => $listingId,
                    'image_path' => 'uploads/listings/' . $newName,
                    'position'   => $pos++,
                ]);
            }

            // Handle Batch Images: ALREADY HANDLED ABOVE IN $type === 'regular' block

            $db->transComplete();

            // ── Notifications ──
            $userModel = new \App\Models\UserModel();
            $provider  = $userModel->getById($providerId);
            $notify    = new \App\Services\NotificationService();
            $emailSvc  = new \App\Services\EmailService();

            if ($provider) {
                $listingTitle = $this->request->getPost('title');
                $notify->notifyListingUnderReview($provider->phone, $listingTitle);
                $emailSvc->sendHTML($provider->email, "Listing Under Review: {$listingTitle}", "
                    <h3>Listing Received!</h3>
                    <p>Your class <strong>'{$listingTitle}'</strong> has been submitted and is currently under review.</p>
                    <p>Our team will verify the details and notify you once it's live on the platform.</p>
                ");

                $adminPhone = env('ADMIN_PHONE');
                $adminEmail = env('ADMIN_EMAIL');
                if ($adminPhone) {
                    $notify->notifyAdminNewListing($adminPhone, $provider->name, $listingTitle);
                }
                if ($adminEmail) {
                    $emailSvc->sendHTML($adminEmail, "ALERT: New Listing Submission", "
                        <h3>New Listing for Review</h3>
                        <p><strong>Provider:</strong> {$provider->name}</p>
                        <p><strong>Class:</strong> {$listingTitle}</p>
                        <p>Please log in to the admin panel to review and approve.</p>
                    ");
                }
            }

            $msg = $isVerified ? 'Class listed successfully! It is now live.' : 'Class listed successfully! It is now under review.';
            return $this->response->setJSON(['success' => true, 'message' => $msg, 'listing_id' => $listingId]);

        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $providerId = logged('id');
        $userModel = new \App\Models\UserModel();
        $model = new ListingModel();
        $listing = $model->getWithCategory((int)$id);

        if (!$listing || $listing->provider_id != $providerId) {
            return redirect()->to('provider/listings')->with('error', 'Listing not found.');
        }

        $catModel = new \App\Models\CategoryModel();
        $imgModel = new \App\Models\ListingImageModel();
        $instrModel = new \App\Models\InstructorModel();
        $instructors = $instrModel->getByProvider($providerId);

        return view('frontend/provider/edit_listing', [
            'page_title'        => 'Edit Class | ' . $listing->title,
            'user'              => $userModel->getById($providerId),
            'listing'           => $listing,
            'categories'        => $catModel->getDropdown(),
            'images'            => $imgModel->where('listing_id', $id)->findAll(),
            'instructors'        => $instructors,
            'show_location_bar' => false,
        ]);
    }

    public function update($id)
    {
        $providerId = logged('id');
        $model = new ListingModel();
        $listing = $model->where('id', $id)->where('provider_id', $providerId)->first();

        if (!$listing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Listing not found.']);
        }

        $rules = [
            'institute_name'    => 'required|min_length[5]|max_length[100]',
            'category_id'       => 'required|is_natural_no_zero',
            'subcategory_ids'   => 'required', 
            'type'              => 'required|in_list[regular,workshop,course]',
            'manual_address'    => 'required|min_length[10]',
            'description'       => 'required|min_length[50]',
            'latitude'          => 'required',
            'longitude'         => 'required',
        ];

        $type = $this->request->getPost('type');

        if ($type === 'regular') {
            $rules['batches.*.name'] = 'required';
            $rules['batches.*.batch_start_date'] = 'required';
            $rules['batches.*.from_time'] = 'required';
            $rules['batches.*.to_time'] = 'required';
            $rules['batches.*.price'] = 'required|numeric|greater_than_equal_to[0]';
            $rules['batches.*.batch_size'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['batches.*.instructor_name'] = 'required|min_length[3]';
        } else {
            $rules['instructor_name']       = 'required|min_length[3]';
        }

        if ($type === 'workshop') {
            $rules['workshop.start_date'] = 'required';
            $rules['workshop.from_time'] = 'required';
            $rules['workshop.to_time'] = 'required';
            $rules['workshop.price'] = 'required|numeric|greater_than_equal_to[0]';
            $rules['workshop.batch_size'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['workshop.registration_end_date'] = 'required';
        } elseif ($type === 'course') {
            $rules['course.duration_number'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['course.duration_type'] = 'required|in_list[weeks,months]';
            $rules['course.start_date'] = 'required';
            $rules['course.end_date'] = 'required';
            $rules['course.from_time'] = 'required';
            $rules['course.to_time'] = 'required';
            $rules['course.price'] = 'required|numeric|greater_than_equal_to[0]';
            $rules['course.batch_size'] = 'required|numeric|greater_than_equal_to[1]';
            $rules['course.registration_end_date'] = 'required';
        }

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(' ', $errors), 'errors' => $errors]);
        }

        // ── Custom Logical Validation ──
        $catId = (int)$this->request->getPost('category_id');
        $subIds = (array)$this->request->getPost('subcategory_ids');
        
        if (empty($subIds) || (count($subIds) == 1 && empty($subIds[0]))) {
             return $this->response->setJSON(['success' => false, 'message' => 'At least one subcategory is required.']);
        }

        $subModel = new \App\Models\SubcategoryModel();
        foreach ($subIds as $sid) {
            if (empty($sid)) continue;
            $subData = $subModel->find($sid);
            if (!$subData || $subData->category_id != $catId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid subcategory selection. Subcategories must belong to the selected category.']);
            }
        }

        if ($type === 'workshop') {
            $w = $this->request->getPost('workshop');
            if (!empty($w['early_bird_price']) && (float)$w['early_bird_price'] >= (float)$w['price']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird price must be less than the workshop price.']);
            }
            if (strtotime($w['registration_end_date']) > strtotime($w['start_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Registration must end on or before the workshop start date.']);
            }
            if (!empty($w['early_bird_end_date']) && strtotime($w['early_bird_end_date']) > strtotime($w['registration_end_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird offer must end on or before the registration end date.']);
            }
        } elseif ($type === 'course') {
            $c = $this->request->getPost('course');
            if (strtotime($c['start_date']) >= strtotime($c['end_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Course start date must be strictly before the end date.']);
            }
            if (strtotime($c['registration_end_date']) > strtotime($c['start_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Registration must end on or before the course start date.']);
            }
            if (!empty($c['early_bird_price']) && (float)$c['early_bird_price'] >= (float)$c['price']) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird price must be less than the course price.']);
            }
            if (!empty($c['early_bird_end_date']) && strtotime($c['early_bird_end_date']) > strtotime($c['registration_end_date'])) {
                return $this->response->setJSON(['success' => false, 'message' => 'Early bird offer must end on or before the registration end date.']);
            }
        } elseif ($type === 'regular') {
            $batches = $this->request->getPost('batches');
            if(!empty($batches)) {
                if (count((array)$batches) > 5) {
                    return $this->response->setJSON(['success' => false, 'message' => 'You can add up to 5 batches only.']);
                }
                foreach($batches as $idx => $b) {
                   if(strtotime($b['from_time']) >= strtotime($b['to_time'])) {
                       return $this->response->setJSON(['success' => false, 'message' => "Batch '{$b['name']}' has invalid timing."]);
                   }
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $subcategoryIds = (array)$this->request->getPost('subcategory_ids');
            $primarySubcategory = !empty($subcategoryIds) ? $subcategoryIds[0] : null;

            $listingData = [
                'category_id'            => (int)$this->request->getPost('category_id'),
                'subcategory_id'         => $primarySubcategory,
                'institute_name'         => $this->request->getPost('institute_name'),
                'title'                  => $this->request->getPost('institute_name'), 
                'description'            => $this->request->getPost('description'),
                'type'                   => $this->request->getPost('type'),
                'manual_address'         => $this->request->getPost('manual_address'),
                'address'                => $this->request->getPost('formatted_address') ?: $listing->address,
                'formatted_address'      => $this->request->getPost('formatted_address') ?: $listing->formatted_address,
                'latitude'               => $this->request->getPost('latitude') ?: $listing->latitude,
                'longitude'              => $this->request->getPost('longitude') ?: $listing->longitude,
                'instructor_name'        => $this->request->getPost('instructor_name'),
                'social_links'           => $this->request->getPost('social_links'),
                'experience'             => $this->request->getPost('experience'),
            ];

            $instrModel = new \App\Models\InstructorModel();

            if ($type === 'regular') {
                $batches = $this->request->getPost('batches');
                if (!empty($batches) && is_array($batches)) {
                    $batches = array_values($batches);
                    $batchImages = $this->request->getFileMultiple('batch_images');
                    $batchKyc = $this->request->getFileMultiple('batch_instructor_kyc');
                    
                    foreach($batches as $idx => &$b) {
                        $b['price'] = (float)($b['price'] ?? 0);
                        $b['batch_size'] = (int)($b['batch_size'] ?? 0);
                        
                        // 1. Batch Image
                        if (isset($batchImages[$idx]) && $batchImages[$idx]->isValid()) {
                            $file = $batchImages[$idx];
                            $newName = 'batch_upd_' . $idx . '_' . $file->getRandomName();
                            $file->move(FCPATH . 'uploads/listings', $newName);
                            $b['image'] = 'uploads/listings/' . $newName;
                        } 

                        // 2. Instructor Handling
                        $instOpt = $b['instructor_option'] ?? 'new';
                        if ($instOpt === 'new') {
                             $newInstData = [
                                 'provider_id'  => $providerId,
                                 'name'         => $b['instructor_name'] ?? '',
                                 'experience'   => $b['experience'] ?? '',
                                 'social_links' => $b['social_links'] ?? '',
                                 'kyc_status'   => 'pending'
                             ];
                             if (isset($batchKyc[$idx]) && $batchKyc[$idx]->isValid()) {
                                 $file = $batchKyc[$idx];
                                 $newName = 'inst_kyc_upd_' . $idx . '_' . $file->getRandomName();
                                 $file->move(FCPATH . 'uploads/kyc', $newName);
                                 $newInstData['kyc_doc'] = 'uploads/kyc/' . $newName;
                             }
                             $b['instructor_id'] = $instrModel->insert($newInstData);
                        } else {
                             $b['instructor_id'] = $instOpt;
                             $existingInst = $instrModel->find($instOpt);
                             if ($existingInst) {
                                 $b['instructor_name'] = $existingInst->name;
                                 $b['experience']      = $existingInst->experience;
                                 $b['social_links']    = $existingInst->social_links;
                             }
                        }
                    }
                    $listingData['batches'] = json_encode($batches);

                    if(!empty($batches[0])) {
                        $listingData['instructor_id']   = $batches[0]['instructor_id'];
                        $listingData['instructor_name'] = $batches[0]['instructor_name'] ?? '';
                        $listingData['experience']      = $batches[0]['experience'] ?? '';
                    }
                    
                    $minPrice = 0;
                    $minPriceType = 'monthly';
                    if (!empty($batches)) {
                        $minBatch = $batches[0];
                        foreach($batches as $b) {
                            if ((float)($b['price'] ?? 0) < (float)($minBatch['price'] ?? 0)) {
                                $minBatch = $b;
                            }
                        }
                        $minPrice = (float)($minBatch['price'] ?? 0);
                        $minPriceType = $minBatch['price_type'] ?? 'monthly';
                    }
                    $listingData['price'] = $minPrice;
                    $listingData['price_type'] = $minPriceType;
                    $dates = array_column($batches, 'batch_start_date');
                    $listingData['start_date'] = !empty($dates) ? min($dates) : null;
                }
            } else {
                // Shared Instructor (Workshop/Course)
                $instOpt = $this->request->getPost('instructor_option');
                if ($instOpt === 'new') {
                     $newInstData = [
                         'provider_id'  => $providerId,
                         'name'         => $this->request->getPost('instructor_name'),
                         'experience'   => $this->request->getPost('experience'),
                         'social_links' => $this->request->getPost('social_links'),
                         'kyc_status'   => 'pending'
                     ];
                     $kycFile = $this->request->getFile('instructor_kyc_doc');
                     if ($kycFile && $kycFile->isValid()) {
                         $newName = 'kyc_upd_' . $kycFile->getRandomName();
                         $kycFile->move(FCPATH . 'uploads/kyc', $newName);
                         $newInstData['kyc_doc'] = 'uploads/kyc/' . $newName;
                     }
                     $listingData['instructor_id'] = $instrModel->insert($newInstData);
                     $listingData['instructor_name'] = $newInstData['name'];
                     $listingData['experience'] = $newInstData['experience'];
                     $listingData['social_links'] = $newInstData['social_links'];
                } else {
                     $listingData['instructor_id'] = $instOpt;
                     $existingInst = $instrModel->find($instOpt);
                     if ($existingInst) {
                         $listingData['instructor_name'] = $existingInst->name;
                         $listingData['experience']      = $existingInst->experience;
                         $listingData['social_links']    = $existingInst->social_links;
                     }
                }

                if ($type === 'workshop') {
                    $w = $this->request->getPost('workshop');
                    $listingData['start_date']           = $w['start_date'] ?? null;
                    $listingData['class_time']           = $w['from_time'] ?? null;
                    $listingData['class_end_time']       = $w['to_time'] ?? null;
                    $listingData['price']                = (float)($w['price'] ?? 0);
                    $listingData['batch_size']           = (int)($w['batch_size'] ?? 0);
                    $listingData['registration_end_date'] = $w['registration_end_date'] ?? null;
                    $listingData['early_bird_date']      = $w['early_bird_end_date'] ?? null;
                    $listingData['early_bird_slots']     = (int)($w['early_bird_count'] ?? null);
                    $listingData['early_bird_price']     = (float)($w['early_bird_price'] ?? null);
                } elseif ($type === 'course') {
                    $c = $this->request->getPost('course');
                    $listingData['course_duration']      = (int)($c['duration_number'] ?? 0);
                    $listingData['course_duration_type'] = $c['duration_type'] ?? 'weeks';
                    $listingData['start_date']           = $c['start_date'] ?? null;
                    $listingData['end_date']             = $c['end_date'] ?? null;
                    $listingData['class_time']           = $c['from_time'] ?? null;
                    $listingData['class_end_time']       = $c['to_time'] ?? null;
                    $listingData['price']                = (float)($c['price'] ?? 0);
                    $listingData['batch_size']           = (int)($c['batch_size'] ?? 0);
                    $listingData['registration_end_date'] = $c['registration_end_date'] ?? null;
                    $listingData['early_bird_date']      = $c['early_bird_end_date'] ?? null;
                    $listingData['early_bird_slots']     = (int)($c['early_bird_count'] ?? null);
                    $listingData['early_bird_price']     = (float)($c['early_bird_price'] ?? null);
                    if (!empty($c['days'])) {
                        $listingData['price_breakdown'] = json_encode(['days' => $c['days']]);
                    }
                }
            }

            $listingData['review_status'] = 'pending';
            $model->update($id, $listingData);

            $images = $this->request->getFileMultiple('images');
            if ($images) {
                $imgModel = new \App\Models\ListingImageModel();
                $existingCount = $imgModel->where('listing_id', $id)->countAllResults();
                foreach ($images as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/listings', $newName);
                        $imgModel->insert([
                            'listing_id' => $id,
                            'image_path' => 'uploads/listings/' . $newName,
                            'position'   => $existingCount++,
                        ]);
                    }
                }
            }


            // Sync Subcategories
            $model->saveSubcategories((int)$id, $subcategoryIds);

            $db->transComplete();
            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database error during update.']);
            }
            return $this->response->setJSON(['success' => true, 'message' => 'Class updated successfully!']);

        } catch (\Throwable $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function deleteListingImage($imageId)
    {
        $providerId = logged('id');
        $imgModel = new \App\Models\ListingImageModel();
        $listingModel = new ListingModel();

        $image = $imgModel->find($imageId);
        if (!$image) {
            return $this->response->setJSON(['success' => false, 'message' => 'Image not found.']);
        }

        $listing = $listingModel->where('id', $image->listing_id)->where('provider_id', $providerId)->first();
        if (!$listing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized to delete this image.']);
        }

        if ($imgModel->delete($imageId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Image deleted successfully.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Could not delete image.']);
    }


    public function verification()
    {
        $userId = logged('id');
        $role   = logged('role');

        // Allow access to verification page even if role is already 2 (Provider) but status is pending/rejected
        // Or if they are role 3 (Parent) trying to become provider.
        if ($role == 1) {
            return redirect()->to('admin/dashboard');
        }

        $userModel = new \App\Models\UserModel();
        $docModel  = new \App\Models\UserDocumentModel();
        $dbUser    = $userModel->getById($userId);

        // Sync session with DB
        $cndUser = session()->get('cnd_user');
        if ($cndUser && $dbUser) {
            $cndUser['role'] = $dbUser->role;
            $cndUser['provider_verification_status'] = $dbUser->provider_verification_status;
            $cndUser['provider_verification_message'] = $dbUser->provider_verification_message;
            session()->set('cnd_user', $cndUser);
            session()->set('user_role', $dbUser->role);
        }

        // Fetch instructors (from listings) for the KYC page
        $listingModel = new ListingModel();
        $instructors = $listingModel->where('provider_id', $userId)
                                  ->select('instructor_name, experience, social_links, instructor_kyc_status, instructor_kyc_doc, title as class_title')
                                  ->findAll();

        return view('frontend/provider/verification', [
            'page_title'        => 'Verification & KYC | Class Next Door',
            'user'              => $dbUser, 
            'documents'         => $docModel->getByUser($userId),
            'instructors'        => $instructors,
            'show_location_bar' => false,
        ]);
    }

    /**
     * POST: Submit for Admin Verification
     */
    public function submitVerification()
    {
        $userId = logged('id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in.']);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Guard: Prevent re-submission if already pending or approved
        if ($user->provider_verification_status === \App\Models\UserModel::KYC_PENDING_REVIEW) {
            return $this->response->setJSON(['success' => false, 'message' => 'Your application is already under review.']);
        }
        if ($user->provider_verification_status === \App\Models\UserModel::KYC_APPROVED) {
            return $this->response->setJSON(['success' => false, 'message' => 'Your profile is already verified.']);
        }



        $docModel = new \App\Models\UserDocumentModel();
        $docs = $docModel->getByUser($userId);
        if (empty($docs)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please upload at least one KYC document.']);
        }

        // Update user: Transition to PENDING_REVIEW
        $updateData = [
            'role'                          => 2,
            'provider_verification_status'  => \App\Models\UserModel::KYC_PENDING_REVIEW,
            'provider_verification_message' => null, 
            'provider_submitted_at'         => date('Y-m-d H:i:s'),
        ];

        if ($userModel->update($userId, $updateData)) {
            // Update session
            $cndUser = session()->get('cnd_user');
            if ($cndUser) {
                $cndUser['role'] = 2;
                $cndUser['provider_verification_status'] = \App\Models\UserModel::KYC_PENDING_REVIEW;
                $cndUser['provider_verification_message'] = null;
                session()->set('cnd_user', $cndUser);
            }
            session()->set('user_role', 2); 
            
            // Notify Admin
            try {
                $emailSvc = new \App\Services\EmailService();
                $adminEmail = env('ADMIN_EMAIL', 'admin@classnextdoor.in');
                $emailSvc->sendHTML($adminEmail, "New Provider Verification Request: {$user->name}", "
                    <h3>Provider Verification Request</h3>
                    <p>A user has submitted/resubmitted their profile for provider verification.</p>
                    <p><strong>Name:</strong> {$user->name}<br>
                    <strong>Email:</strong> {$user->email}<br>
                    <strong>Phone:</strong> {$user->phone}</p>
                    <p><a href='" . base_url('admin/provider/' . $userId) . "'>Click here to review application</a></p>
                ");
            } catch (\Throwable $e) {
                log_message('error', 'Failed to notify admin: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your verification request has been submitted successfully! Please wait for admin approval.',
                'redirect' => base_url('provider/dashboard')
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to submit verification.']);
    }


    public function sendPhoneVerification()
    {
        $user = logged();
        if (empty($user->phone)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please set a phone number in your profile first.']);
        }

        $userModel = new \App\Models\UserModel();
        
        // Check if phone already exists for another user
        $existing = $userModel->where('phone', $user->phone)->where('id !=', $user->id)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'This phone number is already registered with another account. Please update your profile with a unique number.']);
        }

        // Automatic verification as per user request (OTP service inactive)
        $userModel->update($user->id, ['phone_verified' => 1]);

        return $this->response->setJSON([
            'success'       => true, 
            'auto_verified' => true,
            'message'       => 'Mobile number verified successfully.'
        ]);
    }

    /**
     * Temporary workaround: Mark phone as verified directly (Requested via new endpoint)
     */
    public function markPhoneVerified()
    {
        $user = logged();
        if (empty($user->phone)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please set a phone number in your profile first.']);
        }

        $userModel = new \App\Models\UserModel();
        
        // Check if phone already exists for another user
        $existing = $userModel->where('phone', $user->phone)->where('id !=', $user->id)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'This phone number is already registered with another account.']);
        }

        $userModel->update($user->id, ['phone_verified' => 1]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Mobile number verified successfully.',
            'phone'   => $user->phone
        ]);
    }

    public function checkPhoneVerification()
    {
        $user = logged();
        $otp = $this->request->getPost('otp');
        $notify = new \App\Services\NotificationService();
        if ($notify->verifyOtp($user->phone, $otp)) {
            $userModel = new \App\Models\UserModel();
            $userModel->updateById($user->id, ['phone_verified' => 1]);
            return $this->response->setJSON(['success' => true, 'message' => 'Phone verified successfully!']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid or expired OTP.']);
    }


    public function uploadKyc()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Allow upload even if under review, but lock once fully APPROVED
        if ($user->provider_verification_status === \App\Models\UserModel::KYC_APPROVED) {
            return $this->response->setJSON(['success' => false, 'message' => 'Profile already verified. Cannot upload more documents.']);
        }

        $type = $this->request->getPost('document_type');
        $rules = [
            'document_file' => 'uploaded[document_file]|max_size[document_file,2048]|ext_in[document_file,jpg,jpeg,png,webp,pdf]',
            'document_type' => 'required|in_list[aadhaar,pan,passport,gst,portfolio,other]',
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON([
                'success' => false, 
                'message' => reset($errors), 
                'errors'  => $errors
            ]);
        }

        $file = $this->request->getFile('document_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            if ($file->move(FCPATH . 'uploads/kyc', $newName)) {
                $docModel = new \App\Models\UserDocumentModel();
                $docModel->insert([
                    'user_id'         => $userId,
                    'document_type'   => $type,
                    'file_path'       => 'uploads/kyc/' . $newName,
                    'verified_status' => 'pending',
                ]);

                // Update to IN_PROGRESS if currently NOT_STARTED (or null)
                if (!$user->provider_verification_status || $user->provider_verification_status === \App\Models\UserModel::KYC_NOT_STARTED) {
                    $userModel->update($userId, ['provider_verification_status' => \App\Models\UserModel::KYC_IN_PROGRESS]);
                    
                    $cndUser = session()->get('cnd_user');
                    if ($cndUser) {
                        $cndUser['provider_verification_status'] = \App\Models\UserModel::KYC_IN_PROGRESS;
                        session()->set('cnd_user', $cndUser);
                    }
                }

                return $this->response->setJSON(['success' => true, 'message' => 'Document uploaded. Press "Submit Review" when finished.']);
            }
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Upload failed.']);
    }

    public function deleteKyc($id)
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        // Transition Guard: No deletes if APPROVED. Allowed for PENDING_REVIEW to permit corrections.
        if ($user->provider_verification_status === \App\Models\UserModel::KYC_APPROVED) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot modify documents while verification is approved.']);
        }

        $docModel = new \App\Models\UserDocumentModel();
        $doc = $docModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$doc) {
            return $this->response->setJSON(['success' => false, 'message' => 'Document not found.']);
        }

        // Check if document was previously verified (optional additional safety)
        if ($doc->verified_status === 'verified' && $user->provider_verification_status === \App\Models\UserModel::KYC_APPROVED) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete verified documents of an approved account.']);
        }

        // Delete file from server
        if (!empty($doc->file_path) && file_exists(FCPATH . $doc->file_path)) {
            @unlink(FCPATH . $doc->file_path);
        }

        // Remove DB reference
        $docModel->delete($id);

        // Reset KYC status if no documents remain
        $remaining = $docModel->where('user_id', $userId)->countAllResults();
        if ($remaining === 0) {
            $userModel->update($userId, [
                'provider_verification_status' => \App\Models\UserModel::KYC_NOT_STARTED,
            ]);
            
            // Sync session
            $cndUser = session()->get('cnd_user');
            if ($cndUser) {
                $cndUser['provider_verification_status'] = \App\Models\UserModel::KYC_NOT_STARTED;
                session()->set('cnd_user', $cndUser);
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Document deleted successfully.']);
    }

    public function updatePayout()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        $upiId = $this->request->getPost('upi_id');
        
        // Format Validation (Standard UPI: account@bank)
        $rules = [
            'upi_id' => 'required|min_length[3]|max_length[100]|regex_match[/^[a-zA-Z0-9\.\-_]{2,256}@[a-zA-Z]{2,64}$/]'
        ];
        
        if (!$this->validate($rules, [
            'upi_id' => [
                'regex_match' => 'Invalid UPI format. Example: name@bank',
                'required'    => 'UPI ID is required.'
            ]
        ])) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        // Check if UPI ID has changed for re-verification
        $isVerificationNeeded = true;
        if (!empty($user->upi_id) && $user->upi_id === $upiId && $user->upi_verified) {
            $isVerificationNeeded = false;
        }

        $updateData = [
            'upi_id' => $upiId,
        ];

        if ($isVerificationNeeded) {
            // "Verification Step": In this implementation, we auto-verify but track the event.
            // If the user wants a manual verification step, we would set upi_verified = 0 and wait for admin/API.
            // As per current "Automatic verification as requested", we set it to 1.
            $updateData['upi_verified']    = 1;
            $updateData['upi_verified_at'] = date('Y-m-d H:i:s');
            $updateData['bank_name']       = 'Verified UPI';
        }

        $userModel->updateById($userId, $updateData);

        return $this->response->setJSON([
            'success'       => true, 
            'message'       => $isVerificationNeeded ? 'UPI ID updated and verified.' : 'No changes detected.',
            'verified_name' => 'Verified UPI'
        ]);
    }

    public function updatePhone()
    {
        $userId = logged('id');
        $phone  = $this->request->getPost('phone');

        $rules = [
            'phone' => 'required|numeric|exact_length[10]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(' ', $errors)]);
        }

        $userModel = new \App\Models\UserModel();
        
        // Check if phone already exists for another user
        $existing = $userModel->where('phone', $phone)->where('id !=', $userId)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'This phone number is already registered with another account.']);
        }

        // Automatic verification as requested (OTP service not completed)
        $userModel->update($userId, [
            'phone'          => $phone,
            'phone_verified' => 1
        ]);

        // Clean up any pending verifications
        $verifyModel = new \App\Models\PhoneVerificationModel();
        $verifyModel->deleteByUser($userId);

        return $this->response->setJSON([
            'success'       => true,
            'auto_verified' => true,
            'message'       => 'Phone number updated and verified successfully.',
            'phone'         => $phone
        ]);
    }

    public function verifyPhoneOtp()
    {
        $userId = logged('id');
        $otp    = $this->request->getPost('otp');

        if (!$otp) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter the OTP.']);
        }

        $verifyModel = new \App\Models\PhoneVerificationModel();
        $record = $verifyModel->where('user_id', $userId)
                              ->where('token', $otp)
                              ->where('expires_at >=', date('Y-m-d H:i:s'))
                              ->first();

        if (!$record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid or expired OTP.']);
        }

        // Success: Update user phone and mark verified
        $userModel = new \App\Models\UserModel();
        $userModel->update($userId, [
            'phone'          => $record->phone,
            'phone_verified' => 1
        ]);

        // Clean up
        $verifyModel->deleteByUser($userId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Phone number verified and updated successfully!'
        ]);
    }

    public function updateEmail()
    {
        $userId = logged('id');
        $email  = $this->request->getPost('email');

        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return $this->response->setJSON(['success' => false, 'message' => implode(' ', $errors)]);
        }

        $userModel = new \App\Models\UserModel();
        
        // Check if email already exists for another user
        $existing = $userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'This email address is already registered with another account.']);
        }

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        $verifyModel = new \App\Models\EmailVerificationModel();
        $verifyModel->deleteByUser($userId); // Remove old tokens
        $verifyModel->insert([
            'user_id'    => $userId,
            'email'      => $email,
            'token'      => $otp, // Store OTP in token field
            'expires_at' => $expiry,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Send Email
        $emailSvc = new \App\Services\EmailService();
        
        $html = "
            <div style='font-family: sans-serif; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; border-radius: 10px;'>
                <h2 style='color: #3F3590;'>Verify Your Email - Class Next Door</h2>
                <p>Hello,</p>
                <p>Your one-time password (OTP) for email verification is:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <span style='background: #F3F4F6; color: #111827; padding: 15px 30px; font-size: 2rem; font-weight: 800; border-radius: 10px; letter-spacing: 5px;'>{$otp}</span>
                </div>
                <p>This OTP is valid for 15 minutes. Please do not share this code with anyone.</p>
                <hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>
                <p style='font-size: 12px; color: #999;'>If you did not request this, please ignore this email.</p>
            </div>
        ";

        if ($emailSvc->sendHTML($email, "Email Verification OTP - Class Next Door", $html)) {
            return $this->response->setJSON([
                'success' => true,
                'otp_sent' => true,
                'message' => 'A 6-digit OTP has been sent to your email. Please enter it below.',
                'email'   => $email
            ]);
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Email updated, but failed to send OTP. Please try again later.',
            'email'   => $email
        ]);
    }

    public function verifyEmailOtp()
    {
        $userId = logged('id');
        $otp    = $this->request->getPost('otp');

        if (!$otp) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter the OTP.']);
        }

        $verifyModel = new \App\Models\EmailVerificationModel();
        $record = $verifyModel->where('user_id', $userId)
                              ->where('token', $otp)
                              ->where('expires_at >=', date('Y-m-d H:i:s'))
                              ->first();

        if (!$record) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid or expired OTP.']);
        }

        // Success: Update user email and mark verified
        $userModel = new \App\Models\UserModel();
        $userModel->update($userId, [
            'email'          => $record->email,
            'email_verified' => 1
        ]);

        // Clean up
        $verifyModel->deleteByUser($userId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email verified successfully!'
        ]);
    }

    public function bookings()
    {
        $providerId   = logged('id');
        $bookingModel = new \App\Models\BookingModel();
        $userModel    = new \App\Models\UserModel();
        return view('frontend/provider/bookings', [
            'page_title' => 'Student Bookings | Class Next Door',
            'user'       => $userModel->getById($providerId),
            'bookings'   => $bookingModel->getByProvider($providerId),
            'show_location_bar' => false,
        ]);
    }

    public function instructors()
    {
        $userId = logged('id');
        $role   = logged('role');

        if (!$userId || !in_array($role, [1, 2, 3])) {
            return redirect()->to('/login');
        }

        // Force Provider mode session for management pages
        session()->set('cnd_provider_mode', true);

        $instrModel = new \App\Models\InstructorModel();
        $userModel  = new \App\Models\UserModel();

        return view('frontend/provider/instructors', [
            'page_title'        => 'Instructor Management | Class Next Door',
            'user'              => $userModel->getById($userId),
            'instructors'       => $instrModel->getByProvider($userId),
            'show_location_bar' => false,
        ]);
    }

    public function saveInstructor()
    {
        $providerId = logged('id');
        $role       = logged('role');

        if (!$providerId || !in_array($role, [1, 2, 3])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        $instrModel = new \App\Models\InstructorModel();
        
        $id = $this->request->getPost('id');
        $data = [
            'provider_id'  => $providerId,
            'name'         => $this->request->getPost('name'),
            'experience'   => $this->request->getPost('experience'),
            'social_links' => $this->request->getPost('social_links'),
        ];

        // KYC Doc Upload (Optional)
        $kycFile = $this->request->getFile('kyc_doc');
        if ($kycFile && $kycFile->isValid() && !$kycFile->hasMoved()) {
            $newName = 'inst_kyc_' . $providerId . '_' . $kycFile->getRandomName();
            $kycFile->move(FCPATH . 'uploads/kyc', $newName);
            $data['kyc_doc'] = 'uploads/kyc/' . $newName;
            $data['kyc_status'] = 'pending';
        } elseif ($this->request->getPost('remove_kyc') === '1') {
            $data['kyc_doc'] = null;
            $data['kyc_status'] = 'not_uploaded';
        }

        if ($id && is_numeric($id)) {
            $instrModel->update($id, $data);
            $instructor = $instrModel->find($id);
            $message = 'Instructor updated successfully!';
        } else {
            // Duplicate check by name for the same provider
            $existing = $instrModel->where('provider_id', $providerId)
                                  ->where('name', $data['name'])
                                  ->first();
            if ($existing) {
                return $this->response->setJSON([
                    'success'    => true, 
                    'message'    => 'Instructor already exists in your list.',
                    'instructor' => $existing
                ]);
            }

            $newId = $instrModel->insert($data);
            $instructor = $instrModel->find($newId);
            $message = 'Instructor added successfully!';
        }

        return $this->response->setJSON([
            'success'   => true, 
            'message'   => $message, 
            'instructor' => $instructor
        ]);
    }

    public function deleteInstructor()
    {
        $providerId = logged('id');
        $id         = $this->request->getPost('id');

        if (!$providerId || !$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $instrModel = new \App\Models\InstructorModel();
        $instructor = $instrModel->where('id', $id)->where('provider_id', $providerId)->first();

        if (!$instructor) {
            return $this->response->setJSON(['success' => false, 'message' => 'Instructor not found or access denied']);
        }

        if ($instrModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Instructor removed successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Could not delete instructor']);
    }

    /**
     * View Earnings & Settlements
     */
    public function payouts()
    {
        $providerId = logged('id');
        $userModel = new \App\Models\UserModel();
        $settlementModel = new \App\Models\SettlementModel();
        
        // 1. Get current period info
        $period = $settlementModel->getPeriodInfo();
        
        // 2. Fetch history
        $history = $settlementModel->where('provider_id', $providerId)
            ->where('status !=', 'upcoming')
            ->orderBy('payout_date', 'DESC')
            ->findAll();

        // 3. Dynamic Calculation for "Upcoming" (Current Period)
        $upcoming = $this->calculateCurrentSettlement($providerId, $period);

        return view('frontend/provider/payouts', [
            'page_title' => 'Earnings & Settlements | Class Next Door',
            'user'       => $userModel->getById($providerId),
            'upcoming'   => $upcoming,
            'history'    => $history,
            'period'     => $period
        ]);
    }

    private function calculateCurrentSettlement($providerId, $period)
    {
        $db = \Config\Database::connect();
        
        // Gross Bookings in this period
        $bookings = $db->table('bookings b')
            ->select('b.*, l.title as listing_title, l.id as listing_id')
            ->join('listings l', 'l.id = b.listing_id')
            ->where('l.provider_id', $providerId)
            ->where('b.created_at >=', $period['start'] . ' 00:00:00')
            ->where('b.created_at <=', $period['end'] . ' 23:59:59')
            ->whereIn('b.payment_status', ['paid', 'confirmed', 'success'])
            ->get()->getResult();

        // Refunds in this period
        $refunds = $db->table('refund_queue rq')
            ->select('rq.*, l.title as listing_title, l.id as listing_id')
            ->join('bookings b', 'b.id = rq.booking_id')
            ->join('listings l', 'l.id = b.listing_id')
            ->where('l.provider_id', $providerId)
            ->where('rq.created_at >=', $period['start'] . ' 00:00:00')
            ->where('rq.created_at <=', $period['end'] . ' 23:59:59')
            ->get()->getResult();

        $listingsBreakdown = [];
        $totalGross = 0;
        $totalRefunds = 0;

        foreach ($bookings as $b) {
            if (!isset($listingsBreakdown[$b->listing_id])) {
                $listingsBreakdown[$b->listing_id] = [
                    'id' => $b->listing_id,
                    'title' => $b->listing_title,
                    'gross' => 0, 'refunds' => 0, 'students' => [], 'batches' => []
                ];
            }
            $listingsBreakdown[$b->listing_id]['gross'] += $b->payment_amount;
            $listingsBreakdown[$b->listing_id]['students'][] = [
                'name' => $b->student_name,
                'amount' => $b->payment_amount,
                'date' => $b->created_at
            ];
            $batchName = $b->batch_name ?: 'Default';
            $listingsBreakdown[$b->listing_id]['batches'][$batchName] = ($listingsBreakdown[$b->listing_id]['batches'][$batchName] ?? 0) + $b->payment_amount;
            $totalGross += $b->payment_amount;
        }

        foreach ($refunds as $r) {
            $totalRefunds += $r->amount;
            if (isset($listingsBreakdown[$r->listing_id])) {
                $listingsBreakdown[$r->listing_id]['refunds'] += $r->amount;
            }
        }

        $commissionRate = 0.10; // 10% standard
        $commission = $totalGross * $commissionRate;
        $net = $totalGross - $commission - $totalRefunds;

        return [
            'period_start' => $period['start'],
            'period_end'   => $period['end'],
            'payout_date'  => $period['payout'],
            'gross'        => $totalGross,
            'commission'   => $commission,
            'refunds'      => $totalRefunds,
            'net'          => $net,
            'breakdown'    => $listingsBreakdown
        ];
    }

    /**
     * API: Raise a Concern
     * POST /provider/api/concerns/raise
     */
    public function raiseConcern()
    {
        $providerId = logged('id');
        if (!$providerId) return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);

        $issue = $this->request->getPost('issue');
        if (empty(trim($issue))) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please describe your concern.']);
        }

        $model = new \App\Models\ConcernModel();
        $concernId = $model->insert([
            'provider_id' => $providerId,
            'issue'       => $issue,
            'status'      => 'open'
        ]);

        // Audit Log
        model('App\Models\ActivityLogModel')->add("Provider #{$providerId} raised a CONCERN (#{$concernId})", $providerId);

        return $this->response->setJSON(['success' => true, 'message' => 'Your concern has been submitted to the admin. We will review it shortly.']);
    }

    /**
     * View Notifications / Activity Log
     */
    public function notifications()
    {
        $userId = logged('id');
        $userModel = new \App\Models\UserModel();
        $activityModel = new \App\Models\ActivityLogModel();

        $notifications = $activityModel->where('user', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(100)
            ->findAll();

        // Mark all as read when visiting the page
        $activityModel->where('user', $userId)->where('is_read', 0)->set(['is_read' => 1])->update();

        return view('frontend/provider/notifications', [
            'page_title'        => 'Notifications | Class Next Door',
            'user'              => $userModel->getById($userId),
            'notifications'     => $notifications,
            'show_location_bar' => false,
        ]);
    }
}
