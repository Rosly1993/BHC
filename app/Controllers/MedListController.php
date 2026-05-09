<?php

namespace App\Controllers;

use App\Models\MedListModel;

class MedListController extends BaseController
{
    protected $medlistModel;
    protected $db;

    public function __construct()
    {
        $this->medlistModel = new MedListModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Fetch dropdown options from source tables
        $data['types'] = $this->db->table('tbl_type')->where('Isactive', 1)->get()->getResult();
        $data['dosages'] = $this->db->table('tbl_dosage')->where('Isactive', 1)->get()->getResult();
        
        return view('med_list', $data);
    }
    public function getInventory()
{
    $db = \Config\Database::connect();
    $builder = $db->table('tbl_med_inventory i');
    $builder->select('i.*, m.Description, m.Dosage, u.firstname');
    $builder->join('tbl_med_list m', 'm.Id = i.Med_id');
    
    // Safety check: ensure tbl_users actually exists or use the correct name
    $builder->join('tbl_user u', 'u.id = i.AddedBy', 'left'); 
    
    $query = $builder->get();
    
    // If the query fails or returns nothing, return an empty array to avoid DataTable errors
    return $this->response->setJSON($query->getResult() ?? []);
}

    public function getMedList()
    {
        $request = service('request');
        $postData = $request->getPost();

        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length'];
        $searchValue = $postData['search']['value'];

        $totalRecords = $this->medlistModel->countAll();

        $totalRecordwithFilter = $this->medlistModel->like('Description', $searchValue)
                                    ->orLike('Type', $searchValue)
                                    ->countAllResults();

        $records = $this->medlistModel->like('Description', $searchValue)
                    ->orLike('Type', $searchValue)
                    ->orderBy('Id', 'DESC') // Using 'Id'
                    ->findAll($rowperpage, $start);

        $data = [];
        foreach($records as $record ){
            $data[] = [ 
                "Id"          => $record['Id'], // Capitalized
                "Description" => $record['Description'],
                "Dosage"      => $record['Dosage'],
                "Type"        => $record['Type'],
                "Isactive"    => $record['Isactive'],
            ];
        }

        return $this->response->setJSON([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $data
        ]);
    }

  public function store()
{
    $session = \Config\Services::session();
    $userId = $session->get('userId');
    $userName = $session->get('fullName'); // Useful for the "Details" string

    $Id = $this->request->getPost('Id');
    $description = $this->request->getPost('Description');
    $dosage = $this->request->getPost('Dosage');
    $type = $this->request->getPost('Type');
    $quantityInput = $this->request->getPost('Quantity') ?? 0;

    // 1. Duplicate Check
    $builder = $this->medlistModel->builder();
    $builder->where(['Description' => $description, 'Dosage' => $dosage, 'Type' => $type]);
    if (!empty($Id)) { $builder->where('Id !=', $Id); }
    if ($builder->get()->getRow()) {
        return $this->response->setJSON(['status' => 'error', 'message' => "Duplicate medication detected."]);
    }

    // 2. Prepare Action & History Details
    $isEdit = !empty($Id);
    $action = $isEdit ? 'UPDATED' : 'CREATED';
    $historyDetails = $isEdit ? "Updated medication details." : "Registered new medication.";
    
    // 3. Save/Update Med List
    $payload = [
        'Description' => $description,
        'Dosage'      => $dosage,
        'Type'        => $type,
        'Isactive'    => $this->request->getPost('Isactive') ?? 1
    ];

    if ($isEdit) {
        $payload['Id'] = $Id;
    } else {
        $payload['AddedBy'] = $userId;
        $payload['DateAdded'] = date('Y-m-d H:i:s');
    }

    $this->medlistModel->save($payload);
    $finalMedId = $isEdit ? $Id : $this->medlistModel->insertID();

    // 4. Handle Inventory Upsert & History Log
    if ($quantityInput > 0) {
        $inventoryBuilder = $this->db->table('tbl_med_inventory');
        $invRecord = $inventoryBuilder->where('Med_id', $finalMedId)->get()->getRow();

        if ($invRecord) {
            $inventoryBuilder->where('Med_id', $finalMedId)->update([
                'Qty' => $invRecord->Qty + $quantityInput,
                'AddedBy' => $userId,
                'DateAdded' => date('Y-m-d H:i:s'),
            ]);
            $historyDetails .= " Stock increased by $quantityInput.";
            $action = 'STOCK_IN';
        } else {
            $inventoryBuilder->insert([
                'Med_id'     => $finalMedId,
                'Qty'        => $quantityInput,
                'DateAdded' => date('Y-m-d H:i:s'),
                'AddedBy'    => $userId
            ]);
            $historyDetails .= " Initial stock set to $quantityInput.";
        }
    }

    // 5. RECORD TO HISTORY TABLE
    $this->db->table('tbl_med_history')->insert([
        'Med_id'     => $finalMedId,
        'Action'     => $action,
        'Details'    => $historyDetails,
        'Qty_Change' => $quantityInput,
        'User_Id'    => $userId,
        'Created_At' => date('Y-m-d H:i:s')
    ]);

    return $this->response->setJSON(['status' => 'success', 'message' => 'Record and History updated!']);
}
    public function delete($id)
    {
        if ($this->medlistModel->delete($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Record deleted.']);
        }
    }
    public function getHistory($medId)
{
    $db = \Config\Database::connect();
    $history = $db->table('tbl_med_history h')
        ->select('h.*, u.firstname')
        ->join('tbl_user u', 'u.id = h.User_Id', 'left')
        ->where('h.Med_id', $medId)
        ->orderBy('h.Created_At', 'DESC')
        ->get()
        ->getResult();

    return $this->response->setJSON($history);
}
}