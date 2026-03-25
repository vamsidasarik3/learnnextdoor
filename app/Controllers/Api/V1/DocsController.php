<?php

namespace App\Controllers\Api\V1;

/**
 * DocsController — /v1/docs
 * ─────────────────────────────────────────────────────────────────────
 * Serves:
 *   GET /v1/docs          → Swagger UI HTML page
 *   GET /v1/openapi.json  → OpenAPI 3.0 specification JSON
 */
class DocsController extends ApiBaseController
{
    /**
     * GET /v1/docs — Swagger UI
     */
    public function index(): string
    {
        return view('api/v1/swagger_ui');
    }

    /**
     * GET /v1/openapi.json — OpenAPI 3.0 spec
     */
    public function openApiSpec()
    {
        $spec = $this->buildSpec();

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setBody(json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    // ─── Build the full OpenAPI 3.0 specification ─────────────────────

    private function buildSpec(): array
    {
        $baseUrl = rtrim(base_url(), '/');

        return [
            'openapi' => '3.0.3',
            'info' => [
                'title'       => 'Class Next Door — Public API v1',
                'description' => 'RESTful API for Class Next Door. Provides endpoints for browsing listings, searching, managing bookings, and more. All responses use the standard JSON envelope: `{ success, api_version, timestamp, data?, meta?, error?, message? }`.',
                'version'     => '1.0.0',
                'contact' => [
                    'name'  => 'Class Next Door Support',
                    'email' => env('ADMIN_EMAIL', 'support@classnextdoor.com'),
                    'url'   => $baseUrl,
                ],
                'license' => [
                    'name' => 'Proprietary',
                ],
            ],
            'servers' => [
                ['url' => $baseUrl . '/v1', 'description' => 'API v1 (current)'],
            ],
            'tags' => [
                ['name' => 'Listings',    'description' => 'Browse, search and view class listings'],
                ['name' => 'Categories',  'description' => 'Category taxonomy'],
                ['name' => 'Bookings',    'description' => 'Three-step booking flow'],
                ['name' => 'Auth',        'description' => 'Frontend parent authentication'],
                ['name' => 'Push',        'description' => 'Web Push notification subscriptions'],
                ['name' => 'Location',    'description' => 'User location management'],
                ['name' => 'Email',       'description' => 'Email OTP verification'],
                ['name' => 'Reviews',     'description' => 'Parent reviews and ratings'],
                ['name' => 'Meta',        'description' => 'API metadata and health'],
            ],
            'components' => [
                'schemas'         => $this->schemas(),
                'securitySchemes' => [
                    'sessionAuth' => [
                        'type'        => 'apiKey',
                        'in'          => 'cookie',
                        'name'        => 'ci_session',
                        'description' => 'CodeIgniter 4 session cookie (set automatically after /login).',
                    ],
                ],
            ],
            'paths' => array_merge(
                $this->listingPaths(),
                $this->bookingPaths(),
                $this->authPaths(),
                $this->utilPaths(),
                $this->metaPaths()
            ),
        ];
    }

    // ─── Schema definitions ───────────────────────────────────────────

    private function schemas(): array
    {
        return [
            'ApiResponse' => [
                'type'        => 'object',
                'description' => 'Standard API response envelope',
                'properties'  => [
                    'success'     => ['type' => 'boolean'],
                    'api_version' => ['type' => 'string', 'example' => '1.0'],
                    'timestamp'   => ['type' => 'string', 'format' => 'date-time'],
                    'data'        => ['description' => 'Response payload (type varies by endpoint)'],
                    'meta'        => ['$ref' => '#/components/schemas/PaginationMeta'],
                    'error'       => ['type' => 'string', 'description' => 'Machine-readable error code'],
                    'message'     => ['type' => 'string'],
                    'errors'      => ['type' => 'object', 'description' => 'Validation field errors'],
                ],
            ],
            'PaginationMeta' => [
                'type'       => 'object',
                'properties' => [
                    'total'       => ['type' => 'integer'],
                    'page'        => ['type' => 'integer'],
                    'per_page'    => ['type' => 'integer'],
                    'total_pages' => ['type' => 'integer'],
                    'has_next'    => ['type' => 'boolean'],
                    'has_prev'    => ['type' => 'boolean'],
                ],
            ],
            'Listing' => [
                'type'       => 'object',
                'properties' => [
                    'id'                    => ['type' => 'integer'],
                    'title'                 => ['type' => 'string'],
                    'description'           => ['type' => 'string'],
                    'type'                  => ['type' => 'string', 'enum' => ['regular', 'workshop', 'course']],
                    'price'                 => ['type' => 'number', 'format' => 'float'],
                    'address'               => ['type' => 'string'],
                    'cover_image'           => ['type' => 'string', 'nullable' => true],
                    'avg_rating'            => ['type' => 'number', 'format' => 'float'],
                    'total_reviews'         => ['type' => 'integer'],
                    'student_count'         => ['type' => 'integer'],
                    'status'                => ['type' => 'string', 'enum' => ['active', 'inactive']],
                    'start_date'            => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                    'end_date'              => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                    'class_time'            => ['type' => 'string', 'nullable' => true],
                    'class_end_time'        => ['type' => 'string', 'nullable' => true],
                    'early_bird_price'      => ['type' => 'number', 'nullable' => true],
                    'early_bird_date'       => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                    'registration_end_date' => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                    'distance_km'           => ['type' => 'number', 'nullable' => true],
                    'created_at'            => ['type' => 'string', 'format' => 'date-time'],
                ],
            ],
            'Category' => [
                'type'       => 'object',
                'properties' => [
                    'id'   => ['type' => 'integer'],
                    'name' => ['type' => 'string'],
                    'slug' => ['type' => 'string'],
                    'icon' => ['type' => 'string', 'nullable' => true],
                ],
            ],
            'Booking' => [
                'type'       => 'object',
                'properties' => [
                    'booking_id'     => ['type' => 'integer'],
                    'listing_title'  => ['type' => 'string'],
                    'class_date'     => ['type' => 'string', 'format' => 'date', 'nullable' => true],
                    'class_time'     => ['type' => 'string', 'nullable' => true],
                    'address'        => ['type' => 'string'],
                    'booking_type'   => ['type' => 'string', 'enum' => ['regular', 'trial']],
                    'student_name'   => ['type' => 'string'],
                    'amount'         => ['type' => 'number'],
                    'payment_status' => ['type' => 'string', 'enum' => ['pending', 'paid', 'failed']],
                ],
            ],
            'Error' => [
                'type'       => 'object',
                'properties' => [
                    'success'     => ['type' => 'boolean', 'example' => false],
                    'api_version' => ['type' => 'string'],
                    'timestamp'   => ['type' => 'string', 'format' => 'date-time'],
                    'error'       => ['type' => 'string'],
                    'message'     => ['type' => 'string'],
                ],
            ],
        ];
    }

    // ─── Path definitions ─────────────────────────────────────────────

    private function listingPaths(): array
    {
        $listingParams = [
            ['name' => 'type',     'in' => 'query', 'schema' => ['type' => 'string',  'enum' => ['regular', 'workshop', 'course'], 'default' => 'regular']],
            ['name' => 'category', 'in' => 'query', 'schema' => ['type' => 'integer'], 'description' => 'Category ID filter'],
            ['name' => 'sort',     'in' => 'query', 'schema' => ['type' => 'string',  'enum' => ['relevancy', 'rating', 'price_asc', 'price_desc', 'distance'], 'default' => 'relevancy']],
            ['name' => 'lat',      'in' => 'query', 'schema' => ['type' => 'number'],  'description' => 'User latitude'],
            ['name' => 'lng',      'in' => 'query', 'schema' => ['type' => 'number'],  'description' => 'User longitude'],
            ['name' => 'radius',   'in' => 'query', 'schema' => ['type' => 'number',  'default' => 25, 'minimum' => 1, 'maximum' => 200], 'description' => 'Radius in km'],
            ['name' => 'page',     'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 1,  'minimum' => 1]],
            ['name' => 'per_page', 'in' => 'query', 'schema' => ['type' => 'integer', 'default' => 12, 'minimum' => 1, 'maximum' => 40]],
        ];

        return [
            '/listings' => [
                'get' => [
                    'tags'        => ['Listings'],
                    'summary'     => 'Browse listings',
                    'description' => 'Returns a paginated list of listings filtered by type, category, location, and sort order.',
                    'operationId' => 'listListings',
                    'parameters'  => $listingParams,
                    'responses'   => [
                        '200' => ['description' => 'Paginated listing array', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                    ],
                ],
            ],
            '/listings/search' => [
                'get' => [
                    'tags'        => ['Listings'],
                    'summary'     => 'Search listings by keyword',
                    'description' => 'Full-text keyword search with optional location filtering.',
                    'operationId' => 'searchListings',
                    'parameters'  => array_merge(
                        [['name' => 'q', 'in' => 'query', 'required' => true, 'schema' => ['type' => 'string'], 'description' => 'Search keyword(s)']],
                        $listingParams
                    ),
                    'responses' => [
                        '200' => ['description' => 'Matching listings', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                        '400' => ['description' => 'Missing keyword and coordinates', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                    ],
                ],
            ],
            '/listings/carousel' => [
                'get' => [
                    'tags'        => ['Listings'],
                    'summary'     => 'Featured carousel listings',
                    'description' => 'Returns up to 5 admin-pinned or algorithmically selected slides for the homepage carousel.',
                    'operationId' => 'carouselListings',
                    'parameters'  => [
                        ['name' => 'state',  'in' => 'query', 'schema' => ['type' => 'string'],  'description' => 'Indian state name (e.g. Maharashtra)'],
                        ['name' => 'lat',    'in' => 'query', 'schema' => ['type' => 'number'],  'description' => 'User latitude'],
                        ['name' => 'lng',    'in' => 'query', 'schema' => ['type' => 'number'],  'description' => 'User longitude'],
                        ['name' => 'radius', 'in' => 'query', 'schema' => ['type' => 'number',  'default' => 25]],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Up to 5 carousel slides', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                    ],
                ],
            ],
            '/listings/{id}' => [
                'get' => [
                    'tags'        => ['Listings'],
                    'summary'     => 'Get single listing detail',
                    'description' => 'Returns the full listing detail including images, slots, and reviews.',
                    'operationId' => 'getListing',
                    'parameters'  => [
                        ['name' => 'id',  'in' => 'path',  'required' => true, 'schema' => ['type' => 'integer']],
                        ['name' => 'lat', 'in' => 'query', 'schema' => ['type' => 'number']],
                        ['name' => 'lng', 'in' => 'query', 'schema' => ['type' => 'number']],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Listing detail', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                        '404' => ['description' => 'Listing not found', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                    ],
                ],
            ],
            '/categories' => [
                'get' => [
                    'tags'        => ['Categories'],
                    'summary'     => 'List all categories',
                    'description' => 'Returns all available listing categories, ordered alphabetically.',
                    'operationId' => 'listCategories',
                    'parameters'  => [],
                    'responses'   => [
                        '200' => ['description' => 'Category array', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                    ],
                ],
            ],
        ];
    }

    private function bookingPaths(): array
    {
        return [
            '/bookings' => [
                'get' => [
                    'tags'        => ['Bookings'],
                    'summary'     => 'Get my bookings',
                    'description' => 'Returns the authenticated parent\'s booking history (up to 50 entries).',
                    'operationId' => 'myBookings',
                    'security'    => [['sessionAuth' => []]],
                    'parameters'  => [],
                    'responses'   => [
                        '200' => ['description' => 'Booking list', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                        '401' => ['description' => 'Login required', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/Error']]]],
                    ],
                ],
            ],
            '/bookings/init' => [
                'post' => [
                    'tags'        => ['Bookings'],
                    'summary'     => 'Step 1 — Send OTP',
                    'description' => 'Validates student info, stores pending booking in session, and sends a 6-digit OTP via WhatsApp.',
                    'operationId' => 'bookingInit',
                    'security'    => [['sessionAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content'  => [
                            'application/json' => [
                                'schema' => [
                                    'required'   => ['listing_id', 'booking_type', 'student_name', 'phone'],
                                    'properties' => [
                                        'listing_id'   => ['type' => 'integer', 'example' => 42],
                                        'booking_type' => ['type' => 'string',  'enum' => ['regular', 'trial']],
                                        'student_name' => ['type' => 'string',  'example' => 'Aditya Sharma'],
                                        'student_age'  => ['type' => 'integer', 'example' => 8],
                                        'phone'        => ['type' => 'string',  'example' => '9876543210'],
                                        'class_date'   => ['type' => 'string',  'format' => 'date'],
                                        'class_time'   => ['type' => 'string',  'example' => '10:00'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'OTP sent', 'content' => ['application/json' => ['schema' => ['$ref' => '#/components/schemas/ApiResponse']]]],
                        '401' => ['description' => 'Login required'],
                        '422' => ['description' => 'Validation errors'],
                        '429' => ['description' => 'OTP rate limit exceeded'],
                    ],
                ],
            ],
            '/bookings/verify-otp' => [
                'post' => [
                    'tags'        => ['Bookings'],
                    'summary'     => 'Step 2 — Verify OTP',
                    'description' => 'Verifies the 6-digit OTP. For free classes: immediately creates booking and returns confirmation. For paid classes: creates a Razorpay order and returns order details.',
                    'operationId' => 'bookingVerifyOtp',
                    'requestBody' => [
                        'required' => true,
                        'content'  => [
                            'application/json' => [
                                'schema' => [
                                    'required'   => ['phone', 'otp'],
                                    'properties' => [
                                        'phone' => ['type' => 'string', 'example' => '9876543210'],
                                        'otp'   => ['type' => 'string', 'example' => '123456'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Booking confirmed (free) or Razorpay order data (paid)'],
                        '400' => ['description' => 'Invalid OTP or session expired'],
                        '422' => ['description' => 'Validation errors'],
                    ],
                ],
            ],
            '/bookings/confirm-payment' => [
                'post' => [
                    'tags'        => ['Bookings'],
                    'summary'     => 'Step 3 — Confirm Razorpay payment',
                    'description' => 'Verifies the Razorpay HMAC-SHA256 payment signature. On success, confirms the booking, records the transaction, and notifies parent + provider via WhatsApp.',
                    'operationId' => 'bookingConfirmPayment',
                    'requestBody' => [
                        'required' => true,
                        'content'  => [
                            'application/json' => [
                                'schema' => [
                                    'required'   => ['razorpay_payment_id', 'razorpay_order_id', 'razorpay_signature'],
                                    'properties' => [
                                        'razorpay_payment_id'  => ['type' => 'string'],
                                        'razorpay_order_id'    => ['type' => 'string'],
                                        'razorpay_signature'   => ['type' => 'string'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'responses' => [
                        '200' => ['description' => 'Booking confirmed'],
                        '400' => ['description' => 'Signature mismatch or session expired'],
                    ],
                ],
            ],
        ];
    }

    private function authPaths(): array
    {
        return [
            '/auth/login' => [
                'post' => [
                    'tags'        => ['Auth'],
                    'summary'     => 'Frontend parent login',
                    'description' => 'Authenticates a parent user and establishes a session.',
                    'operationId' => 'parentLogin',
                    'requestBody' => [
                        'required' => true,
                        'content'  => ['application/json' => ['schema' => ['required' => ['email', 'password'], 'properties' => ['email' => ['type' => 'string', 'format' => 'email'], 'password' => ['type' => 'string', 'format' => 'password']]]]],
                    ],
                    'responses' => ['200' => ['description' => 'Login success'], '401' => ['description' => 'Invalid credentials']],
                ],
            ],
            '/auth/logout' => [
                'get' => [
                    'tags'        => ['Auth'],
                    'summary'     => 'Logout',
                    'operationId' => 'parentLogout',
                    'parameters'  => [],
                    'responses'   => ['302' => ['description' => 'Redirects to /']],
                ],
            ],
        ];
    }

    private function utilPaths(): array
    {
        return [
            '/location' => [
                'post' => [
                    'tags'        => ['Location'],
                    'summary'     => 'Set user location',
                    'description' => 'Saves the user\'s selected location in session and cookie.',
                    'operationId' => 'setLocation',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['required' => ['location_name'], 'properties' => ['location_name' => ['type' => 'string'], 'lat' => ['type' => 'number'], 'lng' => ['type' => 'number'], 'state' => ['type' => 'string']]]]]],
                    'responses'   => ['200' => ['description' => 'Location saved']],
                ],
            ],
            '/email/send-otp' => [
                'post' => [
                    'tags'        => ['Email'],
                    'summary'     => 'Send email OTP',
                    'operationId' => 'sendEmailOtp',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['required' => ['email'], 'properties' => ['email' => ['type' => 'string', 'format' => 'email'], 'phone' => ['type' => 'string']]]]]],
                    'responses'   => ['200' => ['description' => 'OTP sent']],
                ],
            ],
            '/email/verify-otp' => [
                'post' => [
                    'tags'        => ['Email'],
                    'summary'     => 'Verify email OTP',
                    'operationId' => 'verifyEmailOtp',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['required' => ['email', 'otp'], 'properties' => ['email' => ['type' => 'string', 'format' => 'email'], 'otp' => ['type' => 'string'], 'phone' => ['type' => 'string'], 'booking_id' => ['type' => 'integer']]]]]],
                    'responses'   => ['200' => ['description' => 'Email verified'], '400' => ['description' => 'Invalid OTP']],
                ],
            ],
            '/reviews' => [
                'post' => [
                    'tags'        => ['Reviews'],
                    'summary'     => 'Submit a review',
                    'operationId' => 'submitReview',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['required' => ['listing_id', 'rating'], 'properties' => ['listing_id' => ['type' => 'integer'], 'rating' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 5], 'review_text' => ['type' => 'string'], 'booking_id' => ['type' => 'integer']]]]]],
                    'responses'   => ['200' => ['description' => 'Review saved'], '400' => ['description' => 'Already reviewed or invalid']],
                ],
            ],
            '/push/subscribe' => [
                'post' => [
                    'tags'        => ['Push'],
                    'summary'     => 'Subscribe to push notifications',
                    'operationId' => 'pushSubscribe',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['required' => ['phone', 'subscription'], 'properties' => ['phone' => ['type' => 'string'], 'subscription' => ['type' => 'object']]]]]],
                    'responses'   => ['200' => ['description' => 'Subscribed'], '401' => ['description' => 'OTP verification required']],
                ],
            ],
            '/push/unsubscribe' => [
                'post' => [
                    'tags'        => ['Push'],
                    'summary'     => 'Unsubscribe from push notifications',
                    'operationId' => 'pushUnsubscribe',
                    'requestBody' => ['required' => true, 'content' => ['application/json' => ['schema' => ['required' => ['phone'], 'properties' => ['phone' => ['type' => 'string']]]]]],
                    'responses'   => ['200' => ['description' => 'Unsubscribed']],
                ],
            ],
        ];
    }

    private function metaPaths(): array
    {
        return [
            '/health' => [
                'get' => [
                    'tags'        => ['Meta'],
                    'summary'     => 'API health check',
                    'description' => 'Returns the API status, version, and server timestamp.',
                    'operationId' => 'health',
                    'parameters'  => [],
                    'responses'   => [
                        '200' => [
                            'description' => 'API is healthy',
                            'content'     => [
                                'application/json' => [
                                    'example' => ['success' => true, 'api_version' => '1.0', 'status' => 'ok', 'timestamp' => '2026-02-25T18:00:00+05:30'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
