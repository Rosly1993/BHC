<?php

namespace App\Controllers;

use App\Models\MedListModel;

class IssuanceController extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index() {
        // We need the list of meds that actually have stock to issue
        $data['meds'] = $this->db->table('tbl_med_inventory i')
            ->select('i.Med_id, m.Description, m.Dosage, i.Qty')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->where('i.Qty >', 0)
            ->get()->getResult();

        return view('med_issuance', $data);
    }

    public function store()
    {
        $session = \Config\Services::session();
        $medId   = $this->request->getPost('Med_id');
        $qtyToIssue = (int)$this->request->getPost('Qty');

        // 1. Check current stock
        $inventory = $this->db->table('tbl_med_inventory')->where('Med_id', $medId)->get()->getRow();

        if (!$inventory || $inventory->Qty < $qtyToIssue) {
            return $this->response->setJSON([
                'status' => 'error', 
                'message' => 'Insufficient stock! Available: ' . ($inventory->Qty ?? 0)
            ]);
        }

        $this->db->transStart();

        // 2. Insert Issuance Record
        $this->db->table('tbl_issuance')->insert([
            'Med_id'     => $medId,
            'IssuedTo'   => $this->request->getPost('IssuedTo'),
            'Age'        => $this->request->getPost('Age'),
            'Qty'        => $qtyToIssue,
            'DateIssued' => date('Y-m-d H:i:s'),
            'IssuedBy'   => $session->get('userId')
        ]);

        // 3. Deduct Stock from Inventory
        $this->db->table('tbl_med_inventory')
            ->where('Med_id', $medId)
            ->update(['Qty' => $inventory->Qty - $qtyToIssue]);

        // 4. Log to History
        $this->db->table('tbl_med_history')->insert([
            'Med_id'     => $medId,
            'Action'     => 'ISSUED',
            'Details'    => "Issued $qtyToIssue units to " . $this->request->getPost('IssuedTo'),
            'Qty_Change' => -$qtyToIssue,
            'User_Id'    => $session->get('userId'),
            'Created_At' => date('Y-m-d H:i:s')
        ]);

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Transaction failed.']);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Medication issued successfully!']);
    }

   public function getIssuanceLog()
{
    $start = $this->request->getGet('start');
    $end = $this->request->getGet('end');

    $builder = $this->db->table('tbl_issuance i')
        ->select('i.*, m.Description, m.Dosage, u.firstname as Issuer')
        ->join('tbl_med_list m', 'm.Id = i.Med_id')
        ->join('tbl_user u', 'u.id = i.IssuedBy', 'left');

    if ($start && $end) {
        $builder->where('i.DateIssued >=', $start . ' 00:00:00')
                ->where('i.DateIssued <=', $end . ' 23:59:59');
    }

    $builder->orderBy('i.Id', 'DESC');

    return $this->response->setJSON($builder->get()->getResult());
}
    // Add this to IssuanceController.php
    public function getAvailableMeds()
    {
        $meds = $this->db->table('tbl_med_inventory i')
            ->select('i.Med_id, m.Description, m.Dosage, i.Qty')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->where('i.Qty >', 0)
            ->get()->getResult();

        return $this->response->setJSON($meds);
    }
  
}