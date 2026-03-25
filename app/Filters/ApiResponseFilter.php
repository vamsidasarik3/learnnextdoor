<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * ApiResponseFilter — Subtask 4.1
 * ─────────────────────────────────────────────────────────────────────
 * Applied AFTER every /v1/ request to:
 *   1. Force Content-Type: application/json on all API responses.
 *   2. Inject standard CORS headers so mobile clients can call the API.
 *   3. Inject API version meta header.
 *   4. Sanitise any non-JSON response that accidentally slipped through
 *      (wraps it in a standard error envelope).
 */
class ApiResponseFilter implements FilterInterface
{
    /** The current API version string injected into every response. */
    private const API_VERSION = '1.0';

    public function before(RequestInterface $request, $arguments = null)
    {
        // Handle CORS pre-flight OPTIONS — return 200 immediately.
        if ($request->getMethod(true) === 'OPTIONS') {
            $response = \Config\Services::response();
            $this->addCorsHeaders($response);
            return $response->setStatusCode(200)->setBody('');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Always set content-type to JSON for v1 API routes
        $response->setContentType('application/json');

        // Add standard API headers
        $response->setHeader('X-API-Version', self::API_VERSION);
        $response->setHeader('X-Powered-By',  'Class Next Door API');
        $response->setHeader('Cache-Control',  'no-cache, no-store, must-revalidate');
        $response->setHeader('Pragma',         'no-cache');
        $response->setHeader('Expires',        '0');

        // Add CORS headers
        $this->addCorsHeaders($response);

        // Ensure the body is valid JSON; if not, wrap it
        $body = $response->getBody();
        if (!empty($body)) {
            json_decode($body);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $response->setBody(json_encode([
                    'success' => false,
                    'error'   => 'internal_error',
                    'message' => 'An unexpected response was generated.',
                ], JSON_UNESCAPED_UNICODE));
            }
        }
    }

    // ─── Private helpers ──────────────────────────────────────────────

    private function addCorsHeaders(ResponseInterface $response): void
    {
        $allowedOrigins = explode(',', env('API_ALLOWED_ORIGINS', '*'));
        $origin         = \Config\Services::request()->getHeaderLine('Origin');

        if ($origin && in_array($origin, $allowedOrigins, true)) {
            $response->setHeader('Access-Control-Allow-Origin', $origin);
            $response->setHeader('Vary', 'Origin');
        } else {
            $response->setHeader('Access-Control-Allow-Origin', '*');
        }

        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-API-Key');
        $response->setHeader('Access-Control-Max-Age',       '86400');
    }
}
