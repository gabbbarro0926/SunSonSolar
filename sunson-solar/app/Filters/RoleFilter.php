<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Usage in Routes: ['filter' => 'auth', 'role:admin,hr'] — must run after "auth".
 * The special argument "employee" allows every non-customer role.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = AuthFilter::$user;
        $allowed = $arguments ?? [];

        if (! $user) {
            return $this->deny('Not authenticated.', 401);
        }

        $ok = in_array($user['role'], $allowed, true)
            || (in_array('employee', $allowed, true) && $user['user_type'] === 'employee');

        if (! $ok) {
            return $this->deny('You do not have permission to do this.', 403);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function deny(string $message, int $code): ResponseInterface
    {
        return service('response')->setStatusCode($code)->setJSON([
            'status'  => $code,
            'message' => $message,
        ]);
    }
}
