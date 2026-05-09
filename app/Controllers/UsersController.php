<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\HistoryModel;
use App\Models\FactoryModel;
use App\Models\ComponentModel;
use App\Models\AreaModel;
use App\Models\RoleModel;

class UsersController extends BaseController
{
    protected $processModel;
    protected $session;
    protected $history;
    protected $factoryModel;
    protected $componentModel;
    protected $areaModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->history = new HistoryModel();
        $this->factoryModel = new FactoryModel();
        $this->componentModel = new ComponentModel();
        $this->areaModel = new AreaModel();
        $this->roleModel = new RoleModel();
        $this->session = session();
    }

    public function index()
{ 

    // Checking Permission
    $this->checkPermission('USERS', 'view');
    // Get all active factories for the dropdown
    $data['factories'] = $this->factoryModel
        ->where('IsActive', 1)
        ->orderBy('Id', 'ASC')
        ->findAll();

    // Get all active roles for the dropdown
    $data['roles'] = $this->roleModel
        ->where('IsActive', 1)
        ->orderBy('Id', 'ASC')
        ->findAll();

   // Get processes with hierarchical data via multiple Left Joins
    $data['users'] = $this->userModel
        ->select('
            tbl_user.Id, 
            tbl_user.FullName, 
            tbl_user.IsActive, 
            tbl_user.IsLock,
            tbl_user.DateAdd, 
            tbl_user.LastLoginDate,
            tbl_user.EmailAddress,
            tbl_area.Id as AreaId, 
            tbl_area.AreaName,
            tbl_component.Id as ComponentId, 
            tbl_component.ComponentName,
            tbl_role.Id as RoleId, 
            tbl_role.RoleName,
            tbl_factory.Id as FactoryId, 
            tbl_factory.FactoryName
        ')
        ->join('tbl_area', 'tbl_user.AreaId = tbl_area.Id', 'left')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->join('tbl_role', 'tbl_user.RoleId = tbl_role.Id', 'left')
        ->orderBy('tbl_user.Id', 'ASC')
        ->findAll();

    return view('users', $data);
    }

// Get components for the dependent dropdown
public function getComponentsByFactory($factoryId) {
    $components = $this->componentModel
        ->where('FactoryId', $factoryId)
        ->where('IsActive', 1)
        ->findAll();
    return $this->response->setJSON($components);
}
// Get components for the dependent dropdown

public function getAreasByComponent($componentId) {
    $areas = $this->areaModel
        ->where('ComponentId', $componentId)
        ->where('IsActive', 1)
        ->findAll();
    return $this->response->setJSON($areas);
}
public function edit($id) {
    // Join to get FactoryId so the frontend knows which factory to select
    $user = $this->userModel
        ->select('tbl_user.*, tbl_area.ComponentId, tbl_component.FactoryId,, tbl_area.Id as AreaId')
        ->join('tbl_area', 'tbl_user.AreaId = tbl_area.Id', 'left')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->find($id);
    return $this->response->setJSON($user);
}

    public function store()
    {
        // Checking Permission
        $this->checkPermission('USERS', 'add');
        $userId = $this->request->getPost('UserId');
        $userName = $this->request->getPost('UserName');

        // Check for duplicate
       if ($this->userModel->where('UserName', $userName)
                         ->first())  {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => ['UserName' => 'User name already exists!']
            ]);
        }

        if(!$this->validate([
            'UserName' => 'required|min_length[3]|max_length[50]',
            'IsActive' => 'required|in_list[0,1]'
        ])){
            return $this->response->setStatusCode(400)->setJSON([
                'success'=>false, 
                'errors'=>$this->validator->getErrors()
            ]);
        }

        $this->userModel->insert([
            'FullName'  => strtoupper(trim($this->request->getPost('FullName'))),
            'UserName'  => $userName,
            'EmailAddress'  => $this->request->getPost('EmailAddress'),
            'Password' => md5($this->request->getPost('Password')),
            'FactoryId'  => $this->request->getPost('FactoryId'),
            'ComponentId'  => $this->request->getPost('ComponentId'),
            'AreaId'  => $this->request->getPost('AreaId'),
            'RoleId'  => $this->request->getPost('RoleId'),
            'IsActive'  => (int)$this->request->getPost('IsActive'),
            'DateAdd' => date('Y-m-d H:i:s'),
            'AddedBy'   => $this->session->get('Id'),
        ]);

      $id = $this->userModel->getInsertID();

    // Corrected Join: Area -> Component -> Factory
    $newUser = $this->userModel
         ->select('tbl_user.*, tbl_area.AreaName, tbl_component.ComponentName, tbl_factory.FactoryName, tbl_role.RoleName')
        ->join('tbl_area', 'tbl_user.AreaId = tbl_area.Id', 'left')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->join('tbl_role', 'tbl_user.RoleId = tbl_role.Id', 'left')
        ->find($id);

    // History log MUST come before the return
    $this->history->log('users', 'CREATE', null, $newUser, session()->get('Id'));
    return $this->response->setJSON([
        'success' => true, 
        'user' => $newUser, 
        'message' => '✅ User added successfully!'
    ]);
}

 public function update($id)
{
    // Checking Permission
    $this->checkPermission('USERS', 'edit');
    $user = $this->userModel->find($id);
    if(!$user) return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'User not found']);

    $userName = $this->request->getPost('UserName');
    
    // Check for duplicate excluding current user
    if ($this->userModel->where('UserName', $userName)->where('Id !=', $id)->first()) {
        return $this->response->setStatusCode(400)->setJSON([
            'success' => false,
            'errors' => ['UserName' => '⚠ User name already exists!']
        ]);
    }

    if(!$this->validate([
        'UserName' => 'required|min_length[3]|max_length[50]',
        'IsActive' => 'required|in_list[0,1]'
    ])){
        return $this->response->setStatusCode(400)->setJSON(['success'=>false,'errors'=>$this->validator->getErrors()]);
    }

    // 1. Prepare the standard data
    $updateData = [
        'FullName'  => strtoupper(trim($this->request->getPost('FullName'))),
        'UserName'     => $userName,
        'EmailAddress' => $this->request->getPost('EmailAddress'),
        'FactoryId'    => $this->request->getPost('FactoryId'),
        'ComponentId'  => $this->request->getPost('ComponentId'),
        'AreaId'       => $this->request->getPost('AreaId'),
        'RoleId'       => $this->request->getPost('RoleId'),
        'IsActive'     => (int)$this->request->getPost('IsActive'),
        'DateUpdate'   => date('Y-m-d H:i:s'),
        'UpdatedBy'    => $this->session->get('Id'),
    ];

    // 2. Conditionally add Password if it's provided
    $newPassword = $this->request->getPost('Password');
    if (!empty($newPassword)) {
        $updateData['Password'] = md5($newPassword);
    }

    // 3. Execute the update with the prepared array
    $this->userModel->update($id, $updateData);

    // history logic
    $after = $this->userModel->find($id);
    $before = $user; 

    $this->history->log('users', 'UPDATE', $before, $after, session()->get('Id'));

    // Fetch with Joins for UI update
    $newUser = $this->userModel
        ->select('tbl_user.*, tbl_area.AreaName, tbl_component.ComponentName, tbl_factory.FactoryName, tbl_role.RoleName')
        ->join('tbl_area', 'tbl_user.AreaId = tbl_area.Id', 'left')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->join('tbl_role', 'tbl_user.RoleId = tbl_role.Id', 'left')
        ->find($id);

    return $this->response->setJSON(['success'=>true, 'message'=>'✅ User updated successfully!', 'user'=>$newUser]);
}
    public function delete($id)
    {
        $role = $this->areaModel->find($id);
        if(!$role) return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'Role not found']);

         // history
        $before = $role;

        $this->areaModel->delete($id);

        $this->history->log(
            'roles',
            'DELETE',
            $before,
            null,
            session()->get('Id')
        );

        return $this->response->setJSON(['success'=>true,'message'=>'✅ Role deleted successfully!']);
    }
  public function toggleStatus($id)
{
    // Checking Permission
    $this->checkPermission('USERS', 'edit');
    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($id);

    if(!$user){
        return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
    }

    // Toggle
    $newStatus = $user['IsActive'] == 1 ? 0 : 1;

      // History
    $before = $user;

    $userModel->update($id, ['IsActive' => $newStatus]);

    $after = $userModel->find($id);

    $this->history->log(
        'users',
        'STATUS_CHANGE',
        $before,
        $after,
        session()->get('Id')
    );

   
    return $this->response->setJSON([
        'success' => true,
        'newStatus' => $newStatus,
        'message' => $newStatus ? '✅ User activated' : '✅ User deactivated'
    ]);
}

public function toggleLock($id)
{// Checking Permission
    $this->checkPermission('USERS', 'edit');

    $userModel = new \App\Models\UserModel();
    $user = $userModel->find($id);

    if(!$user){
        return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
    }

    $newLockStatus = $user['IsLock'] == 1 ? 0 : 1;
    $before = $user;

    $userModel->update($id, ['IsLock' => $newLockStatus]);
    $after = $userModel->find($id);

    $this->history->log('users', 'LOCK_CHANGE', $before, $after, session()->get('Id'));

    return $this->response->setJSON([
        'success' => true,
        'newLock' => $newLockStatus,
        'message' => $newLockStatus ? '🔒 User account locked' : '🔓 User account unlocked'
    ]);
}
}
