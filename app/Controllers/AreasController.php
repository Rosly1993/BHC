<?php

namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\HistoryModel;
use App\Models\FactoryModel;
use App\Models\ComponentModel;

class AreasController extends BaseController
{
    protected $areaModel;
    protected $session;
    protected $history;
    protected $factoryModel;
    protected $componentModel;

    public function __construct()
    {
        $this->areaModel = new AreaModel();
        $this->history = new HistoryModel();
        $this->factoryModel = new FactoryModel();
        $this->componentModel = new ComponentModel();
        $this->session = session();
    }

    public function index()
{ 
    
    // Checking Permission
    $this->checkPermission('AREAS', 'view');

    // Get all active factories for the dropdown
    $data['factories'] = $this->factoryModel
        ->where('IsActive', 1)
        ->orderBy('Id', 'ASC')
        ->findAll();

    // Get components with Factory Name via Left Join
    $data['components'] = $this->areaModel
        ->select('tbl_area.Id, tbl_area.AreaName, tbl_area.IsActive, tbl_area.DateAdd, tbl_component.ComponentName, tbl_factory.FactoryName')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->orderBy('tbl_area.Id', 'ASC')
        ->findAll();

    return view('areas', $data);
}

// Get components for the dependent dropdown
public function getComponentsByFactory($factoryId) {
    $components = $this->componentModel
        ->where('FactoryId', $factoryId)
        ->where('IsActive', 1)
        ->findAll();
    return $this->response->setJSON($components);
}

public function edit($id) {
    // Join to get FactoryId so the frontend knows which factory to select
    $area = $this->areaModel
        ->select('tbl_area.*, tbl_component.FactoryId')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->find($id);
    return $this->response->setJSON($area);
}

    public function store()
    {
    // Checking Permission
        $this->checkPermission('AREAS', 'add');


        $componentId = $this->request->getPost('ComponentId');
        $areaName = $this->request->getPost('AreaName');

        // Check for duplicate
       if ($this->areaModel->where('ComponentId', $componentId)
                         ->where('AreaName', $areaName)
                         ->first())  {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => ['AreaName' => 'Area name already exists!']
            ]);
        }

        if(!$this->validate([
            'AreaName' => 'required|min_length[3]|max_length[50]',
            'IsActive' => 'required|in_list[0,1]'
        ])){
            return $this->response->setStatusCode(400)->setJSON([
                'success'=>false, 
                'errors'=>$this->validator->getErrors()
            ]);
        }

        $this->areaModel->insert([
            'ComponentId'  => $componentId,
            'AreaName'    => strtoupper(trim($this->request->getPost('AreaName'))),
            'IsActive'  => (int)$this->request->getPost('IsActive'),
            'DateAdd' => date('Y-m-d H:i:s'),
            'AddedBy'   => $this->session->get('Id'),
        ]);

      $id = $this->areaModel->getInsertID();

    // Corrected Join: Area -> Component -> Factory
    $newArea = $this->areaModel
        ->select('tbl_area.*, tbl_factory.FactoryName, tbl_component.ComponentName')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->find($id);

    // History log MUST come before the return
    $this->history->log('areas', 'CREATE', null, $newArea, session()->get('Id'));

    return $this->response->setJSON([
        'success' => true, 
        'area' => $newArea, 
        'message' => '✅ Area added successfully!'
    ]);
}

    public function update($id)
    {
        // Checking Permission
        $this->checkPermission('AREAS', 'edit');

        $area = $this->areaModel->find($id);
        if(!$area) return $this->response->setStatusCode(404)->setJSON(['success'=>false,'message'=>'Area not found']);

        $componentId = $this->request->getPost('ComponentId');
        $areaName = $this->request->getPost('AreaName');

        // Check for duplicate excluding current role
        if ($this->areaModel->where('ComponentId', $componentId)->where('AreaName', $areaName)->where('Id !=', $id)->first()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'errors' => ['AreaName' => 'Area name already exists!']
            ]);
        }

        if(!$this->validate([
            'AreaName' => 'required|min_length[3]|max_length[50]',
            'IsActive' => 'required|in_list[0,1]'
        ])){
            return $this->response->setStatusCode(400)->setJSON(['success'=>false,'errors'=>$this->validator->getErrors()]);
        }

        $this->areaModel->update($id, [
            'AreaName'    => strtoupper(trim($this->request->getPost('AreaName'))),
            'ComponentId'    => $componentId,
            'IsActive'    => (int)$this->request->getPost('IsActive'),
            'DateUpdate' => date('Y-m-d H:i:s'),
            'UpdatedBy'   => $this->session->get('Id'),
        ]);

     
        // history
        // Get updated record
        $after =  $this->areaModel->find($id);
        $before = $area; // existing data

        $this->history->log(
            'areas',
            'UPDATE',
            $before,
            $after,
            session()->get('Id')
        );

        // Fetch with Join to get the actual Name string
         $newArea = $this->areaModel
        ->select('tbl_area.*, tbl_factory.FactoryName, tbl_component.ComponentName')
        ->join('tbl_component', 'tbl_area.ComponentId = tbl_component.Id', 'left')
        ->join('tbl_factory', 'tbl_component.FactoryId = tbl_factory.Id', 'left')
        ->find($id);
        return $this->response->setJSON(['success'=>true,'message'=>'✅ Area updated successfully!','area'=>$newArea]);
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
    $this->checkPermission('AREAS', 'edit');

    $areaModel = new \App\Models\AreaModel();
    $area = $areaModel->find($id);

    if(!$area){
        return $this->response->setJSON(['success' => false, 'message' => 'Area not found']);
    }

    // Toggle
    $newStatus = $area['IsActive'] == 1 ? 0 : 1;

      // History
    $before = $area;

    $areaModel->update($id, ['IsActive' => $newStatus]);

    $after = $areaModel->find($id);

    $this->history->log(
        'areas',
        'STATUS_CHANGE',
        $before,
        $after,
        session()->get('Id')
    );

   
    return $this->response->setJSON([
        'success' => true,
        'newStatus' => $newStatus,
        'message' => $newStatus ? '✅ Area activated' : '✅ Area deactivated'
    ]);
}


}
