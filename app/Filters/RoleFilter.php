<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $requiredRole = $arguments[0] ?? null;

        if (session()->get('role') !== $requiredRole) {
            return redirect()->to($requiredRole === 'operateur' ? '/operateur/login' : '/client/login');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}