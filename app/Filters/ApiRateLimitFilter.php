<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

/**
 * ApiRateLimitFilter — Subtask 4.1
 * ─────────────────────────────────────────────────────────────────────
 * Enforces a per-IP request-rate limit on all /v1/ API endpoints.
 *
 * Strategy : sliding-window counter stored in CI4 Cache.
 *   Default : 120 requests / 60 seconds per IP
 *
 * Headers returned on every response:
 *   X-RateLimit-Limit     : max requests in the window
 *   X-RateLimit-Remaining : requests remaining this window
 *   X-RateLimit-Reset     : Unix-timestamp when the window resets
 *
 * Responses:
 *   HTTP 429  { "success": false, "error": "rate_limit_exceeded", ... }
 */
class ApiRateLimitFilter implements FilterInterface
{
    /** Max requests allowed in $windowSeconds. */
    private const MAX_REQUESTS = 120;

    /** Rolling window length, seconds. */
    private const WINDOW_SECONDS = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        $ip     = $request->getIPAddress();
        $key    = 'api_rl_' . md5($ip);
        $cache  = \Config\Services::cache();

        $data = $cache->get($key) ?? ['count' => 0, 'reset' => time() + self::WINDOW_SECONDS];

        // Reset window if expired
        if (time() > $data['reset']) {
            $data = ['count' => 0, 'reset' => time() + self::WINDOW_SECONDS];
        }

        $data['count']++;
        $ttl = max(1, $data['reset'] - time());
        $cache->save($key, $data, $ttl + 1);

        $remaining = max(0, self::MAX_REQUESTS - $data['count']);

        // Attach rate-limit headers via the response singleton
        $response = \Config\Services::response();
        $response->setHeader('X-RateLimit-Limit',     (string) self::MAX_REQUESTS);
        $response->setHeader('X-RateLimit-Remaining', (string) $remaining);
        $response->setHeader('X-RateLimit-Reset',      (string) $data['reset']);

        if ($data['count'] > self::MAX_REQUESTS) {
            return $response
                ->setStatusCode(429, 'Too Many Requests')
                ->setHeader('Retry-After', (string) $ttl)
                ->setContentType('application/json')
                ->setBody(json_encode([
                    'success'     => false,
                    'error'       => 'rate_limit_exceeded',
                    'message'     => 'Too many requests. Please retry after ' . $ttl . ' second(s).',
                    'retry_after' => $ttl,
                ], JSON_UNESCAPED_UNICODE));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Headers are already set in before(); nothing extra needed.
    }
}
