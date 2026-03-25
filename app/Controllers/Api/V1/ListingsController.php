<?php

namespace App\Controllers\Api\V1;

use App\Models\ListingModel;
use App\Models\CategoryModel;
use App\Models\FeaturedCarouselModel;
use App\Services\CarouselService;

/**
 * ListingsController — /v1/listings
 * ─────────────────────────────────────────────────────────────────────
 * Versioned RESTful API for listing discovery.
 *
 * Routes (all under /v1/):
 *   GET  listings             Browse / filter listings with pagination
 *   GET  listings/{id}        Single listing detail
 *   GET  listings/search      Full-text + location search
 *   GET  listings/carousel    Featured carousel slides
 *   GET  categories           All available categories
 *
 * Query Parameters — GET /v1/listings
 *   type        : regular | workshop | course   (default: regular)
 *   category    : integer category ID           (optional)
 *   sort        : relevancy | rating | price_asc | price_desc (default: relevancy)
 *   lat, lng    : float coordinates             (optional)
 *   radius      : km radius                     (default: 25, max: 200)
 *   page        : int page number               (default: 1)
 *   per_page    : int items per page            (default: 12, max: 40)
 *
 * Query Parameters — GET /v1/listings/search
 *   q           : search keyword(s)             (required)
 *   + all params from listings above
 *
 * Query Parameters — GET /v1/listings/carousel
 *   state       : Indian state name             (optional)
 *   lat, lng    : float coordinates             (optional)
 *   radius      : km                            (default: 25)
 */
class ListingsController extends ApiBaseController
{
    protected $helpers = ['basic', 'url'];

    /** Default search radius km */
    private const DEFAULT_RADIUS = 25;

    // ─── GET /v1/listings ────────────────────────────────────────────

    /**
     * @OA\Get(
     *   path="/v1/listings",
     *   summary="Browse listings",
     *   tags={"Listings"},
     *   @OA\Parameter(name="type",     in="query", schema={"type":"string","enum":["regular","workshop","course"]}),
     *   @OA\Parameter(name="category", in="query", schema={"type":"integer"}),
     *   @OA\Parameter(name="sort",     in="query", schema={"type":"string","enum":["relevancy","rating","price_asc","price_desc"]}),
     *   @OA\Parameter(name="lat",      in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="lng",      in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="radius",   in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="page",     in="query", schema={"type":"integer"}),
     *   @OA\Parameter(name="per_page", in="query", schema={"type":"integer"}),
     *   @OA\Response(response=200, description="Paginated listing array")
     * )
     */
    public function index()
    {
        $type       = $this->request->getGet('type')     ?? 'regular';
        $sort       = $this->request->getGet('sort')     ?? 'relevancy';
        $page       = max(1, (int) ($this->request->getGet('page')     ?? 1));
        $perPage    = min(40, max(1, (int) ($this->request->getGet('per_page') ?? 12)));
        $offset     = ($page - 1) * $perPage;
        $radius     = min(200, max(1, (float) ($this->request->getGet('radius') ?? self::DEFAULT_RADIUS)));
        $categoryId = $this->request->getGet('category') !== null
            ? (int) $this->request->getGet('category')
            : null;
        $lat = $this->request->getGet('lat') !== null ? (float) $this->request->getGet('lat') : null;
        $lng = $this->request->getGet('lng') !== null ? (float) $this->request->getGet('lng') : null;

        // Sanitise type
        if (!in_array($type, ['regular', 'workshop', 'course'], true)) {
            $type = 'regular';
        }

        // Sanitise sort
        if (!in_array($sort, ['relevancy', 'rating', 'price_asc', 'price_desc', 'distance'], true)) {
            $sort = 'relevancy';
        }

        $model = new ListingModel();
        $rows  = $model->getByLocation($type, null, null, $radius, $sort, $perPage, $offset, $categoryId);
        $total = $model->countByLocation($type, null, null, $radius, $categoryId);

        $listings = $this->normaliseListings($rows);

        return $this->success(
            $listings,
            array_merge(
                $this->paginationMeta($total, $page, $perPage),
                [
                    'type'        => $type,
                    'sort'        => $sort,
                    'category_id' => $categoryId,
                    'radius_km'   => $radius,
                    'location'    => $lat ? ['lat' => $lat, 'lng' => $lng] : null,
                ]
            )
        );
    }

    // ─── GET /v1/listings/search ──────────────────────────────────────

    /**
     * @OA\Get(
     *   path="/v1/listings/search",
     *   summary="Search listings by keyword",
     *   tags={"Listings"},
     *   @OA\Parameter(name="q",        in="query", required=true, schema={"type":"string"}),
     *   @OA\Parameter(name="type",     in="query", schema={"type":"string"}),
     *   @OA\Parameter(name="category", in="query", schema={"type":"integer"}),
     *   @OA\Parameter(name="sort",     in="query", schema={"type":"string"}),
     *   @OA\Parameter(name="lat",      in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="lng",      in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="radius",   in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="page",     in="query", schema={"type":"integer"}),
     *   @OA\Parameter(name="per_page", in="query", schema={"type":"integer"}),
     *   @OA\Response(response=200, description="Matching listings")
     * )
     */
    public function search()
    {
        $q          = trim((string) ($this->request->getGet('q') ?? ''));
        $type       = $this->request->getGet('type')     ?? null;
        $sort       = $this->request->getGet('sort')     ?? 'relevancy';
        $page       = max(1, (int) ($this->request->getGet('page')     ?? 1));
        $perPage    = min(40, max(1, (int) ($this->request->getGet('per_page') ?? 12)));
        $offset     = ($page - 1) * $perPage;
        $radius     = min(200, max(1, (float) ($this->request->getGet('radius') ?? self::DEFAULT_RADIUS)));
        $categoryId = $this->request->getGet('category') !== null ? (int) $this->request->getGet('category') : null;
        $lat        = $this->request->getGet('lat') !== null ? (float) $this->request->getGet('lat') : null;
        $lng        = $this->request->getGet('lng') !== null ? (float) $this->request->getGet('lng') : null;

        if ($q === '' && $lat === null) {
            return $this->fail(
                'Provide at least a search keyword (q) or coordinates (lat, lng).',
                'missing_parameter'
            );
        }

        if ($type !== null && !in_array($type, ['regular', 'workshop', 'course'], true)) {
            $type = null;
        }
        if (!in_array($sort, ['relevancy', 'distance', 'rating', 'price_asc', 'price_desc'], true)) {
            $sort = 'relevancy';
        }

        $model = new ListingModel();
        $rows  = $model->search($q, $type, null, null, $radius, $sort, $categoryId, $perPage, $offset);
        $total = $model->countSearch($q, $type, null, null, $radius, $categoryId);

        $listings = $this->normaliseListings($rows);

        return $this->success(
            $listings,
            array_merge(
                $this->paginationMeta($total, $page, $perPage),
                [
                    'query'       => $q,
                    'type'        => $type,
                    'sort'        => $sort,
                    'category_id' => $categoryId,
                    'radius_km'   => $radius,
                ]
            )
        );
    }

    // ─── GET /v1/listings/carousel ────────────────────────────────────

    /**
     * @OA\Get(
     *   path="/v1/listings/carousel",
     *   summary="Featured carousel listings",
     *   tags={"Listings"},
     *   @OA\Parameter(name="state",  in="query", schema={"type":"string"}),
     *   @OA\Parameter(name="lat",    in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="lng",    in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="radius", in="query", schema={"type":"number"}),
     *   @OA\Response(response=200, description="Up to 5 featured slides")
     * )
     */
    public function carousel()
    {
        $state  = trim($this->request->getGet('state') ?? '');
        $lat    = $this->request->getGet('lat')    !== null ? (float) $this->request->getGet('lat')    : null;
        $lng    = $this->request->getGet('lng')    !== null ? (float) $this->request->getGet('lng')    : null;
        $radius = min(200, max(5, (float) ($this->request->getGet('radius') ?? 25)));

        if (empty($state)) {
            $state = session()->get('cnd_state') ?? 'ALL';
        }

        $model  = new FeaturedCarouselModel();
        $slides = $model->getCarouselData($state, $lat, $lng, $radius, 5);

        $slides = array_map(function ($row) {
            $row['avg_rating']  = round((float)($row['avg_rating']  ?? 0), 1);
            $row['distance_km'] = isset($row['distance_km']) ? round((float)$row['distance_km'], 1) : null;
            $row['price']       = isset($row['price'])       ? (float)$row['price']              : null;
            return $row;
        }, $slides);

        return $this->success(
            $slides,
            ['state' => $state, 'total' => count($slides)]
        );
    }

    // ─── GET /v1/listings/{id} ────────────────────────────────────────

    /**
     * @OA\Get(
     *   path="/v1/listings/{id}",
     *   summary="Get a single listing detail",
     *   tags={"Listings"},
     *   @OA\Parameter(name="id",  in="path",  required=true, schema={"type":"integer"}),
     *   @OA\Parameter(name="lat", in="query", schema={"type":"number"}),
     *   @OA\Parameter(name="lng", in="query", schema={"type":"number"}),
     *   @OA\Response(response=200, description="Listing detail with images, slots, reviews"),
     *   @OA\Response(response=404, description="Not found")
     * )
     */
    public function show(int $id)
    {
        $lat = $this->request->getGet('lat') !== null ? (float) $this->request->getGet('lat') : null;
        $lng = $this->request->getGet('lng') !== null ? (float) $this->request->getGet('lng') : null;

        $model = new ListingModel();
        $data  = $model->getDetail($id, $lat, $lng);

        if (!$data) {
            return $this->notFound('Listing');
        }

        return $this->success([
            'listing' => $data['listing'],
            'images'  => $data['images'],
            'reviews' => $data['reviews'],
            'slots'   => $data['slots'],
        ]);
    }

    // ─── GET /v1/categories ───────────────────────────────────────────

    /**
     * @OA\Get(
     *   path="/v1/categories",
     *   summary="List all categories",
     *   tags={"Categories"},
     *   @OA\Response(response=200, description="Array of category objects")
     * )
     */
    public function categories()
    {
        $model      = new CategoryModel();
        $categories = $model->orderBy('name', 'ASC')->findAll();

        return $this->success(
            array_map(fn($c) => (array) $c, $categories),
            ['total' => count($categories)]
        );
    }

    // ─── Private helpers ──────────────────────────────────────────────

    private function normaliseListings(array $rows): array
    {
        return array_map(function ($r) {
            $arr = (array) $r;

            // Ensure cover image is set
            if (empty($arr['cover_image'])) {
                $db  = \Config\Database::connect();
                $img = $db->table('listing_images')
                          ->where('listing_id', $arr['id'])
                          ->orderBy('position', 'ASC')
                          ->get()->getRow();
                $arr['cover_image'] = $img ? $img->image_path : null;
            }

            // Round floating-point fields
            if (isset($arr['avg_rating']))      $arr['avg_rating']      = round((float)$arr['avg_rating'],      1);
            if (isset($arr['distance_km']))      $arr['distance_km']     = round((float)$arr['distance_km'],     1);
            if (isset($arr['relevance_score']))  $arr['relevance_score'] = round((float)$arr['relevance_score'], 2);
            if (isset($arr['price']))            $arr['price']           = (float) $arr['price'];

            return $arr;
        }, $rows);
    }
}
