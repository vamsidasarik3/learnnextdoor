<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * ListingModel
 * Table: listings
 *
 * Covers: CRUD, provider queries, category join, and Haversine
 * distance-based location filtering (stub — wired in Module 2).
 */
class ListingModel extends BaseModel
{
    protected $table      = 'listings';
    protected $primaryKey = 'id';
    protected $returnType = 'object';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'provider_id', 'created_by_provider_id', 'category_id', 'subcategory_id', 'title', 'description', 'type',
        'address', 'state', 'latitude', 'longitude', 'city', 'locality', 'pincode', 'formatted_address',
        'price', 'price_type', 'price_breakdown', 'free_trial',
        'registration_end_date', 'early_bird_date',
        'early_bird_slots', 'early_bird_price',
        'experience', 'social_links', 'linkedin_url',
        // Schedule fields — type-specific
        'start_date',  // Regular: class start date; Course: course start date
        'end_date',    // Course only: course end date
        'class_time',  // Regular & Course: daily scheduled time (e.g. 09:00:00)
        'class_end_time',
        'status', 'review_status', 'payment', 'admin_remarks', 'total_students', 'batch_size', 'batches',
        'course_duration', 'course_duration_type', 'instructor_id', 'instructor_name', 'instructor_kyc_status', 'instructor_kyc_doc', 'institute_name', 'manual_address'
    ];

    // ── Scoped Queries ──────────────────────────────────────────

    /** All listings for a given provider */
    public function getByProvider(int $providerId): array
    {
        return $this->where('provider_id', $providerId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /** All listings for a provider with category and student count */
    public function getByProviderWithStats(int $providerId): array
    {
        return $this->db->table('listings l')
            ->select('l.*, c.name AS category_name, 
                      (SELECT GROUP_CONCAT(sc.name SEPARATOR ", ") FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                      (SELECT COUNT(b.id) FROM bookings b WHERE b.listing_id = l.id AND b.payment_status = "paid") AS student_count')
            ->join('categories c', 'c.id = l.category_id', 'left')
            ->where('l.provider_id', $providerId)
            ->orderBy('l.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getWithCategory(int $id): ?object
    {
        $row = $this->db->table('listings l')
            ->select('l.*, c.name AS category_name, 
                      (SELECT GROUP_CONCAT(sc.name SEPARATOR ", ") FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                      (SELECT GROUP_CONCAT(lsc.subcategory_id) FROM listing_subcategories lsc WHERE lsc.listing_id = l.id) AS subcategory_ids')
            ->join('categories c', 'c.id = l.category_id', 'left')
            ->where('l.id', $id)
            ->get()
            ->getRowObject();

        if ($row) {
            if (!empty($row->batches)) {
                $row->batches = json_decode($row->batches, true) ?? [];
            } else {
                $row->batches = [];
            }

            // Fallback for regular classes: if no batches defined, create 1 default batch from base fields
            if ($row->type === 'regular' && empty($row->batches)) {
                $row->batches = [[
                    'name'             => 'Weekly Batch',
                    'from_time'        => $row->class_time ?? null,
                    'to_time'          => $row->class_end_time ?? null,
                    'price'            => (float)($row->price ?? 0),
                    'max_students'     => (int)($row->max_students ?? 0),
                    'batch_start_date' => $row->start_date ?? null
                ]];
            }
        }

        return $row;
    }

    /**
     * Listings filtered by type and status with optional
     * Haversine distance ordering (sorted by distance if lat/lng provided).
     *
     * @param  string      $type      'regular' | 'workshop' | 'course'
     * @param  float|null  $lat       User latitude
     * @param  float|null  $lng       User longitude
     * @param  float       $radiusKm  Search radius in km
     * @param  string      $sort      'distance' | 'rating' | 'price_asc' | 'price_desc' | 'relevancy'
     * @param  int         $limit
     * @param  int         $offset
     * @return array
     */
    public function getByLocation(
        string $type       = 'regular',
        ?float $lat        = null,
        ?float $lng        = null,
        float  $radiusKm   = 25,
        string $sort       = 'relevancy',
        int    $limit      = 20,
        int    $offset     = 0,
        ?int   $categoryId = null,
        ?array $subcategoryIds = null,
        ?string $dateFilter    = null
    ): array {
        $builder = $this->db->table('listings l')
            ->select('l.*, c.name AS category_name, 
                      (SELECT GROUP_CONCAT(sc.name SEPARATOR ", ") FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                      u.is_verified             AS provider_verified,
                      COALESCE(AVG(r.rating), 0) AS avg_rating,
                      COUNT(r.id)               AS review_count')
            ->join('categories c', 'c.id = l.category_id', 'left')
            ->join('users u',      'u.id = l.provider_id', 'left')
            ->join('reviews r',    'r.listing_id = l.id', 'left')
            ->where('l.type',          $type)
            ->where('l.status',        'active')
            ->where('l.review_status', 'approved')
            ->where('l.payment',       'success')
            ->groupBy('l.id');

        // Debug logging for missing records
        $this->logActiveListingStats($type, $lat, $lng, $radiusKm, $categoryId);

        // Optional category filter
        if ($categoryId !== null) {
            $builder->where('l.category_id', $categoryId);
        }

        if (!empty($subcategoryIds)) {
            $builder->join('listing_subcategories lsc_filter', 'lsc_filter.listing_id = l.id');
            $builder->whereIn('lsc_filter.subcategory_id', $subcategoryIds);
        }

        // Date Filter
        if ($dateFilter === 'this_week') {
            $builder->where('l.start_date >=', date('Y-m-d'));
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
        } elseif ($dateFilter === 'this_weekend') {
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
            $builder->where('l.start_date >=', date('Y-m-d', strtotime('friday this week')));
        }

        // Haversine distance filter (when location is provided)
        if ($lat !== null && $lng !== null) {
            $builder->select(
                "(6371 * ACOS(
                    COS(RADIANS({$lat})) * COS(RADIANS(l.latitude)) *
                    COS(RADIANS(l.longitude) - RADIANS({$lng})) +
                    SIN(RADIANS({$lat})) * SIN(RADIANS(l.latitude))
                )) AS distance_km"
            );
            $builder->having('distance_km <=', $radiusKm);
        }

        // Sorting — relevancy uses composite score
        switch ($sort) {
            case 'distance':
                if ($lat !== null && $lng !== null) {
                    $builder->orderBy('distance_km', 'ASC')->orderBy('avg_rating', 'DESC');
                } else {
                    $builder->orderBy('l.total_students', 'DESC');
                }
                break;
            case 'rating':
                $builder->orderBy('avg_rating',  'DESC')->orderBy('review_count', 'DESC');
                break;
            case 'price_asc':
                $builder->orderBy('l.price',     'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('l.price',     'DESC');
                break;
            default:
                $builder->orderBy('l.total_students', 'DESC')->orderBy('avg_rating', 'DESC');
                break;
        }

        return $builder->limit($limit, $offset)->get()->getResultObject();
    }

    /** Count listings matching getByLocation filter (for pagination) */
    public function countByLocation(
        string $type       = 'regular',
        ?float $lat        = null,
        ?float $lng        = null,
        float  $radiusKm   = 25,
        ?int   $categoryId = null,
        ?array $subcategoryIds = null,
        ?string $dateFilter    = null
    ): int {
        $builder = $this->db->table('listings l')
            ->select('l.id') // Ensure at least one column is selected to avoid syntax error
            ->where('l.type',          $type)
            ->where('l.status',        'active')
            ->where('l.review_status', 'approved')
            ->where('l.payment',       'success')
            ->groupBy('l.id'); // Group to avoid duplicate counts from joins

        if ($categoryId !== null) {
            $builder->where('l.category_id', $categoryId);
        }

        // Date Filter
        if ($dateFilter === 'this_week') {
            $builder->where('l.start_date >=', date('Y-m-d'));
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
        } elseif ($dateFilter === 'this_weekend') {
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
            $builder->where('l.start_date >=', date('Y-m-d', strtotime('friday this week')));
        }

        if ($lat !== null && $lng !== null) {
            $builder->select(
                "(6371 * ACOS(
                    COS(RADIANS({$lat})) * COS(RADIANS(l.latitude)) *
                    COS(RADIANS(l.longitude) - RADIANS({$lng})) +
                    SIN(RADIANS({$lat})) * SIN(RADIANS(l.latitude))
                )) AS distance_km"
            );
            $builder->having('distance_km <=', $radiusKm);
        }

        // Use subquery to correctly count results with HAVING filter or GROUP BY
        $sql = $builder->getCompiledSelect();
        $result = $this->db->query("SELECT COUNT(*) AS n FROM ({$sql}) sub");
        return (int) ($result->getRow()->n ?? 0);
    }

    /** Listings pending admin review */
    public function getPendingReview(): array
    {
        return $this->where('review_status', 'pending')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /** Increment total_students counter after confirmed booking */
    public function incrementStudents(int $listingId): void
    {
        $this->db->query(
            'UPDATE listings SET total_students = total_students + 1 WHERE id = ?',
            [$listingId]
        );
    }

    // ── SEARCH ──────────────────────────────────────────────────

    /**
     * Full-text keyword search with optional location filter, category
     * filter, type filter, and all sort modes.
     *
     * Relevancy scoring (SQL computed column `relevance_score`):
     *   title match (exact word)  × 3
     *   category match            × 2
     *   description match         × 1
     *   popular boost             = LEAST(total_students / 10, 5)
     *   rating boost              = avg_rating
     *
     * @param  string      $query      Keyword(s) from search bar
     * @param  string|null $type       'regular'|'workshop'|'course'|null (all)
     * @param  float|null  $lat        User latitude
     * @param  float|null  $lng        User longitude
     * @param  float       $radiusKm   Search radius
     * @param  string      $sort       'relevancy'|'distance'|'rating'|'price_asc'|'price_desc'
     * @param  int|null    $categoryId Optional category filter
     * @param  int         $limit
     * @param  int         $offset
     * @return array
     */
    public function search(
        string  $query      = '',
        ?string $type       = null,
        ?float  $lat        = null,
        ?float  $lng        = null,
        float   $radiusKm   = 25,
        string  $sort       = 'relevancy',
        ?int    $categoryId = null,
        int     $limit      = 20,
        int     $offset     = 0,
        ?array  $subcategoryIds = null,
        ?string $dateFilter     = null
    ): array {
        $safeQ = $this->db->escapeLikeString(trim($query));

        // Relevancy score: weighted keyword match + popularity + rating
        $relevanceExpr = "
            (
              (CASE WHEN l.title       LIKE '%{$safeQ}%' THEN 3 ELSE 0 END) +
              (CASE WHEN c.name        LIKE '%{$safeQ}%' THEN 2 ELSE 0 END) +
              (CASE WHEN sc.name       LIKE '%{$safeQ}%' THEN 2 ELSE 0 END) +
              (CASE WHEN l.description LIKE '%{$safeQ}%' THEN 1 ELSE 0 END) +
              LEAST(l.total_students / 10, 5) +
              COALESCE(AVG(r.rating), 0)
            ) AS relevance_score
        ";

        $builder = $this->db->table('listings l')
            ->select('l.*, c.name AS category_name, 
                      (SELECT GROUP_CONCAT(sc.name SEPARATOR ", ") FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                      COALESCE(AVG(r.rating), 0) AS avg_rating,
                      COUNT(r.id)               AS review_count,
                      li.image_path             AS cover_image,
                      u.is_verified             AS provider_verified')
            ->select($relevanceExpr)
            ->join('categories c',     'c.id = l.category_id',                'left')
            ->join('users u',          'u.id = l.provider_id',                'left')
            ->join('reviews r',        'r.listing_id = l.id',                 'left')
            ->join('listing_images li','li.listing_id = l.id AND li.position = 0', 'left')
            ->where('l.status',        'active')
            ->where('l.review_status', 'approved')
            ->where('l.payment',       'success')
            ->groupBy('l.id');

        // Keyword filter — must match at least one field
        if ($query !== '') {
            $builder->groupStart()
                ->like('l.title',         $safeQ, 'both', false)
                ->orLike('l.description', $safeQ, 'both', false)
                ->orLike('c.name',          $safeQ, 'both', false)
                ->orLike('sc.name',         $safeQ, 'both', false)
                ->orLike('l.address',       $safeQ, 'both', false)
            ->groupEnd();
        }

        // Type filter
        if ($type !== null && in_array($type, ['regular', 'workshop', 'course'])) {
            $builder->where('l.type', $type);
        }

        // Category filter
        if ($categoryId !== null) {
            $builder->where('l.category_id', $categoryId);
        }

        // Date Filter
        if ($dateFilter === 'this_week') {
            $builder->where('l.start_date >=', date('Y-m-d'));
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
        } elseif ($dateFilter === 'this_weekend') {
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
            $builder->where('l.start_date >=', date('Y-m-d', strtotime('friday this week')));
        }

        // Location / Haversine
        if ($lat !== null && $lng !== null) {
            $builder->select(
                "(6371 * ACOS(
                    COS(RADIANS({$lat})) * COS(RADIANS(l.latitude)) *
                    COS(RADIANS(l.longitude) - RADIANS({$lng})) +
                    SIN(RADIANS({$lat})) * SIN(RADIANS(l.latitude))
                )) AS distance_km"
            );
            $builder->having('distance_km <=', $radiusKm);
        }

        // Sorting
        switch ($sort) {
            case 'distance':
                if ($lat !== null) {
                    $builder->orderBy('distance_km', 'ASC');
                } else {
                    $builder->orderBy('relevance_score', 'DESC');
                }
                break;
            case 'rating':
                $builder->orderBy('avg_rating', 'DESC')->orderBy('review_count', 'DESC');
                break;
            case 'price_asc':
                $builder->orderBy('l.price', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('l.price', 'DESC');
                break;
            default:
                $builder->orderBy('relevance_score', 'DESC');
                break;
        }

        return $builder->limit($limit, $offset)->get()->getResultObject();
    }

    /**
     * Count results for `search()` — same filters, returns int.
     */
    public function countSearch(
        string  $query      = '',
        ?string $type       = null,
        ?float  $lat        = null,
        ?float  $lng        = null,
        float   $radiusKm   = 25,
        ?int    $categoryId = null,
        ?array  $subcategoryIds = null,
        ?string $dateFilter     = null
    ): int {
        $safeQ = $this->db->escapeLikeString(trim($query));

        $builder = $this->db->table('listings l')
            ->select('l.id') // Ensure at least one column is selected to avoid syntax error
            ->join('categories c',     'c.id = l.category_id',    'left')
            ->where('l.status',        'active')
            ->where('l.review_status', 'approved')
            ->where('l.payment',       'success')
            ->groupBy('l.id'); // Group to avoid duplicate counts from joins

        if ($query !== '') {
            $builder->groupStart()
                ->like('l.title',         $safeQ, 'both', false)
                ->orLike('l.description', $safeQ, 'both', false)
                ->orLike('c.name',          $safeQ, 'both', false)
                ->orLike('l.address',       $safeQ, 'both', false)
            ->groupEnd();
        }

        if ($type !== null && in_array($type, ['regular', 'workshop', 'course'])) {
            $builder->where('l.type', $type);
        }

        if ($categoryId !== null) {
            $builder->where('l.category_id', $categoryId);
        }

        // Date Filter
        if ($dateFilter === 'this_week') {
            $builder->where('l.start_date >=', date('Y-m-d'));
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
        } elseif ($dateFilter === 'this_weekend') {
            $builder->where('l.start_date <=', date('Y-m-d', strtotime('sunday this week')));
            $builder->where('l.start_date >=', date('Y-m-d', strtotime('friday this week')));
        }

        if ($lat !== null && $lng !== null) {
            $builder->select(
                "(6371 * ACOS(
                    COS(RADIANS({$lat})) * COS(RADIANS(l.latitude)) *
                    COS(RADIANS(l.longitude) - RADIANS({$lng})) +
                    SIN(RADIANS({$lat})) * SIN(RADIANS(l.latitude))
                )) AS distance_km"
            );
            $builder->having('distance_km <=', $radiusKm);
        }

        // Use subquery count trick for HAVING-filtered or GROUPed results
        $sql = $builder->getCompiledSelect();
        $result = $this->db->query("SELECT COUNT(*) AS n FROM ({$sql}) sub");
        return (int) ($result->getRow()->n ?? 0);
    }

    // ── DETAIL ──────────────────────────────────────────────────

    /**
     * Rich single-listing fetch for the detail page and API.
     * Returns an array with keys:
     *   listing    — all listing columns + category_name + provider_name + avg_rating + review_count + cover_image
     *   images     — all listing_images ordered by position
     *   reviews    — all reviews with reviewer name + date
     *   slots      — upcoming listing_availabilities (next 90 days, not disabled)
     *   distance_km — Haversine distance if lat/lng provided, else null
     *
     * Returns null if the listing is not found / not active+approved.
     *
     * @param  int        $id   Listing ID
     * @param  float|null $lat  User latitude (for distance)
     * @param  float|null $lng  User longitude
     */
    public function getDetail(int $id, ?float $lat = null, ?float $lng = null): ?array
    {
        // ── Core listing row ──────────────────────────────────
        $distExpr = ($lat !== null && $lng !== null)
            ? "(6371 * ACOS(
                COS(RADIANS({$lat})) * COS(RADIANS(l.latitude)) *
                COS(RADIANS(l.longitude) - RADIANS({$lng})) +
                SIN(RADIANS({$lat})) * SIN(RADIANS(l.latitude))
              )) AS distance_km"
            : "NULL AS distance_km";

        $row = $this->db->table('listings l')
            ->select("
                l.*,
                c.name          AS category_name,
                (SELECT GROUP_CONCAT(sc.name SEPARATOR ', ') FROM listing_subcategories lsc JOIN subcategories sc ON sc.id = lsc.subcategory_id WHERE lsc.listing_id = l.id) AS subcategory_names,
                u.name          AS provider_name,
                u.phone         AS provider_phone,
                u.is_verified   AS provider_verified,
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(r.id)               AS review_count,
                li.image_path             AS cover_image,
                {$distExpr}
            ")
            ->join('categories c',      'c.id = l.category_id',                    'left')
            ->join('users u',           'u.id = l.provider_id',                    'left')
            ->join('reviews r',         'r.listing_id = l.id',                     'left')
            ->join('listing_images li', 'li.listing_id = l.id AND li.position = 0','left')
            ->where('l.id',            $id)
            ->where('l.status',        'active')
            ->where('l.review_status', 'approved')
            ->where('l.payment',       'success')
            ->groupBy('l.id')
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        // Normalise floats
        $row['avg_rating']  = round((float)($row['avg_rating']  ?? 0), 1);
        $row['distance_km'] = ($row['distance_km'] !== null)
            ? round((float)$row['distance_km'], 1)
            : null;
        $row['price']       = (float)($row['price'] ?? 0);

        // Decode JSON fields
        if (!empty($row['price_breakdown'])) {
            $row['price_breakdown'] = json_decode($row['price_breakdown'], true) ?? [];
        }

        if (!empty($row['batches'])) {
            $row['batches'] = json_decode($row['batches'], true) ?? [];
        } else {
            $row['batches'] = [];
        }

        // Fallback for regular classes: if no batches defined, create 1 default batch from base fields
        if ($row['type'] === 'regular' && empty($row['batches'])) {
            $row['batches'] = [[
                'name'             => 'Weekly Batch',
                'from_time'        => $row['class_time'] ?? null,
                'to_time'          => $row['class_end_time'] ?? null,
                'price'            => (float)($row['price'] ?? 0),
                'batch_size'       => (int)($row['batch_size'] ?? 0),
                'batch_start_date' => $row['start_date'] ?? null
            ]];
        }

        // ── All images ────────────────────────────────────────
        $images = $this->db->table('listing_images')
            ->where('listing_id', $id)
            ->orderBy('position', 'ASC')
            ->get()
            ->getResultArray();

        // ── Reviews (with reviewer name) ──────────────────────
        $reviews = $this->db->table('reviews rv')
            ->select('rv.*, u.name AS reviewer_name')
            ->join('users u', 'u.id = rv.user_id', 'left')
            ->where('rv.listing_id', $id)
            ->orderBy('rv.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        // ── Upcoming availability slots (next 90 days) ────────
        $slots = $this->db->table('listing_availabilities')
            ->where('listing_id',  $id)
            ->where('is_disabled', 0)
            ->where('available_date >=', date('Y-m-d'))
            ->where('available_date <=', date('Y-m-d', strtotime('+90 days')))
            ->orderBy('available_date', 'ASC')
            ->orderBy('available_time', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'listing'  => $row,
            'images'   => $images,
            'reviews'  => $reviews,
            'slots'    => $slots,
        ];
    }

    /**
     * Save multiple subcategories for a listing
     */
    public function saveSubcategories(int $listingId, array $subcategoryIds)
    {
        $db = \Config\Database::connect();
        $db->table('listing_subcategories')->where('listing_id', $listingId)->delete();

        if (!empty($subcategoryIds)) {
            $batchData = [];
            foreach ($subcategoryIds as $sid) {
                if (!$sid) continue;
                $batchData[] = [
                    'listing_id'     => $listingId,
                    'subcategory_id' => (int)$sid,
                    'created_at'     => date('Y-m-d H:i:s'),
                ];
            }
            if (!empty($batchData)) {
                $db->table('listing_subcategories')->insertBatch($batchData);
            }
        }
    }

    /**
     * Internal diagnostic logging to debug why some listings might be hidden.
     */
    protected function logActiveListingStats($type, $lat, $lng, $radius, $catId)
    {
        $db = \Config\Database::connect();
        
        // 1. Total records
        $total = $db->table('listings')->countAllResults();
        
        // 2. Filter breakdown
        $active   = $db->table('listings')->where('status', 'active')->countAllResults();
        $approved = $db->table('listings')->where('review_status', 'approved')->countAllResults();
        $paid     = $db->table('listings')->where('payment', 'success')->countAllResults();
        
        $fullyValid = $db->table('listings')
            ->where('status', 'active')
            ->where('review_status', 'approved')
            ->where('payment', 'success')
            ->countAllResults();

        // 3. Location filter (if applicable)
        $locFail = 0;
        if ($lat && $lng) {
            $sql = "SELECT COUNT(*) as cnt FROM listings l 
                    WHERE status='active' AND review_status='approved' AND payment='success'
                    AND (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) > ?";
            $res = $db->query($sql, [$lat, $lng, $lat, $radius])->getRow();
            $locFail = $res->cnt ?? 0;
        }

        log_message('debug', "Listing Stats: Total=$total, Active=$active, Approved=$approved, Paid=$paid, Valid=$fullyValid, HiddenByLocation=$locFail");
        
        // Save to temporary file for immediate check
        $logStr = sprintf("[%s] Total=%d, Active=%d, Approved=%d, Paid=%d, Valid=%d, HiddenByLoc=%d (Radius=%d)\n", 
                          date('Y-m-d H:i:s'), $total, $active, $approved, $paid, $fullyValid, $locFail, $radius);
        file_put_contents(WRITEPATH . 'logs/listing_debug.log', $logStr, FILE_APPEND);
    }
}

