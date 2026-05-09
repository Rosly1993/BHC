<?php
namespace App\Controllers;

use App\Models\UserModel;
// use App\Models\HistoryModel; // Import this
// use Firebase\JWT\JWT; // Remove if only using Sessions
// use Firebase\JWT\Key;

class Auth extends BaseController {

    // protected $history;

    public function __construct() {
        // $this->history = new HistoryModel();
    }

    public function login() {
        // Simple check: if already logged in, go to dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('login');
    }

    public function loginProcess() {
        $session = session();
        $model = new UserModel();
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Find user by username
        $user = $model->where('username', $username)->first();

        if ($user) {
            // Check password (using MD5 as per your current DB state)
            if (md5($password) === $user['password']) {
                
                // Set session data
                $session->set([
                    'userId'     => $user['id'],
                    'username'   => $user['username'],
                    'role'       => (int) $user['role'], // FIX: Use square brackets []
                    'fullName'   => $user['firstname'] . ' ' . $user['lastname'], 
                    'isLoggedIn' => true,
                ]);

                // Optional: Log the login to your HistoryModel
                // $this->history->save(['user_id' => $user['id'], 'action' => 'Logged In']);

                return redirect()->to('/dashboard');
            } else {
                // Password didn't match
                return redirect()->back()->withInput()->with('error', 'Invalid password.');
            }
        }
        
        // Username didn't exist
        return redirect()->back()->withInput()->with('error', 'Username not found.');
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}