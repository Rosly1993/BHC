<?php

namespace App\Controllers;

use App\Models\PurchaseModel;

class PurchaseController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $data['meds'] = $this->db->table('tbl_med_list')->where('Isactive', 1)->get()->getResult();
        return view('purchase_med', $data);
    }

    public function getPurchaseHistory()
    {
        $fromDate = $this->request->getGet('from');
        $toDate = $this->request->getGet('to');

        $builder = $this->db->table('tbl_purchases p')
            ->select('p.*, u.firstname as Encoder')
            ->join('tbl_user u', 'u.id = p.CreatedBy', 'left');

        // Apply filters if dates are provided
        if ($fromDate && $toDate) {
            $builder->where('p.PurchaseDate >=', $fromDate);
            $builder->where('p.PurchaseDate <=', $toDate);
        } else {
            // Default: Current Month
            $builder->where('p.PurchaseDate >=', date('Y-m-01'));
            $builder->where('p.PurchaseDate <=', date('Y-m-t'));
        }

        $builder->orderBy('p.PurchaseDate', 'DESC');
        return $this->response->setJSON($builder->get()->getResult());
    }
    public function getPurchaseItems($purchaseId)
    {
        $items = $this->db->table('tbl_purchase_items i')
            ->select('i.*, m.Description, m.Dosage')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->where('i.PurchaseId', $purchaseId)
            ->get()
            ->getResult();

        return $this->response->setJSON($items);
    }

    public function store()
    {
        $session = \Config\Services::session();
        $file = $this->request->getFile('attachment');
        $fileName = null;

        if ($file && $file->isValid()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/receipts', $fileName);
        }

        $this->db->transStart();

        // 1. Insert Purchase Header
        $header = [
            'PurchaseDate'   => $this->request->getPost('PurchaseDate'),
            'ReferenceNo'    => $this->request->getPost('ReferenceNo'),
            'AttachmentPath' => $fileName,
            'CreatedBy'      => $session->get('userId'),
            'CreatedAt'      => date('Y-m-d H:i:s')
        ];
        $this->db->table('tbl_purchases')->insert($header);
        $purchaseId = $this->db->insertID();

        // 2. Process Multiple Items
        $medIds = $this->request->getPost('med_id');
        $qtys   = $this->request->getPost('qty');
        $prices = $this->request->getPost('price');

        if ($medIds) {
            foreach ($medIds as $index => $medId) {
                $qty = (int)$qtys[$index];
                $price = (float)$prices[$index];

                $this->db->table('tbl_purchase_items')->insert([
                    'PurchaseId' => $purchaseId,
                    'Med_id'     => $medId,
                    'Qty'        => $qty,
                    'UnitPrice'  => $price,
                    'Subtotal'   => $qty * $price
                ]);

                // 3. Update Inventory
                $inv = $this->db->table('tbl_med_inventory')->where('Med_id', $medId)->get()->getRow();
                if ($inv) {
                    $this->db->table('tbl_med_inventory')->where('Med_id', $medId)->update([
                        'Qty' => $inv->Qty + $qty, 
                        'AddedBy' => $session->get('userId'), 
                        'DateAdded' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    $this->db->table('tbl_med_inventory')->insert([
                        'Med_id' => $medId, 
                        'Qty' => $qty, 
                        'AddedBy' => $session->get('userId'), 
                        'DateAdded' => date('Y-m-d H:i:s')
                    ]);
                }

                // 4. Log History
                $this->db->table('tbl_med_history')->insert([
                    'Med_id' => $medId, 
                    'Action' => 'PURCHASE', 
                    'Qty_Change' => $qty,
                    'Details' => "Added $qty units via Purchase Ref: " . $header['ReferenceNo'],
                    'User_Id' => $session->get('userId'), 
                    'Created_At' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->db->transComplete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'Inventory restocked successfully.']);
    }
}