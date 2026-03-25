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
        $isProviderMode = $session->get('cnd_provider_mode') ?? true;
        
        if ($user && isset($user['role']) && $user['role'] == 2) {
            $path = ltrim($request->getUri()->getPath(), '/');

            if ($isProviderMode) {
                // Provider Mode: Block public-facing pages
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
            } else {
                // User Mode: Block provider-management pages (except toggle-mode)
                if (str_starts_with($path, 'provider/') && !str_starts_with($path, 'provider/toggle-mode')) {
                    return redirect()->to('/');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
