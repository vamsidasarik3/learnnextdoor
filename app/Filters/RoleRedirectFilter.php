<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleRedirectFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $user = $session->get('cnd_user');
        
        // If user is logged in as a Provider (Role 2)
        if ($user && isset($user['role']) && $user['role'] == 2) {
            $isProviderMode = $session->get('cnd_provider_mode') ?? true;
            $path = ltrim($request->getUri()->getPath(), '/');

            // 1. If hitting a provider page, force Provider Mode = true
            if (str_starts_with($path, 'provider/')) {
                if (!$isProviderMode) {
                    $session->set('cnd_provider_mode', true);
                }
                return null; // Allow access
            }

            // 2. If in Provider Mode, restrict access to public-facing pages
            if ($isProviderMode) {
                $blockedPaths = [
                    '',
                    'classes',
                    'contact',
                    'search',
                    'booking',
                    'login',
                    'register'
                ];
                
                $isBlocked = false;
                if ($path === '' || $path === '/') {
                    $isBlocked = true;
                } else {
                    foreach ($blockedPaths as $bp) {
                        if ($bp !== '' && ( $path === $bp || str_starts_with($path, $bp . '/') )) {
                            $isBlocked = true;
                            break;
                        }
                    }
                }

                if ($isBlocked) {
                    return redirect()->to('/provider/dashboard');
                }
            }
            // 3. If in User Mode, they are allowed to browse public pages (handled by the 'provider/' check above)
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
