<?php

namespace App\Filters;

use App\Models\ApiTokenModel;
use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Requires "Authorization: Bearer <token>". On success the user row is stored
 * in the "auth_user" service-shared slot so controllers can read it.
 */
class AuthFilter implements FilterInterface
{
    public static ?array $user = null;

    public function before(RequestInterface $request, $arguments = null)
    {
        self::$user = null;
        $header     = $request->getHeaderLine('Authorization');

        if (! preg_match('/^Bearer\s+(\S+)$/i', $header, $m)) {
            return $this->deny('Missing or invalid Authorization header.', 401);
        }

        $row = (new ApiTokenModel())
            ->where('token_hash', hash('sha256', $m[1]))
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if (! $row) {
            return $this->deny('Token is invalid or expired.', 401);
        }

        $user = (new UserModel())->find($row['user_id']);

        if (! $user || (int) $user['is_active'] !== 1) {
            return $this->deny('Account is deactivated.', 403);
        }

        self::$user = $user;
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
