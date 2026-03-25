<?php

namespace App\Controllers;

use App\Models\ListingModel;
use App\Models\FeaturedCarouselModel;
use App\Models\CategoryModel;
use App\Models\FeedbackModel;
use App\Services\CarouselService;

/**
 * Frontend Controller — Class Next Door
 * ─────────────────────────────────────────────────────────────
 * Handles all parent-facing public pages.
 * Does NOT extend AdminBaseController — no auth required.
 * ─────────────────────────────────────────────────────────────
 */
class Frontend extends BaseController
{
    protected $helpers = ['basic', 'url', 'form', 'cookie', 'text'];

    /** Default search radius (km) if location is set. */
    const DEFAULT_RADIUS_KM = 25;

    /** Max listings per type shown on home page. */
    const HOME_LIMIT = 12;

    // ─────────────────────────────────────────────────────────
    // SHARED: resolve location from session/cookie
    // ─────────────────────────────────────────────────────────
    private function resolveLocation(): array
    {
        // Use raw $_COOKIE (not CI4's get_cookie()) to ensure we read cookies
        // set by JavaScript client-side, which bypass CI4's cookie prefix/domain config.
        $rawCookie = function(string $key): ?string {
            return isset($_COOKIE[$key]) && $_COOKIE[$key] !== '' ? rawurldecode($_COOKIE[$key]) : null;
        };

        $name  = $this->request->getGet('name')  ?? session()->get('cnd_location_name') ?? $rawCookie('cnd_location_name');
        $lat   = $this->request->getGet('lat')   ?? session()->get('cnd_lat')           ?? $rawCookie('cnd_lat');
        $lng   = $this->request->getGet('lng')   ?? session()->get('cnd_lng')           ?? $rawCookie('cnd_lng');
        $state = $this->request->getGet('state') ?? session()->get('cnd_state')         ?? $rawCookie('cnd_state');

        return [
            'name'  => $name,
            'lat'   => $lat  ? (float) $lat  : null,
            'lng'   => $lng  ? (float) $lng  : null,
            'state' => $state,
            'set'   => !empty($name) && !empty($lat) && !empty($lng),
        ];
    }

    // ─────────────────────────────────────────────────────────
    // JOIN AS PROVIDER — GET /join-as-provider
    // ─────────────────────────────────────────────────────────
    public function joinAsProvider()
    {
        $user = session()->get('cnd_user');
        if (!$user) {
            // This should be handled by filter, but safe-check
            return redirect()->to('/login?redirect=' . urlencode(current_url()));
        }

        $role = (int)($user['role'] ?? 0);

        if ($role === 2) {
            // Already a provider
            return redirect()->to('provider/dashboard');
        }

        if ($role === 3) {
            // Parent wanting to join as provider
            return redirect()->to('provider/verification');
        }

        // Default
        return redirect()->to('/');
    }

    // ─────────────────────────────────────────────────────────
    // HOME PAGE — GET /
    // ─────────────────────────────────────────────────────────
    public function index(): string
    {
        $loc = $this->resolveLocation();

        $listingModel    = new ListingModel();
        $carouselService = new CarouselService();

        // ── Featured carousel (smart: admin picks + algo gap-fill) ─
        $slides = $carouselService->getCarouselForState($loc['state']);
        
        // Normalise floats for view
        $featured_listings = array_map(function ($row) {
            $arr = (array) $row;
            $id  = $arr['listing_id'] ?? ($arr['id'] ?? null);
            
            if (empty($arr['cover_image']) && $id) {
               $db = \Config\Database::connect();
               $img = $db->table('listing_images')->where('listing_id', $id)->orderBy('position', 'ASC')->get()->getRow();
               $arr['cover_image'] = $img ? $img->image_path : null;
            }
            if (isset($arr['avg_rating'])) {
                $arr['avg_rating'] = round((float)$arr['avg_rating'], 1);
            }
            return $arr;
        }, $slides);

        // ── Listing sections: fetch 4 per type ONLY if location is set ─────
        $listings       = ['regular' => [], 'workshop' => [] , 'course' => [] ];
        $listings_total = 0;

        // Diagnostic Log for visibility issues
        if (ENVIRONMENT !== 'production') {
            $listingModel->logActiveListingStats($loc['lat'], $loc['lng']);
        }

        if ($loc['set'] && $loc['lat'] && $loc['lng']) {
            foreach (['regular', 'workshop', 'course'] as $type) {
                $rows = $listingModel->getByLocation(
                    $type,
                    $loc['lat'],
                    $loc['lng'],
                    self::DEFAULT_RADIUS_KM,
                    'distance', // Will be updated in ListingModel for distance ASC + rating DESC
                    4,
                    0
                );
                $listings[$type] = array_map(function($r) {
                    $item = (array) $r;
                    if (empty($item['cover_image'])) {
                       $db = \Config\Database::connect();
                       $img = $db->table('listing_images')->where('listing_id', $item['id'])->orderBy('position', 'ASC')->get()->getRow();
                       $item['cover_image'] = $img ? $img->image_path : null;
                    }
                    return $item;
                }, $rows);
                $listings_total += count($rows);
            }
        }

        $categories = (new \App\Models\CategoryModel())->orderBy('name', 'ASC')->findAll();

        return view('frontend/home', [
            'page_title'        => 'Find Classes Near You | Class Next Door',
            'meta_description'  => "Discover the best kids' classes — sports, arts, academics, coding, and more near you.",
            'show_location_bar' => false,
            'selected_location' => $loc['name'],
            'location_selected' => $loc['set'],
            'featured_listings' => $featured_listings,
            'listings'          => $listings,
            'listings_total'    => $listings_total,
            'location_state'    => $loc['state'],
            'categories'        => $categories,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // BROWSE CLASSES — GET /classes
    // ─────────────────────────────────────────────────────────
    /**
     * POST /submit-review
     */
    /**
     * POST /submit-review  OR  POST /api/reviews/submit
     * Handles review submission for all class types.
     */
    public function submitReview()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        // Handle both JSON (from my-bookings.php) and standard POST (if any)
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $listingId = (int)($input['listing_id'] ?? 0);
        $rating    = (int)($input['rating'] ?? 0);
        $text      = trim($input['review_text'] ?? '');

        $user      = session()->get('cnd_user');
        $userId    = $user['id'] ?? session()->get('user_id');
        $userPhone = $user['phone'] ?? session()->get('cnd_phone') ?? get_cookie('cnd_phone');

        if (!$userPhone) {
            return $this->response->setJSON(['success' => false, 'message' => 'Login/Phone required']);
        }
        
        if ($rating < 1 || $rating > 5) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please select a rating']);
        }

        $bookingModel = new \App\Models\BookingModel();
        $reviewModel  = new \App\Models\ReviewModel();

        // 1. Check enrolment (confirmed or completed)
        $enrolment = $bookingModel->checkEnrolment($listingId, $userId, $userPhone);
        if (!$enrolment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Enrolment required to post a review']);
        }

        // 2. Check if already reviewed
        if ($reviewModel->hasReviewed($listingId, $userId, $userPhone)) {
             return $this->response->setJSON(['success' => false, 'message' => 'You have already reviewed this class']);
        }

        // 3. Save review
        $reviewModel->insert([
            'listing_id'   => $listingId,
            'user_id'      => $userId ?: null,
            'parent_phone' => $userPhone ?: null,
            'rating'       => $rating,
            'review_text'  => $text ?: null,
        ]);

        // 4. Mark regular class booking as completed if not already (logic for certificates/completion)
        if ($enrolment && isset($enrolment->booking_status) && $enrolment->booking_status !== 'completed') {
            $bookingModel->markCompleted($enrolment->id);
        }

        return $this->response->setJSON([
            'success' => true, 
            'message' => 'Thank you! Your review has been posted successfully.'
        ]);
    }

    public function classes(): string
    {
        $loc        = $this->resolveLocation();
        $type       = $this->request->getGet('type')       ?? 'regular';
        $sort       = $this->request->getGet('sort')       ?? 'relevancy';
        $page       = max(1, (int) ($this->request->getGet('page')   ?? 1));
        $radius     = (float) ($this->request->getGet('radius')      ?? self::DEFAULT_RADIUS_KM);
        $categoryId = $this->request->getGet('category') !== null
                        ? (int) $this->request->getGet('category')
                        : null;
        $subcategoryId = $this->request->getGet('subcategory');
        if (is_string($subcategoryId)) {
            $subcategoryId = explode(',', $subcategoryId);
        }
        $subcategoryIdArr = array_filter(array_map('intval', (array)$subcategoryId));
        $dateFilter = $this->request->getGet('date_filter');
        $perPage    = 20;
        $offset     = ($page - 1) * $perPage;

        $listingModel  = new ListingModel();
        $categoryModel = new CategoryModel();

        // All categories for the filter row
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        $subcategories = [];
        if ($categoryId) {
            $subcategories = (new \App\Models\SubcategoryModel())
                            ->where('category_id', $categoryId)
                            ->where('status', 'active')
                            ->orderBy('name', 'ASC')
                            ->findAll();
        }

        $listings = ['regular' => [], 'workshop' => [], 'course' => []];
        $total    = 0;

        $rows = $listingModel->getByLocation(
            $type,
            $loc['lat'],
            $loc['lng'],
            $radius,
            $sort,
            $perPage,
            $offset,
            $categoryId,
            $subcategoryIdArr,
            $dateFilter
        );
        $listings[$type] = array_map(function($r) {
            $item = (array) $r;
            if (empty($item['cover_image'])) {
               $db = \Config\Database::connect();
               $img = $db->table('listing_images')->where('listing_id', $item['id'])->orderBy('position', 'ASC')->get()->getRow();
               $item['cover_image'] = $img ? $img->image_path : null;
            }
            return $item;
        }, $rows);
        $total = $listingModel->countByLocation(
            $type,
            $loc['lat'],
            $loc['lng'],
            $radius,
            $categoryId,
            $subcategoryIdArr,
            $dateFilter
        );

        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

        return view('frontend/classes', [
            'page_title'         => 'Browse Classes | Class Next Door',
            'meta_description'   => "Find and book kids' classes near you — filter by type, category, distance and more.",
            'show_location_bar'  => false,
            'selected_location'  => $loc['name'],
            'location_selected'  => $loc['set'],
            'listings'           => $listings,
            'listings_total'     => $total,
            'current_type'       => $type,
            'current_sort'       => $sort,
            'current_page'       => $page,
            'total_pages'        => $totalPages,
            'current_category'   => $categoryId,
            'current_subcategory'=> $subcategoryIdArr,
            'categories'         => array_map(function($c){ return (array) $c; }, $categories),
            'subcategories'      => array_map(function($s){ return (array) $s; }, $subcategories),
            'location_state'     => $loc['state'],
            'radius'             => $radius,
            'current_date_filter'=> $dateFilter,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // MY BOOKINGS — GET /my-bookings
    // Shows parent's own bookings — must be logged in.
    // ─────────────────────────────────────────────────────────
    public function myBookings(): string
    {
        $user = session()->get('cnd_user');
        if (empty($user)) {
            session()->setFlashdata('redirect_after_login', base_url('my-bookings'));
            return redirect()->to('login');
        }

        $phone = $user['phone'] ?? null;
        if (!$phone) {
            $userModel = new \App\Models\UserModel();
            $fullUser = $userModel->find($user['id']);
            $phone = $fullUser->phone ?? null;
        }

        if (!$phone) {
             return view('frontend/my_bookings', [
                'page_title'       => 'My Bookings | Class Next Door',
                'meta_description' => 'View and manage your class bookings.',
                'bookings'         => [],
                'user'             => $user,
            ]);
        }

        $db       = \Config\Database::connect();
        $bookings = $db->table('bookings b')
            ->select('b.*, l.title AS listing_title, l.address AS listing_address, l.type AS listing_type,
                      l.start_date AS listing_start_date, l.end_date AS listing_end_date, 
                      l.class_time AS listing_class_time, l.class_end_time AS listing_class_end_time,
                      (SELECT COUNT(*) FROM reviews r WHERE r.listing_id = b.listing_id 
                       AND (r.user_id = b.parent_id OR r.parent_phone = b.parent_phone)) > 0 as has_reviewed')
            ->join('listings l', 'l.id = b.listing_id', 'left')
            ->where('b.parent_phone', $phone)
            ->orderBy('b.created_at', 'DESC')
            ->limit(50)
            ->get()->getResultArray();

        return view('frontend/my_bookings', [
            'page_title'       => 'My Bookings | Class Next Door',
            'meta_description' => 'View and manage your class bookings.',
            'bookings'         => $bookings,
            'user'             => $user,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // NEARBY LISTINGS API — GET /api/listings/nearby
    // JSON endpoint consumed by app.js after location confirm.
    // ─────────────────────────────────────────────────────────
    public function nearbyListings()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $type       = $this->request->getGet('type')        ?? 'regular';
        $lat        = $this->request->getGet('lat')         ? (float) $this->request->getGet('lat')  : null;
        $lng        = $this->request->getGet('lng')         ? (float) $this->request->getGet('lng')  : null;
        $sort       = $this->request->getGet('sort')        ?? 'relevancy';
        $radius     = (float) ($this->request->getGet('radius')  ?? self::DEFAULT_RADIUS_KM);
        $page       = max(1, (int) ($this->request->getGet('page')  ?? 1));
        $limit      = min(20, max(1, (int) ($this->request->getGet('limit') ?? 12)));
        $offset     = ($page - 1) * $limit;
        $categoryId = $this->request->getGet('category') !== null
                        ? (int) $this->request->getGet('category')
                        : null;
        $subcategoryId = $this->request->getGet('subcategory');
        if (is_string($subcategoryId)) {
            $subcategoryId = explode(',', $subcategoryId);
        }
        $subcategoryIdArr = array_filter(array_map('intval', (array)$subcategoryId));
        $dateFilter = $this->request->getGet('date_filter');

        $listingModel = new ListingModel();

        $rows  = $listingModel->getByLocation($type, $lat, $lng, $radius, $sort, $limit, $offset, $categoryId, $subcategoryIdArr, $dateFilter);
        $total = $listingModel->countByLocation($type, $lat, $lng, $radius, $categoryId, $subcategoryIdArr, $dateFilter);

        // Normalise: convert objects → arrays, add placeholders for missing images
        $listings = array_map(function ($r) {
            $arr = (array) $r;
            if (empty($arr['cover_image'])) {
               $db = \Config\Database::connect();
               $img = $db->table('listing_images')->where('listing_id', $arr['id'])->orderBy('position', 'ASC')->get()->getRow();
               $arr['cover_image'] = $img ? $img->image_path : null;
            }
            if (isset($arr['avg_rating'])) {
                $arr['avg_rating'] = round((float) $arr['avg_rating'], 1);
            }
            if (isset($arr['distance_km'])) {
                $arr['distance_km'] = round((float) $arr['distance_km'], 1);
            }
            return $arr;
        }, $rows);

        return $this->response->setJSON([
            'success'         => true, // Always true now if query runs
            'location_served' => ($lat !== null && $lng !== null) ? ($total > 0) : true,
            'type'            => $type,
            'category_id'     => $categoryId,
            'subcategory_ids' => $subcategoryIdArr,
            'listings'        => $listings,
            'total'           => $total,
            'page'            => $page,
            'radius_km'       => $radius,
            'date_filter'     => $dateFilter,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // CAROUSEL API — GET /api/listings/carousel
    // Returns up to 5 carousel slides (admin picks + algo fill).
    // Query params:
    //   state     — Indian state name (optional, falls back to 'ALL')
    //   lat, lng  — user coordinates   (optional)
    //   radius    — km (default 25)
    // ─────────────────────────────────────────────────────────
    public function carouselApi()
    {
        $req    = service('request');
        $state  = trim($req->getGet('state')  ?? '');
        $lat    = $req->getGet('lat')  !== null ? (float) $req->getGet('lat')  : null;
        $lng    = $req->getGet('lng')  !== null ? (float) $req->getGet('lng')  : null;
        $radius = max(5, min(200, (float) ($req->getGet('radius') ?? 25)));

        if (empty($state)) {
            // try to fall back to session/cookie state
            $state = session()->get('cnd_state') ?? get_cookie('cnd_state') ?? 'ALL';
        }

        $carouselModel = new FeaturedCarouselModel();
        $slides = $carouselModel->getCarouselData(
            $state,
            $lat,
            $lng,
            $radius,
            5
        );

        // Normalise numeric fields
        $slides = array_map(function ($row) {
            $row['avg_rating']  = round((float)($row['avg_rating']  ?? 0), 1);
            $row['distance_km'] = isset($row['distance_km']) && $row['distance_km'] !== null
                ? round((float)$row['distance_km'], 1) : null;
            $row['price'] = isset($row['price']) ? (float)$row['price'] : null;
            return $row;
        }, $slides);

        return $this->response->setJSON([
            'success' => true,
            'state'   => $state,
            'total'   => count($slides),
            'slides'  => $slides,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // SEARCH API — GET /api/listings/search
    // Full-text keyword search with distance / sort / filter.
    // Query params:
    //   q        — keyword(s) [required, min 1 char]
    //   type     — 'regular'|'workshop'|'course'  (optional, all if absent)
    //   category — category ID                    (optional)
    //   sort     — 'relevancy'|'distance'|'rating'|'price_asc'|'price_desc'
    //   lat, lng — user coordinates               (optional but recommended)
    //   radius   — km (default 25, ignored if no lat/lng)
    //   page     — page number (default 1)
    //   limit    — per page   (default 12, max 40)
    // ─────────────────────────────────────────────────────────
    public function searchListings()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $q          = trim((string) ($this->request->getGet('q') ?? ''));
        $type       = $this->request->getGet('type')     ?? null;
        $sort       = $this->request->getGet('sort')     ?? 'relevancy';
        $radius     = (float) ($this->request->getGet('radius') ?? self::DEFAULT_RADIUS_KM);
        $page       = max(1, (int) ($this->request->getGet('page')  ?? 1));
        $limit      = min(40, max(1, (int) ($this->request->getGet('limit') ?? 12)));
        $offset     = ($page - 1) * $limit;
        $lat        = $this->request->getGet('lat') ? (float) $this->request->getGet('lat') : null;
        $lng        = $this->request->getGet('lng') ? (float) $this->request->getGet('lng') : null;
        $categoryId = $this->request->getGet('category') !== null
                        ? (int) $this->request->getGet('category')
                        : null;
        $subcategoryId = $this->request->getGet('subcategory');
        if (is_string($subcategoryId)) {
            $subcategoryId = explode(',', $subcategoryId);
        }
        $subcategoryIdArr = array_filter(array_map('intval', (array)$subcategoryId));
        $dateFilter = $this->request->getGet('date_filter');

        // Sanitise type
        if ($type !== null && !in_array($type, ['regular', 'workshop', 'course'])) {
            $type = null;
        }

        // Sanitise sort
        $validSorts = ['relevancy', 'distance', 'rating', 'price_asc', 'price_desc'];
        if (!in_array($sort, $validSorts)) {
            $sort = 'relevancy';
        }

        // Require at least a keyword OR a location
        if ($q === '' && $lat === null) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Provide a search keyword or allow location access.',
                'listings'=> [],
                'total'   => 0,
            ]);
        }

        $listingModel = new ListingModel();

        $rows  = $listingModel->search(
            $q,
            $type,
            $lat,
            $lng,
            $radius,
            $sort,
            $categoryId,
            $limit,
            $offset,
            $subcategoryIdArr,
            $dateFilter
        );
        $total = $listingModel->countSearch(
            $q,
            $type,
            $lat,
            $lng,
            $radius,
            $categoryId,
            $subcategoryIdArr,
            $dateFilter
        );

        // Normalise result rows
        $listings = array_map(function ($r) {
            $arr = (array) $r;
            if (empty($arr['cover_image']))  $arr['cover_image']  = null;
            if (isset($arr['avg_rating']))   $arr['avg_rating']   = round((float) $arr['avg_rating'],   1);
            if (isset($arr['distance_km']))  $arr['distance_km']  = round((float) $arr['distance_km'],  1);
            if (isset($arr['relevance_score'])) $arr['relevance_score'] = round((float) $arr['relevance_score'], 2);
            return $arr;
        }, $rows);

        return $this->response->setJSON([
            'success'      => true,
            'query'        => $q,
            'type'         => $type,
            'sort'         => $sort,
            'category_id'  => $categoryId,
            'subcategory_ids' => $subcategoryIdArr,
            'listings'     => $listings,
            'total'        => $total,
            'page'         => $page,
            'total_pages'  => $total > 0 ? (int) ceil($total / $limit) : 1,
            'has_more'     => ($offset + $limit) < $total,
            'radius_km'    => $radius,
            'date_filter'  => $dateFilter,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // SEARCH PAGE — GET /search
    // Rendered search results page (SSR + AJAX hybrid)
    // ─────────────────────────────────────────────────────────
    public function searchPage(): string
    {
        $loc        = $this->resolveLocation();
        $q          = trim((string) ($this->request->getGet('q') ?? ''));
        $type       = $this->request->getGet('type')     ?? null;
        $sort       = $this->request->getGet('sort')     ?? 'relevancy';
        $radius     = (float) ($this->request->getGet('radius') ?? self::DEFAULT_RADIUS_KM);
        $page       = max(1, (int) ($this->request->getGet('page') ?? 1));
        $categoryId = $this->request->getGet('category') !== null
                        ? (int) $this->request->getGet('category')
                        : null;
        $subcategoryId = $this->request->getGet('subcategory');
        if (is_string($subcategoryId)) {
            $subcategoryId = explode(',', $subcategoryId);
        }
        $subcategoryIdArr = array_filter(array_map('intval', (array)$subcategoryId));
        $dateFilter = $this->request->getGet('date_filter');
        $perPage    = 12;
        $offset     = ($page - 1) * $perPage;

        $listingModel  = new ListingModel();
        $categoryModel = new CategoryModel();

        $listings   = [];
        $total      = 0;

        // Only query if we have a keyword or a location
        if ($q !== '' || $loc['set']) {
            $rows = $listingModel->search(
                $q,
                $type,
                $loc['lat'],
                $loc['lng'],
                $radius,
                $sort,
                $categoryId,
                $perPage,
                $offset,
                $subcategoryIdArr,
                $dateFilter
            );
            $listings = array_map(fn($r) => (array) $r, $rows);
            $total    = $listingModel->countSearch(
                $q,
                $type,
                $loc['lat'],
                $loc['lng'],
                $radius,
                $categoryId,
                $subcategoryIdArr,
                $dateFilter
            );
        }

        $subcategories = [];
        if ($categoryId) {
            $subcategories = (new \App\Models\SubcategoryModel())
                            ->where('category_id', $categoryId)
                            ->where('status', 'active')
                            ->orderBy('name', 'ASC')
                            ->findAll();
        }

        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();
        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

        return view('frontend/search', [
            'page_title'        => $q ? "Search: \"{$q}\" | Class Next Door" : 'Search Classes | Class Next Door',
            'meta_description'  => "Search results for \"{$q}\" — find the best kids' classes near you.",
            'show_location_bar' => true,
            'selected_location' => $loc['name'],
            'location_selected' => $loc['set'],
            'query'             => $q,
            'listings'          => $listings,
            'listings_total'    => $total,
            'current_type'      => $type,
            'current_sort'      => $sort,
            'current_category'  => $categoryId,
            'current_subcategory'=> $subcategoryIdArr,
            'subcategories'     => array_map(fn($s) => (array) $s, $subcategories),
            'categories'        => array_map(fn($c) => (array) $c, $categories),
            'radius'            => $radius,
            'location_state'    => $loc['state'],
            'current_date_filter'=> $dateFilter,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // LISTING DETAIL PAGE — GET /classes/:id
    // ─────────────────────────────────────────────────────────
    public function listingDetail(int $id): string
    {
        $loc  = $this->resolveLocation();
        $model = new ListingModel();

        $data = $model->getDetail(
            $id,
            $loc['lat'],
            $loc['lng']
        );

        if (!$data) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(
                "Listing #{$id} not found or not yet published."
            );
        }

        $l = $data['listing'];

        // Enrolment and Review status
        $user      = session()->get('cnd_user');
        $userId    = $user['id'] ?? null;
        $userPhone = $user['phone'] ?? get_cookie('cnd_phone');
        
        $bookingModel = new \App\Models\BookingModel();
        $reviewModel  = new \App\Models\ReviewModel();

        $enrolment = $bookingModel->checkEnrolment($id, $userId, $userPhone);
        $hasReviewed = $reviewModel->hasReviewed($id, $userId, $userPhone);

        return view('frontend/detail', [
            'page_title'        => esc($l['title']) . ' | Class Next Door',
            'meta_description'  => mb_substr(strip_tags($l['description'] ?? ''), 0, 160),
            'show_location_bar' => true,
            'selected_location' => $loc['name'],
            'location_selected' => $loc['set'],
            'listing'           => $l,
            'images'            => $data['images'],
            'reviews'           => $data['reviews'],
            'slots'             => $data['slots'],
            'location_state'    => $loc['state'],
            'email_verified'    => session()->get('cnd_email_verified') === true,
            'is_enrolled'       => !empty($enrolment),
            'has_reviewed'      => $hasReviewed,
            'enrolment_data'    => $enrolment
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // LISTING DETAIL API — GET /api/listings/:id
    // Returns full listing detail as JSON.
    // ─────────────────────────────────────────────────────────
    public function listingDetailApi(int $id)
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $lat = $this->request->getGet('lat') !== null ? (float)$this->request->getGet('lat') : null;
        $lng = $this->request->getGet('lng') !== null ? (float)$this->request->getGet('lng') : null;

        $model = new ListingModel();
        $data  = $model->getDetail($id, $lat, $lng);

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Listing not found or not published.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'listing' => $data['listing'],
            'images'  => $data['images'],
            'reviews' => $data['reviews'],
            'slots'   => $data['slots'],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // CONTACT US — GET /contact
    // ─────────────────────────────────────────────────────────
    public function contact(): string
    {
        return view('frontend/contact', [
            'page_title'       => 'Contact Us | Class Next Door',
            'meta_description' => 'Get in touch with the Class Next Door team for support or feedback.',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // CONTACT SUBMIT — POST /contact/submit
    // ─────────────────────────────────────────────────────────
    // ─────────────────────────────────────────────────────────
    // LOCATION SET — POST /set-location (AJAX)
    // ─────────────────────────────────────────────────────────
    public function setLocation()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $name  = $this->request->getPost('location_name');
        $lat   = $this->request->getPost('lat');
        $lng   = $this->request->getPost('lng');
        $state = $this->request->getPost('state') ?? '';   // Indian state from reverse geocoder

        if (empty($name)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Location name is required.']);
        }

        // Persist in session
        session()->set('cnd_location_name', $name);
        session()->set('cnd_lat',   $lat);
        session()->set('cnd_lng',   $lng);
        session()->set('cnd_state', $state);

        // Also set cookies (7-day expiry) for cross-session persistence
        $expiry = strtotime('+7 days');
        setcookie('cnd_location_name', $name,  $expiry, '/');
        setcookie('cnd_lat',           $lat,   $expiry, '/');
        setcookie('cnd_lng',           $lng,   $expiry, '/');
        setcookie('cnd_state',         $state, $expiry, '/');

        return $this->response->setJSON([
            'success'       => true,
            'location_name' => $name,
            'state'         => $state,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // WEB PUSH: Subscribe / Unsubscribe
    // ─────────────────────────────────────────────────────────

    /**
     * POST /api/push/subscribe
     * Body (JSON): { phone, subscription: { endpoint, keys: { p256dh, auth } } }
     */
    public function pushSubscribe()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $input = $this->request->getJSON(true);
        $phone = trim($input['phone'] ?? '');
        $sub   = $input['subscription'] ?? null;

        if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid phone.']);
        }

        if (!$sub || empty($sub['endpoint']) || empty($sub['keys']['p256dh']) || empty($sub['keys']['auth'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Incomplete subscription object.']);
        }

        // Require that this phone was OTP-verified during booking in this session
        $session  = session();
        $verified = $session->get('cnd_push_verified_' . md5($phone));
        if (!$verified) {
            $pending = $session->get('cnd_pending_booking');
            if (!$pending || ($pending['phone'] ?? '') !== $phone) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Phone OTP verification required before subscribing.',
                ]);
            }
        }

        $model = new \App\Models\PushSubscriptionModel();
        $model->upsert(
            $phone,
            $sub,
            null,
            substr($this->request->getUserAgent()->getAgentString(), 0, 300)
        );

        $session->set('cnd_push_verified_' . md5($phone), time() + 604800);

        return $this->response->setJSON(['success' => true]);
    }

    public function pushUnsubscribe()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $input = $this->request->getJSON(true);
        $phone = trim($input['phone'] ?? '');

        if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid phone.']);
        }

        $model = new \App\Models\PushSubscriptionModel();
        $model->deleteByPhone($phone);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * GET /activity
     * Shows upcoming and past classes for the user / phone session.
     */
    public function activity()
    {
        $session = session();
        $userId  = $session->get('user_id');
        $phone   = $session->get('cnd_phone') ?? $_COOKIE['cnd_phone'] ?? null;

        // If no identity, show a simplified "login" or prompt
        if (!$userId && !$phone) {
            return view('frontend/activity_login');
        }

        $bookingModel     = new \App\Models\BookingModel();
        $transactionModel = new \App\Models\TransactionModel();
        
        $data = [
            'page_title' => 'My Classes — Class Next Door',
            'upcoming'   => $bookingModel->getUpcoming($userId, $phone),
            'completed'  => $bookingModel->getCompleted($userId, $phone),
            'payments'   => $userId ? $transactionModel->getByUser($userId, 'payment') : [],
            'phone'      => $phone,
            'active_tab' => $this->request->getGet('tab') ?? 'upcoming',
        ];

        return view('frontend/activity', $data);
    }




    /**
     * POST /contact/submit  or  POST /api/feedback
     */
    public function contactSubmit()
    {
        $isAjax = $this->request->isAJAX();
        $input  = $isAjax ? $this->request->getJSON(true) : $this->request->getPost();

        if (! $this->validate([
            'name'    => 'required|min_length[2]|max_length[100]',
            'email'   => 'required|valid_email',
            'subject' => 'required',
            'message' => 'required|min_length[10]|max_length[2000]',
            'phone'   => 'permit_empty|regex_match[/^[6-9][0-9]{9}$/]',
        ], $input)) {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Store in feedbacks table
        $feedbackModel = new FeedbackModel();
        $feedbackModel->create([
            'user_id' => session()->get('user_id') ?? null,
            'message' => sprintf(
                "[Subject: %s]\n[Name: %s] [Email: %s] [Phone: %s]\n\n%s",
                $input['subject'],
                $input['name'],
                $input['email'],
                $input['phone'] ?: 'N/A',
                $input['message']
            ),
            'status'  => 'new',
        ]);

        if ($isAjax) {
            // ── Admin Notification (Subtask 3.1) ──
            $adminPhone = env('ADMIN_PHONE');
            $adminEmail = env('ADMIN_EMAIL');
            $notify     = new \App\Services\NotificationService();
            $emailSvc   = new \App\Services\EmailService();

            if ($adminPhone) {
                $notify->notifyAdminNewFeedback($adminPhone, $input['name'], $input['subject']);
            }
            if ($adminEmail) {
                $emailSvc->sendHTML($adminEmail, "ALERT: New Customer Feedback", "
                    <h3>New Feedback Received</h3>
                    <p><strong>From:</strong> {$input['name']} ({$input['email']})</p>
                    <p><strong>Subject:</strong> {$input['subject']}</p>
                    <hr>
                    <p>" . nl2br(esc($input['message'])) . "</p>
                ");
            }

            return $this->response->setJSON(['success' => true, 'message' => "Thank you! We've received your message."]);
        }

        return redirect()->to('contact')->with('success', "Thank you! Your message has been sent. We'll get back to you within 24 hours.");
    }

    /**
     * AJAX API: Get subcategories by category ID (Public)
     */
    public function getSubcategories()
    {
        $catId = $this->request->getGet('category_id');
        if (!$catId) {
            return $this->response->setJSON(['success' => false, 'data' => []]);
        }

        $model = new \App\Models\SubcategoryModel();
        $subs  = $model->getByCategory((int)$catId);

        return $this->response->setJSON(['success' => true, 'data' => $subs]);
    }
}

