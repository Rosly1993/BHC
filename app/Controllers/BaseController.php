<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    protected $helpers = ['url', 'form']; // Add common helpers here

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);
    }

    /**
     * Global Permission Check
     * @param string $module The name of the module (e.g., 'Inventory')
     * @param string $action The action to check (e.g., 'view', 'add', 'edit')
     * @return bool|void Redirects if unauthorized
     */
protected function checkPermission(string $module, string $action)
{
    $session = session();
    $userId = $session->get('Id');

    // --- STEP 1: SILENT SESSION RECOVERY ---
    if (!$userId) {
        // Look for the JWT in the Request Header
        $token = $this->request->getHeaderLine('Authorization');
        
        if ($token) {
            try {
                $tokenParts = explode('.', $token);
                if (count($tokenParts) === 3) {
                    $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
                    
                    // If Token is valid, "Force Login" the user back into the session
                    if (isset($payload['exp']) && $payload['exp'] > time()) {
                        $userId = $payload['data']['id'];
                        
                        // Re-fetch everything needed for the session
                        $db = \Config\Database::connect();
                        $user = $db->table('tbl_user')->where('Id', $userId)->get()->getRowArray();
                        
                        if ($user) {
                            $session->set('Id', $user['Id']);
                            $session->set('username', $user['username']);
                            // IMPORTANT: Re-fetch and re-set the permissions array here
                            // $session->set('permissions', $this->loadPermissions($user['RoleId']));
                            
                            log_message('debug', "Session auto-recovered for User: " . $userId);
                        }
                    }
                }
            } catch (\Exception $e) {
                log_message('error', "JWT Recovery failed: " . $e->getMessage());
            }
        }
    }

    // --- STEP 2: HARD AUTH GUARD ---
    if (!$userId) {
        return $this->handleUnauthorized();
    }

    // --- STEP 3: PERMISSION CHECK ---
    $permissions = $session->get('permissions');
    if (!isset($permissions[$module][$action]) || (int)$permissions[$module][$action] !== 1) {
        return $this->handleForbidden();
    }

    return true;
}
public function ping() {
    return $this->response->setJSON(['status' => 'alive']);
}
private function handleUnauthorized() {
    if ($this->request->isAJAX()) {
        $this->response->setStatusCode(401)->setJSON(['message' => 'Expired'])->send();
        exit;
    }
    return redirect()->to(base_url('login'))->send();
    exit;
}

  
}
