<?php

namespace App\Controllers;

class UserController extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index() {
        // It's a good habit to check what the column name is in tbl_role
        $data['roles'] = $this->db->table('tbl_role')->get()->getResult();
        return view('user_list', $data);
    }
    public function getUsers() {
        $builder = $this->db->table('tbl_user u')
            ->select('u.*, r.Role') // Adjust role_name to your actual column
            ->join('tbl_role r', 'r.Id = u.role', 'left');
        
        return $this->response->setJSON($builder->get()->getResult());
    }

    public function store() {
        $id = $this->request->getPost('id');
        $password = $this->request->getPost('password');

        $data = [
            'username'      => $this->request->getPost('username'),
            'firstname'     => $this->request->getPost('firstname'),
            'middlename'    => $this->request->getPost('middlename'),
            'lastname'      => $this->request->getPost('lastname'),
            'emailaddress'  => $this->request->getPost('emailaddress'),
            'contactnumber' => $this->request->getPost('contactnumber'),
            'role'          => $this->request->getPost('role'),
            'isactive'      => $this->request->getPost('isactive') ?? 1
        ];

        // Only hash and update password if provided (useful for edits)
        if (!empty($password)) {
            $data['password'] = md5($password);
        }

        if ($id) {
            $this->db->table('tbl_user')->where('id', $id)->update($data);
            $msg = "User updated successfully.";
        } else {
            $this->db->table('tbl_user')->insert($data);
            $msg = "New user created successfully.";
        }

        return $this->response->setJSON(['status' => 'success', 'message' => $msg]);
    }
}