<?php

namespace App\Controllers;

use App\Models\ListingModel;

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

        return view('frontend/provider/dashboard', [
            'page_title'        => 'Provider Dashboard | Class Next Door',
            'user'              => $dbUser,
            'show_location_bar' => false,
        ]);
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
        $user = $userModel->find($userId);

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
        $classStatus = ($user->provider_verification_status === 'approved') ? 'active' : 'pending';

        // Basic validation
        $rules = [
            'institute_name'    => 'required|min_length[3]|max_length[100]',
            'category_id'       => 'required|is_natural_no_zero',
            'subcategory_ids'   => 'required', 
            'type'              => 'required|in_list[regular,workshop,course]',
            'manual_address'    => 'required|min_length[5]',
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
            $rules['batches.*.experience'] = 'required';
        } else {
            $rules['instructor_name']       = 'required|min_length[3]';
            $rules['experience']            = 'required';
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
            return $this->response->setJSON(['success' => false, 'message' => "Total images (including batch photos) must be between 3 and 5. Currently you have {$totalImageCount} valid photos."]);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $model = new ListingModel();
            
            $subcategoryIds = (array)$this->request->getPost('subcategory_ids');
            $primarySubcategory = !empty($subcategoryIds) ? $subcategoryIds[0] : null;

            $listingData = [
                'provider_id'            => $providerId,
                'created_by_provider_id' => $providerId,
                'category_id'            => (int)$this->request->getPost('category_id'),
                'subcategory_id'         => $primarySubcategory, 
                'institute_name'         => $this->request->getPost('institute_name'),
                'title'                  => $this->request->getPost('institute_name'), // Sync title with Institute Name
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
                'instructor_name'        => $this->request->getPost('instructor_name'),
                'social_links'           => $this->request->getPost('social_links'),
                'experience'             => $this->request->getPost('experience'),
                'instructor_kyc_status'  => 'pending',
                'status'                 => $classStatus,
                'review_status'          => 'pending',
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
                         $newName = 'kyc_' . $kycFile->getRandomName();
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

            $listingId = $model->insert($listingData);
            $model->saveSubcategories((int)$listingId, $subcategoryIds);


            // Handle Image Uploads with positions & check for batch images
            $images = $this->request->getFileMultiple('images');
            $imgModel = new \App\Models\ListingImageModel();
            $pos = 0;
            if ($images) {
                foreach ($images as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $newName = $file->getRandomName();
                        $file->move(FCPATH . 'uploads/listings', $newName);
                        $imgModel->insert([
                            'listing_id' => $listingId,
                            'image_path' => 'uploads/listings/' . $newName,
                            'position'   => $pos++,
                        ]);
                    }
                }
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

            return $this->response->setJSON(['success' => true, 'message' => 'Class listed successfully! It is now under review.', 'listing_id' => $listingId]);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $providerId = logged('id');
        $model = new ListingModel();
        $listing = $model->getWithCategory((int)$id);

        if (!$listing || $listing->provider_id != $providerId) {
            return redirect()->to('provider/listings')->with('error', 'Listing not found.');
        }

        $catModel = new \App\Models\CategoryModel();
        $imgModel = new \App\Models\ListingImageModel();
        $slotModel = new \App\Models\ListingAvailabilityModel();

        $instrModel = new \App\Models\InstructorModel();
        $instructors = $instrModel->getByProvider($providerId);
          return view('frontend/provider/edit_listing', [
            'page_title'        => 'Edit Class | ' . $listing->title,
            'listing'           => $listing,
            'categories'        => $catModel->getDropdown(),
            'images'            => $imgModel->where('listing_id', $id)->findAll(),
            'slots'             => $slotModel->getByListing($id),
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
            'institute_name'    => 'required|min_length[3]|max_length[100]',
            'category_id'       => 'required|is_natural_no_zero',
            'subcategory_ids'   => 'required', 
            'type'              => 'required|in_list[regular,workshop,course]',
            'manual_address'    => 'required|min_length[5]',
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
            $rules['batches.*.experience'] = 'required';
        } else {
            $rules['instructor_name']       = 'required|min_length[3]';
            $rules['experience']            = 'required';
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

            $slotModel = new \App\Models\ListingAvailabilityModel();
            $slotModel->where('listing_id', $id)->delete();

            if ($type === 'workshop') {
                $slots = $this->request->getPost('slots');
                if (!empty($slots) && is_array($slots)) {
                    foreach ($slots as $slot) {
                        if (!empty($slot['date']) && !empty($slot['time'])) {
                            $slotModel->insert([
                                'listing_id'     => $id,
                                'available_date' => $slot['date'],
                                'available_time' => $slot['time'],
                            ]);
                        }
                    }
                }
            }

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
            return $this->response->setJSON(['success' => true, 'message' => 'Class updated successfully!']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function disableDates()
    {
        $providerId = logged('id');
        $listingId = $this->request->getPost('listing_id');
        $date      = $this->request->getPost('date');
        $disable   = (int)$this->request->getPost('disable'); 

        $model = new ListingModel();
        $listing = $model->where('id', $listingId)->where('provider_id', $providerId)->first();
        if (!$listing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized.']);
        }

        $slotModel = new \App\Models\ListingAvailabilityModel();
        $slotModel->where('listing_id', $listingId)
                  ->where('available_date', $date)
                  ->set(['is_disabled' => $disable])
                  ->update();

        return $this->response->setJSON(['success' => true, 'message' => 'Availability updated for ' . $date]);
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
        $dbUser    = $userModel->find($userId);

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

        if (!$user->phone_verified) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please verify your phone number first.']);
        }

        $docModel = new \App\Models\UserDocumentModel();
        $docs = $docModel->getByUser($userId);
        if (empty($docs)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please upload at least one KYC document.']);
        }

        // Update user: Role (2) = Provider, Status = Pending
        $updateData = [
            'role'                          => 2,
            'provider_verification_status'  => 'pending',
            'provider_verification_message' => null, // Clear the message on resubmit
            'provider_submitted_at'         => date('Y-m-d H:i:s'),
        ];

        if ($userModel->update($userId, $updateData)) {
            // Update session for the UI to reflect new status immediately
            $cndUser = session()->get('cnd_user');
            if ($cndUser) {
                $cndUser['role'] = 2;
                $cndUser['provider_verification_status'] = 'pending';
                $cndUser['provider_verification_message'] = null;
                session()->set('cnd_user', $cndUser);
            }
            session()->set('user_role', 2); // Legacy key support
            
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
            } catch (\Exception $e) {
                log_message('error', 'Failed to notify admin on verification submit: ' . $e->getMessage());
            }

            $cnd_user = session()->get('cnd_user');
            $cnd_user['role'] = 2;
            session()->set('cnd_user', $cnd_user);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Your verification request has been submitted successfully! You now have access to the Provider Dashboard.',
                'redirect' => base_url('provider/dashboard')
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to submit verification. Please try again.']);
    }


    public function sendPhoneVerification()
    {
        $user = logged();
        if (empty($user->phone)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please set a phone number in your profile first.']);
        }

        // Automatic verification as per user request (OTP service inactive)
        $userModel = new \App\Models\UserModel();
        $userModel->updateById($user->id, ['phone_verified' => 1]);

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
        $userModel->updateById($user->id, ['phone_verified' => 1]);

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
        $type = $this->request->getPost('document_type');
        $rules = [
            'document_file' => 'uploaded[document_file]|max_size[document_file,2048]|ext_in[document_file,jpg,jpeg,png,pdf]',
            'document_type' => 'required|in_list[aadhaar,pan,passport,gst,portfolio,other]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }
        $file = $this->request->getFile('document_file');
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/kyc', $newName);
            $docModel = new \App\Models\UserDocumentModel();
            $docModel->insert([
                'user_id'         => $userId,
                'document_type'   => $type,
                'file_path'       => 'uploads/kyc/' . $newName,
                'verified_status' => 'pending',
            ]);
            return $this->response->setJSON(['success' => true, 'message' => 'Document uploaded for verification.']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'File upload failed.']);
    }

    public function updatePayout()
    {
        $userId = logged('id');
        $upiId  = $this->request->getPost('upi_id');
        $rules = ['upi_id' => 'required|regex_match[/^[a-zA-Z0-9\.\-_]{2,256}@[a-zA-Z]{2,64}$/]'];
        if (!$this->validate($rules, ['upi_id' => ['regex_match' => 'Please enter a valid UPI ID (e.g. name@bank)']])) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }
        $key = env('RAZORPAY_KEY'); $secret = env('RAZORPAY_SECRET');
        if (!$key || !$secret) {
            return $this->response->setJSON(['success' => false, 'message' => 'UPI verification unavailable.']);
        }
        $res = cnd_http_request('POST', 'https://api.razorpay.com/v1/payments/validate/vpa', ['vpa' => $upiId], ['Content-Type: application/json'], "{$key}:{$secret}");
        if ($res->code !== 200) { return $this->response->setJSON(['success' => false, 'message' => 'Unable to verify UPI ID.']); }
        $result = json_decode($res->body, true);
        if (isset($result['success']) && $result['success'] === true) {
            $userModel = new \App\Models\UserModel();
            $userModel->updateById($userId, [
                'upi_id'          => $upiId,
                'bank_name'       => $result['customer_name'] ?? 'UPI Verified',
                'bank_account_no' => null,
                'bank_ifsc'       => null,
            ]);
            return $this->response->setJSON(['success' => true, 'message' => 'UPI verified!', 'verified_name' => $result['customer_name']]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid UPI ID.']);
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
    public function payouts()
    {
        $providerId       = logged('id');
        $transactionModel = new \App\Models\TransactionModel();
        $userModel        = new \App\Models\UserModel();
        
        return view('frontend/provider/payouts', [
            'page_title' => 'Payouts | Class Next Door',
            'user'       => $userModel->getById($providerId),
            'payouts'    => $transactionModel->getByUser($providerId, 'payout'),
            'show_location_bar' => false,
        ]);
    }

    public function instructors()
    {
        $userId = logged('id');
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
        if (!$providerId) return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);

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
        }

        if ($id && is_numeric($id)) {
            $instrModel->update($id, $data);
            $message = 'Instructor updated successfully!';
        } else {
            $instrModel->insert($data);
            $message = 'Instructor added successfully!';
        }

        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }
}
