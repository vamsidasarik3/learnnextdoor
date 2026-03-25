<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;

/**
 * ApiBaseController — Subtask 4.1
 * ─────────────────────────────────────────────────────────────────────
 * Base class for all /v1/ API controllers.
 *
 * Provides:
 *   • Standard success / error response helpers
 *   • Consistent JSON envelope:  { success, data, meta, error }
 *   • HTTP method guards
 *   • Simple API-key authentication helper (token from Bearer header)
 */
abstract class ApiBaseController extends BaseController
{
    /** API version string embedded in every response. */
    protected string $apiVersion = '1.0';

    // ─── Response Envelope Helpers ────────────────────────────────────

    /**
     * Return a standard success JSON response.
     *
     * @param mixed       $data    The payload (array|null)
     * @param array|null  $meta    Optional pagination/meta block
     * @param int         $status  HTTP status code (default 200)
     */
    protected function success(mixed $data = null, ?array $meta = null, int $status = 200)
    {
        $body = [
            'success'     => true,
            'api_version' => $this->apiVersion,
            'timestamp'   => date('c'),
        ];

        if ($data !== null) {
            $body['data'] = $data;
        }

        if ($meta !== null) {
            $body['meta'] = $meta;
        }

        return $this->response
            ->setStatusCode($status)
            ->setJSON($body);
    }

    /**
     * Return a standard error JSON response.
     *
     * @param string $message     Human-readable message
     * @param string $errorCode   Machine-readable snake_case code
     * @param int    $status      HTTP status code
     * @param array  $extra       Extra fields to merge into the root envelope
     */
    protected function fail(
        string $message,
        string $errorCode = 'error',
        int    $status    = 400,
        array  $extra     = []
    ) {
        $body = array_merge([
            'success'     => false,
            'api_version' => $this->apiVersion,
            'timestamp'   => date('c'),
            'error'       => $errorCode,
            'message'     => $message,
        ], $extra);

        return $this->response
            ->setStatusCode($status)
            ->setJSON($body);
    }

    /**
     * Return a validation-errors response (HTTP 422).
     */
    protected function validationError(array $errors)
    {
        return $this->fail(
            message:   'Validation failed. Please check the provided data.',
            errorCode: 'validation_error',
            status:    422,
            extra:     ['errors' => $errors]
        );
    }

    /**
     * Return a 404 not-found response.
     */
    protected function notFound(string $resource = 'Resource')
    {
        return $this->fail(
            message:   "{$resource} not found.",
            errorCode: 'not_found',
            status:    404
        );
    }

    /**
     * Return a 401 unauthorized response.
     */
    protected function unauthorized(string $message = 'Authentication required.')
    {
        return $this->fail(
            message:   $message,
            errorCode: 'unauthorized',
            status:    401
        );
    }

    /**
     * Return a 403 forbidden response.
     */
    protected function forbidden(string $message = 'Access denied.')
    {
        return $this->fail(
            message:   $message,
            errorCode: 'forbidden',
            status:    403
        );
    }

    // ─── Pagination helper ────────────────────────────────────────────

    /**
     * Build a standard pagination meta block.
     */
    protected function paginationMeta(int $total, int $page, int $perPage): array
    {
        $totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;

        return [
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => $totalPages,
            'has_next'    => $page < $totalPages,
            'has_prev'    => $page > 1,
        ];
    }

    // ─── Guard helpers ────────────────────────────────────────────────

    /**
     * Get the logged-in parent user from session (cnd_user).
     * Returns null if not logged in via the frontend auth.
     */
    protected function getParentUser(): ?array
    {
        return session()->get('cnd_user') ?: null;
    }

    /**
     * Get the logged-in admin/provider user via admin session.
     */
    protected function getAdminUser(): mixed
    {
        helper(['basic']);
        return function_exists('logged') ? logged() : null;
    }
}
