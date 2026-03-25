<?php

namespace App\Controllers\Api\V1;

/**
 * MetaController — /v1/health, /v1/ping
 * ─────────────────────────────────────────────────────────────────────
 * Lightweight health and status endpoints.
 * Used by monitoring, load balancers, and mobile app startup checks.
 */
class MetaController extends ApiBaseController
{
    /**
     * GET /v1/health
     * Returns API status, version, and a DB connectivity check.
     */
    public function health()
    {
        $dbOk = false;
        try {
            $db   = \Config\Database::connect();
            $dbOk = $db->connID !== false && $db->query('SELECT 1') !== false;
        } catch (\Throwable $e) {
            log_message('error', '[API/v1/health] DB check failed: ' . $e->getMessage());
        }

        $status = $dbOk ? 'ok' : 'degraded';
        $code   = $dbOk ? 200  : 503;

        return $this->response->setStatusCode($code)->setJSON([
            'success'     => $dbOk,
            'api_version' => $this->apiVersion,
            'status'      => $status,
            'timestamp'   => date('c'),
            'environment' => ENVIRONMENT,
            'components'  => [
                'database' => $dbOk ? 'ok' : 'error',
                'cache'    => $this->checkCache(),
            ],
        ]);
    }

    /**
     * GET /v1/ping
     * Minimal liveness check — always returns 200 if the server is up.
     */
    public function ping()
    {
        return $this->response->setStatusCode(200)->setJSON([
            'success'   => true,
            'message'   => 'pong',
            'timestamp' => date('c'),
        ]);
    }

    private function checkCache(): string
    {
        try {
            $cache = \Config\Services::cache();
            $cache->save('__api_health_check__', 1, 5);
            return $cache->get('__api_health_check__') === 1 ? 'ok' : 'error';
        } catch (\Throwable $e) {
            return 'error';
        }
    }
}
