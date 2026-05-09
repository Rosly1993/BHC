<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
   public function before(RequestInterface $request, $arguments = null) {
    if (!session()->get('isLoggedIn')) {
        
        // Check if the request expects JSON or is an AJAX call
        $isAjax = $request->isAJAX();
        $wantsJson = str_contains($request->getHeaderLine('Accept'), 'application/json');

        if ($isAjax || $wantsJson) {
            // FORCE a 401 JSON response and STOP execution
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'redirect' => base_url('login')
            ]);
            exit; // This prevents the 302 redirect from ever happening
        }

        return redirect()->to('/login')->with('error', 'Session expired.');
    }
}

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}