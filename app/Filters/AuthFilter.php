<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        \helper(['basic', 'cookie']);
        if( !is_logged() ){
            $path = $request->getUri()->getPath();
            if (strpos($path, 'admin') === 0) {
                return redirect()->to('/auth/login');
            }
            
            // For frontend/provider routes, redirect to /login with intended URL
            $currentUrl = current_url();
            return redirect()->to('/login?redirect=' . urlencode($currentUrl));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // die(var_dump());
    }
}