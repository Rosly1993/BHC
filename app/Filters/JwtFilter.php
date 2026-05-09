<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $token = $session->get('jwt_token');

        if (!$token || !$this->isJwtValid($token)) {
            if ($request->isAJAX() || str_contains($request->getHeaderLine('Accept'), 'application/json')) {
                return service('response')
                    ->setStatusCode(401)
                    ->setJSON(['success' => false, 'message' => 'Token Expired']);
            }
            return redirect()->to('/login')->with('error', 'Session expired.');
        }
    }

    private function isJwtValid($token)
    {
        try {
            $key = getenv('JWT_SECRET_KEY');
            if (!$key) return false;
            JWT::decode($token, new Key($key, 'HS256'));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}