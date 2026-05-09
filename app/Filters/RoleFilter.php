<?php

namespace App\Filters; // Must be App\Filters

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRoleId = (int) $session->get('role'); 

        if ($arguments) {
            // Converts ['1', '2'] from routes to [1, 2]
            $allowedRoles = array_map('intval', $arguments);

            if (!in_array($userRoleId, $allowedRoles)) {
                return redirect()->to(base_url('dashboard'))
                                 ->with('error', 'Unauthorized access.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Keep empty
    }
}