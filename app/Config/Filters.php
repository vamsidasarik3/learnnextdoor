<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

use App\Filters\AuthFilter;
use App\Filters\ApiRateLimitFilter;
use App\Filters\ApiResponseFilter;

class Filters extends BaseConfig
{
    /**
     * Configures aliases for Filter classes to
     * make reading things nicer and simpler.
     *
     * @var array
     */
    public $aliases = [
        'csrf'            => CSRF::class,
        'toolbar'         => DebugToolbar::class,
        'honeypot'        => Honeypot::class,
        'invalidchars'    => InvalidChars::class,
        'secureheaders'   => SecureHeaders::class,
        'authentication'  => AuthFilter::class,
        // ── Subtask 4.1 — API v1 filters ──────────────────────────
        'api_rate_limit'  => ApiRateLimitFilter::class,
        'api_response'    => ApiResponseFilter::class,
    ];

    /**
     * List of filter aliases that are always
     * applied before and after every request.
     *
     * @var array
     */
    public $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf',
            // 'invalidchars',
            'authentication' => ['except' => [
                '/',
                'auth/*',
                'classes',
                'classes/*',
                'activity',
                'contact',
                'contact/*',
                'search',
                'set-location',
                'api/listings/*',
                'api/feedback',
                'api/reviews/*',
                'api/push/*',
                'booking/*',
                'login',
                'register',
                'uploads/*',
                'assets/*',
                'v1/*',       // Subtask 4.1 — v1 API is self-guarded (returns 401 JSON)
            ]]
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            // 'secureheaders',
        ],
    ];

    /**
     * List of filter aliases that works on a
     * particular HTTP method (GET, POST, etc.).
     *
     * Example:
     * 'post' => ['csrf', 'throttle']
     *
     * @var array
     */
    public $methods = [
        // CSRF is now applied per-route in $filters below,
        // NOT globally — because CSRF redirects break AJAX POSTs silently.
        'get'  => [],
        'post' => [],
    ];

    /**
     * List of filter aliases that should run on any
     * before or after URI patterns.
     *
     * Example:
     * 'isLoggedIn' => ['before' => ['account/*', 'profiles/*']]
     *
     * @var array
     */
    public $filters = [
        // Apply CSRF to form-based POST routes only (not AJAX endpoints)
        'csrf' => [
            'before' => [
                'login',
                'register',
                'contact/submit',
                'booking/*',
                'book/*',
            ]
        ],

        // ── Subtask 4.1 — API v1 filters ──────────────────────────
        // Rate limiter runs BEFORE the request reaches the controller.
        'api_rate_limit' => [
            'before' => ['v1/*'],
        ],
        // Response normaliser runs AFTER every v1 controller response.
        'api_response' => [
            'before' => ['v1/*'],   // handle OPTIONS pre-flight
            'after'  => ['v1/*'],   // enforce Content-Type, CORS, version header
        ],
    ];
}
