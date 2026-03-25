<?php

namespace App\Controllers;

use App\Controllers\AdminBaseController;
use App\Models\ListingModel;
use App\Models\UserModel;
use App\Models\UserDocumentModel;
use App\Models\FeaturedCarouselModel;
use App\Services\NotificationService;
use App\Services\EmailService;

/**
 * AdminController — Class Next Door
 * ─────────────────────────────────────────────────────────────
 * Subtask 3.2: Listing Review and Management
 * Handles admin oversight, approvals, and provider banning.
 * ─────────────────────────────────────────────────────────────
 */
class AdminController extends AdminBaseController
{
    public $title = 'Review & Management';
    public $menu  = 'management';

    /**
     * View all pending provider verifications.
     */
    public function verifications()
    {
        $this->permissionCheck('listings_view');
        
        $db = \Config\Database::connect();
        
        // Fetch users who have submitted provider verification (role 2 or 3 + pending/rejected/approved)
        $query = $db->table('users u')
            ->select('u.id, u.name, u.email, u.phone, u.phone_verified, u.role, u.status, u.provider_verification_status, u.provider_submitted_at, COUNT(ud.id) as doc_count')
            ->join('user_documents ud', 'ud.user_id = u.id', 'left')
            ->whereIn('u.role', [2, 3])
            ->where('u.provider_verification_status IS NOT NULL')
            ->groupBy('u.id, u.name, u.email, u.phone, u.phone_verified, u.role, u.status, u.provider_verification_status, u.provider_submitted_at')
            ->orderBy('u.provider_submitted_at', 'DESC')
            ->get();

        $providers = $query->getResultObject();

        $this->updatePageData([
            'title' => 'Provider Verifications',
            'menu'  => 'verifications'
        ]);

        return view('admin/verifications/list', [
            'providers' => $providers
        ]);
    }

    /**
     * API: Review Provider (Approve/Reject/Request Info)
     * POST /admin/api/provider/review
     */
    public function reviewProvider()
    {
        $this->permissionCheck('listings_edit');
        if($this->request->getMethod() !== 'post') return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);

        $id      = $this->request->getPost('id');
        $status  = $this->request->getPost('status'); // approved / rejected / more_info
        $remarks = $this->request->getPost('remarks');

        // Validation: Required only if rejecting or requesting more info
        if ($status !== 'approved') {
            if (!$this->validate([
                'remarks' => [
                    'label' => 'Message to Provider',
                    'rules' => 'required|min_length[5]',
                    'errors' => [
                        'required' => 'Please provide a reason or instructions for the provider when not approving.'
                    ]
                ]
            ])) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => $this->validator->getError('remarks') ?: 'Validation failed'
                ]);
            }
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if (!$user) return $this->response->setJSON(['success' => false, 'message' => 'User not found']);

        $updateData = [
            'provider_verification_message' => $remarks
        ];

        if ($status === 'approved') {
            $updateData['provider_verification_status'] = 'approved';
            $updateData['provider_verified_at']         = date('Y-m-d H:i:s');
            $updateData['provider_verification_message'] = null; // Clear message on approval
            $updateData['is_verified']                  = 1;
            
            // If they are currently a Parent (3), promote them to Provider (2)
            if ($user->role == 3) {
                $updateData['role'] = 2;
            }

            // Also approve their existing pending classes
            $listingModel = new ListingModel();
            $listingModel->where('provider_id', $id)
                        ->where('status', 'pending')
                        ->set(['status' => 'active'])
                        ->update();
        } elseif ($status === 'rejected') {
            $updateData['provider_verification_status'] = 'rejected';
        } else {
            // "More Info" essentially keeps it pending but with a message
            $updateData['provider_verification_status'] = 'pending';
        }

        $userModel->update($id, $updateData);

        // Notify Provider
        $notify = new NotificationService();
        $emailSvc = new EmailService();

        if ($status === 'approved') {
            $notify->sendWhatsApp($user->phone, "Your provider account has been approved. All your classes are now live.");
            $emailSvc->sendHTML($user->email, "Account Approved!", "
                <h3>Congratulations!</h3>
                <p>Hello {$user->name},</p>
                <p>Your provider account has been approved. All your classes are now live.</p>
                <p><a href='" . base_url('provider/dashboard') . "'>Go to your dashboard</a></p>
            ");
        } else {
            $subject = ($status === 'rejected') ? "Provider Application Rejected" : "Additional Information Required for Provider Verification";
            $emailSvc->sendHTML($user->email, $subject, "
                <h3>Verification Update</h3>
                <p>Hello {$user->name},</p>
                <p><strong>Status:</strong> " . ucfirst($status) . "</p>
                <p><strong>Message from Admin:</strong> " . esc($remarks) . "</p>
                <p>Please log in to your dashboard to " . ($status === 'rejected' ? "submit a new application" : "upload the required documents") . ".</p>
            ");
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Provider status updated successfully.']);
    }

    public function __construct()
    {
        // Require admin role/permissions (handled in AdminBaseController)
    }

    /**
     * View all listings for review.
     */
    public function index()
    {
        $this->permissionCheck('listings_view');
        
        $model = new ListingModel();
        // Fetch all listings with provider and category info
        $listings = $model->db->table('listings l')
            ->select('l.*, c.name AS category_name, 
                      (SELECT GROUP_CONCAT(sc.name SEPARATOR ", ") FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                      u.name AS provider_name, u.email AS provider_email')
            ->join('categories c',     'c.id = l.category_id',    'left')
            ->join('users u',          'u.id = l.provider_id',    'left')
            ->orderBy('l.review_status', 'ASC') // pending first
            ->orderBy('l.created_at', 'DESC')
            ->get()->getResultObject();

        return view('admin/listings/review_list', [
            'listings' => $listings,
            'title'    => 'Listing Management'
        ]);
    }

    /**
     * API: Listing Review (Approve/Reject)
     * POST /api/admin/listings/review
     */
    public function reviewListing()
    {
        $this->permissionCheck('listings_edit');
        if($this->request->getMethod() !== 'post') return $this->response->setJSON(['success' => false, 'message' => 'Invalid method']);

        $id      = $this->request->getPost('id');
        $status  = $this->request->getPost('status'); // approved / rejected
        $remarks = $this->request->getPost('remarks');

        if (!in_array($status, ['approved', 'rejected'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status']);
        }

        // Validation: Required only if rejecting
        if ($status === 'rejected' && empty(trim($remarks))) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Please provide a reason for rejecting this listing so the provider can fix it.'
            ]);
        }

        $model = new ListingModel();
        $listing = $model->find($id);
        if (!$listing) return $this->response->setJSON(['success' => false, 'message' => 'Listing not found']);

        $updateData = [
            'review_status' => $status,
            'admin_remarks' => $remarks
        ];

        // If approved, set the main status to active automatically
        if ($status === 'approved') {
            $updateData['status'] = 'active';
        }

        $model->update($id, $updateData);

        // Notify Provider
        $userModel = new UserModel();
        $provider  = $userModel->find($listing->provider_id);
        if ($provider) {
            $notify = new NotificationService();
            if ($status === 'approved') {
                $notify->notifyListingPublished($provider->phone, $listing->title);
            } else {
                // Send rejection email/WA
                $emailSvc = new EmailService();
                $emailSvc->sendHTML($provider->email, "Listing Update: {$listing->title}", "
                    <h3>Listing Rejection</h3>
                    <p>Your class <strong>'{$listing->title}'</strong> has been rejected by the moderation team.</p>
                    <p><strong>Reason:</strong> " . esc($remarks) . "</p>
                    <p>Please update your listing details or KYC docs and resubmit.</p>
                ");
            }
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Listing ' . $status . ' successfully.']);
    }

    /**
     * API: Deactivate Listing
     * POST /api/admin/deactivate
     */
    public function deactivateListing()
    {
        $this->permissionCheck('listings_edit');
        $id = $this->request->getPost('id');
        
        $model = new ListingModel();
        $model->update($id, ['status' => 'inactive']);

        return $this->response->setJSON(['success' => true, 'message' => 'Listing deactivated.']);
    }

    /**
     * API: Block User (Ban Provider)
     * POST /api/admin/block-user
     */
    public function blockUser()
    {
        $this->permissionCheck('users_edit');
        $id      = $this->request->getPost('id');
        $remarks = $this->request->getPost('remarks');

        $userModel = new UserModel();
        $userModel->update($id, [
            'status'         => 'banned',
            'status_remarks' => $remarks
        ]);

        // Also deactivate all their listings
        $listingModel = new ListingModel();
        $listingModel->where('provider_id', $id)->set(['status' => 'inactive'])->update();

        return $this->response->setJSON(['success' => true, 'message' => 'User blocked and their listings deactivated.']);
    }

    /**
     * API: Promote Parent to Provider (Approve Verification)
     * POST /api/admin/promote-user
     */
    public function promoteUser()
    {
        $this->permissionCheck('listings_edit');
        $id = $this->request->getPost('id');

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found.']);
        }

        if ($user->role != 3) {
            return $this->response->setJSON(['success' => false, 'message' => 'Only Parent accounts can be promoted to Provider.']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 1. Update User Record: Role=2 (Provider), Status=active, Verify Phone & Email
            $userModel->update($id, [
                'role'           => 2,
                'status'         => 'active',
                'is_verified'    => 1, // Global verification badge
                'phone_verified' => 1,
                'email_verified' => 1,
                'provider_verification_status' => 'approved',
                'provider_verification_message' => null,
                'provider_verified_at' => date('Y-m-d H:i:s'),
            ]);

            // 2. Mark all KYC documents as verified
            $docModel = new UserDocumentModel();
            $docModel->where('user_id', $id)->set(['verified_status' => 'verified'])->update();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception("Database transaction failed.");
            }

            // 3. Notify user
            $notify = new NotificationService();
            $notify->sendWhatsApp($user->phone, "Congratulations! Your application to join Class Next Door as a Provider has been approved. You can now list your classes on the platform.");

            return $this->response->setJSON(['success' => true, 'message' => 'Provider verified and promoted successfully!']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to promote user: ' . $e->getMessage()]);
        }
    }

    /**
     * API: Verify Individual Document
     * POST /api/admin/document/verify
     */
    public function verifyDocument()
    {
        $this->permissionCheck('listings_edit');
        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if (!in_array($status, ['verified', 'rejected'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid status.']);
        }

        $docModel = new UserDocumentModel();
        $docModel->update($id, ['verified_status' => $status]);

        return $this->response->setJSON(['success' => true, 'message' => 'Document status updated to ' . $status]);
    }

    /**
     * View Provider Details (Aadhaar/Bank/Profile)
     */
    public function providerDetail($id)
    {
        $this->permissionCheck('listings_view');

        $userModel = new UserModel();
        $provider  = $userModel->find($id);
        if (!$provider || !in_array($provider->role, [2, 3])) {
            return redirect()->to('admin/listings')->with('error', 'Provider not found.');
        }

        $docModel = new UserDocumentModel();
        $docs     = $docModel->getByUser($id);

        $this->updatePageData([
            'title' => 'Verify Provider',
            'menu'  => 'verifications'
        ]);

        return view('admin/listings/provider_verify', [
            'provider' => $provider,
            'docs'     => $docs
        ]);
    }

    /**
     * API: Update Provider Razorpay Account ID
     * POST /admin/api/update-rzp-account
     */
    public function updateRzpAccount()
    {
        $this->permissionCheck('users_edit');
        $id      = $this->request->getPost('id');
        $account = $this->request->getPost('account_id');

        $userModel = new UserModel();
        $userModel->update($id, ['razorpay_account_id' => $account]);

        return $this->response->setJSON(['success' => true, 'message' => 'Account ID updated.']);
    }

    /**
     * View all settlements (Subtask 3.4)
     */
    public function settlements()
    {
        $this->permissionCheck('listings_view'); // Assuming users_edit includes settlement control

        $db = \Config\Database::connect();
        $builder = $db->table('transactions t')
            ->select('t.*, b.student_name, b.class_date, l.title as listing_title, l.type as listing_type, u.name as provider_name')
            ->join('bookings b', 'b.id = t.booking_id', 'left')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->join('users u',    'u.id = l.provider_id', 'left')
            ->where('t.transaction_type', 'payment')
            ->orderBy('t.created_at', 'DESC');

        $transactions = $builder->get()->getResultObject();

        return view('admin/settlements/list', [
            'transactions' => $transactions,
            'title'        => 'Settlement Management',
            '_page'        => (object)[
                'title' => 'Settlement Management',
                'menu'  => 'settlements'
            ]
        ]);
    }

    /**
     * API: Toggle Settlement Block
     * POST /admin/api/settlements/block
     */
    public function toggleSettlementBlock()
    {
        $this->permissionCheck('users_edit');
        $id      = $this->request->getPost('id');
        $reason  = $this->request->getPost('reason');
        $blocked = (int)$this->request->getPost('blocked'); // 1 or 0

        $db = \Config\Database::connect();
        $db->table('transactions')->where('id', $id)->update([
            'is_blocked'   => $blocked,
            'block_reason' => $reason
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Settlement status updated.']);
    }

    /**
     * View Carousel Management (Subtask 3.5)
     */
    public function carousel()
    {
        $this->permissionCheck('listings_edit');
        
        $db = \Config\Database::connect();
        $carouselModel = new FeaturedCarouselModel();
        $listingModel  = new ListingModel();

        // 1. Get unique states from listings (or a fixed list if preferred)
        $states = $db->table('listings')
                     ->select('DISTINCT(state) as state')
                     ->where('status', 'active')
                     ->get()->getResultArray();
        
        // For demonstration, if no states found, let's use a few common ones
        $stateList = array_column($states, 'state');
        if (empty($stateList)) {
            $stateList = ['Karnataka', 'Maharashtra', 'Delhi', 'Tamil Nadu'];
        } else {
            // Clean/Trim and unique
            $stateList = array_unique(array_map('trim', $stateList));
        }

        $carouselData = [];
        foreach ($stateList as $state) {
            $carouselData[$state] = $carouselModel->getByState($state);
        }

        // 2. Get all active listings for selection
        $allActive = $listingModel->where('status', 'active')
                                  ->orderBy('title', 'ASC')
                                  ->findAll();

        return view('admin/carousel/manage', [
            'carouselData' => $carouselData,
            'allActive'    => $allActive,
            'title'        => 'Carousel Management',
            '_page'        => (object)[
                'title' => 'Carousel Management',
                'menu'  => 'carousel'
            ]
        ]);
    }

    /**
     * API: Add Listing to Carousel
     */
    public function addCarouselListing()
    {
        $this->permissionCheck('listings_edit');
        
        $state     = $this->request->getPost('state');
        $listingId = $this->request->getPost('listing_id');

        $model = new FeaturedCarouselModel();
        
        // Count existing
        $count = $model->where('state', $state)->countAllResults();
        if ($count >= 5) {
            return $this->response->setJSON(['success' => false, 'message' => 'Maximum 5 listings allowed per state.']);
        }

        // Check unique
        if ($model->where(['state' => $state, 'listing_id' => $listingId])->first()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Listing already in carousel.']);
        }

        $model->insert([
            'state'      => $state,
            'listing_id' => $listingId,
            'position'   => $count
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Listing added to carousel.']);
    }

    /**
     * API: Remove from Carousel
     */
    public function removeCarouselListing()
    {
        $this->permissionCheck('listings_edit');
        $id = $this->request->getPost('id');

        $model = new FeaturedCarouselModel();
        $model->delete($id);

        return $this->response->setJSON(['success' => true, 'message' => 'Removed from carousel.']);
    }

    /**
     * API: Reorder Carousel
     */
    /**
     * API: Reorder Carousel
     */
    public function reorderCarousel()
    {
        $this->permissionCheck('listings_edit');
        $order = $this->request->getPost('order'); // Array of IDs in new order

        if (!is_array($order)) return $this->response->setJSON(['success' => false]);

        $model = new FeaturedCarouselModel();
        foreach ($order as $pos => $id) {
            $model->update($id, ['position' => $pos]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * View all categories.
     */
    public function categories()
    {
        $this->permissionCheck('listings_view');
        
        $model = new \App\Models\CategoryModel();
        $categories = $model->orderBy('name', 'ASC')->findAll();

        return view('admin/categories/list', [
            'categories' => $categories,
            'title'      => 'Category Management',
            '_page'      => (object)[
                'title' => 'Category Management',
                'menu'  => 'categories'
            ]
        ]);
    }

    /**
     * Save (Add/Edit) Category.
     */
    public function saveCategory()
    {
        $this->permissionCheck('listings_edit');
        
        $id = $this->request->getPost('id');
        $data = [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?: 'active',
        ];

        $model = new \App\Models\CategoryModel();
        
        if ($id) {
            $model->update($id, $data);
            $msg = 'Category updated successfully.';
        } else {
            $model->insert($data);
            $msg = 'Category created successfully.';
        }

        return redirect()->to('admin/categories')->with('success', $msg);
    }

    /**
     * Delete Category.
     */
    public function deleteCategory($id)
    {
        $this->permissionCheck('listings_edit');
        
        $subModel = new \App\Models\SubcategoryModel();
        $count = $subModel->where('category_id', $id)->countAllResults();
        
        if ($count > 0) {
            return redirect()->to('admin/categories')->with('error', 'Cannot delete category because it has subcategories.');
        }

        $model = new \App\Models\CategoryModel();
        $model->delete($id);

        return redirect()->to('admin/categories')->with('success', 'Category deleted successfully.');
    }

    /**
     * View all subcategories.
     */
    public function subcategories()
    {
        $this->permissionCheck('listings_view');
        
        $db = \Config\Database::connect();
        $subcategories = $db->table('subcategories s')
            ->select('s.*, c.name as category_name')
            ->join('categories c', 'c.id = s.category_id', 'left')
            ->orderBy('c.name', 'ASC')
            ->orderBy('s.name', 'ASC')
            ->get()->getResultObject();

        $catModel = new \App\Models\CategoryModel();
        $categories = $catModel->orderBy('name', 'ASC')->findAll();

        return view('admin/subcategories/list', [
            'subcategories' => $subcategories,
            'categories'    => $categories,
            'title'         => 'Subcategory Management',
            '_page'         => (object)[
                'title' => 'Subcategory Management',
                'menu'  => 'subcategories'
            ]
        ]);
    }

    /**
     * Save (Add/Edit) Subcategory.
     */
    public function saveSubcategory()
    {
        $this->permissionCheck('listings_edit');
        
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        
        // Load URL helper for slug generation
        helper('url');

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'description' => $this->request->getPost('description'),
            'status'      => $this->request->getPost('status') ?: 'active',
        ];

        $model = new \App\Models\SubcategoryModel();
        
        if ($id) {
            $model->update($id, $data);
            $msg = 'Subcategory updated successfully.';
        } else {
            $model->insert($data);
            $msg = 'Subcategory created successfully.';
        }

        return redirect()->to('admin/subcategories')->with('success', $msg);
    }

    /**
     * Delete Subcategory.
     */
    public function deleteSubcategory($id)
    {
        $this->permissionCheck('listings_edit');
        
        $listingModel = new \App\Models\ListingModel();
        $count = $listingModel->where('subcategory_id', $id)->countAllResults();
        
        if ($count > 0) {
            return redirect()->to('admin/subcategories')->with('error', 'Cannot delete subcategory because it is linked to classes.');
        }

        $model = new \App\Models\SubcategoryModel();
        $model->delete($id);

        return redirect()->to('admin/subcategories')->with('success', 'Subcategory deleted successfully.');
    }
}
