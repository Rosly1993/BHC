<?php

namespace App\Controllers;

class PurchaseController extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index() {
        $data['meds'] = $this->db->table('tbl_med_list')->where('Isactive', 1)->get()->getResult();
        return view('purchase_med', $data);
    }

    public function getPurchaseHistory() {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        $builder = $this->db->table('tbl_purchases p')
            ->select('p.*, u.firstname as Encoder, 
                      SUM(i.Qty) as TotalQty, 
                      SUM(i.Subtotal) as GrandTotal') 
            ->join('tbl_user u', 'u.id = p.CreatedBy', 'left')
            ->join('tbl_purchase_items i', 'i.PurchaseId = p.Id', 'left')
            ->groupBy('p.Id');

        if ($from && $to) {
            $builder->where('p.PurchaseDate >=', $from)->where('p.PurchaseDate <=', $to);
        } else {
            $builder->where('p.PurchaseDate >=', date('Y-m-01'))->where('p.PurchaseDate <=', date('Y-m-t'));
        }

        return $this->response->setJSON($builder->orderBy('p.PurchaseDate', 'DESC')->get()->getResult());
    }

    public function getPurchaseItems($purchaseId) {
        $items = $this->db->table('tbl_purchase_items i')
            ->select('i.*, m.Description, m.Dosage')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->where('i.PurchaseId', $purchaseId)
            ->get()
            ->getResult();
        return $this->response->setJSON($items);
    }

    public function store() {
        $session = session();
        $file = $this->request->getFile('attachment');
        $fileName = null;

        if ($file && $file->isValid()) {
            $fileName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/receipts', $fileName);
        }

        $this->db->transStart();

        $header = [
            'PurchaseDate'   => $this->request->getPost('PurchaseDate'),
            'ReferenceNo'    => $this->request->getPost('ReferenceNo'),
            'AttachmentPath' => $fileName,
            'CreatedBy'      => $session->get('userId'),
            'CreatedAt'      => date('Y-m-d H:i:s')
        ];
        $this->db->table('tbl_purchases')->insert($header);
        $purchaseId = $this->db->insertID();

        $medIds = $this->request->getPost('med_id');
        $qtys   = $this->request->getPost('qty');
        $prices = $this->request->getPost('price');

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

            // Update Inventory logic remains the same...
            $inv = $this->db->table('tbl_med_inventory')->where('Med_id', $medId)->get()->getRow();
            if ($inv) {
                $this->db->table('tbl_med_inventory')->where('Med_id', $medId)->update([
                    'Qty' => $inv->Qty + $qty,
                    'AddedBy' => $session->get('userId'),
                    'DateAdded' => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->table('tbl_med_inventory')->insert([
                    'Med_id' => $medId, 'Qty' => $qty, 'AddedBy' => $session->get('userId'), 'DateAdded' => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->db->transComplete();
        return $this->response->setJSON(['status' => 'success', 'message' => 'Inventory restocked successfully.']);
    }

    public function exportDetailed() {
        $from = $this->request->getGet('from');
        $to = $this->request->getGet('to');

        $builder = $this->db->table('tbl_purchase_items i')
            ->select('p.PurchaseDate, p.ReferenceNo, m.Description, m.Dosage, i.Qty, i.UnitPrice, i.Subtotal, u.firstname as Encoder')
            ->join('tbl_purchases p', 'p.Id = i.PurchaseId')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->join('tbl_user u', 'u.id = p.CreatedBy');

        if ($from && $to) {
            $builder->where('p.PurchaseDate >=', $from)->where('p.PurchaseDate <=', $to);
        }

        $data = $builder->orderBy('p.PurchaseDate', 'DESC')->get()->getResultArray();

        $filename = "Detailed_Purchase_Report_".date('Ymd').".csv";
        header("Content-Type: text/csv");
        header("Content-Disposition: attachment; filename=$filename");

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Date', 'Reference No', 'Medicine', 'Dosage', 'Qty', 'Unit Price', 'Subtotal', 'Encoder']);
        foreach ($data as $row) { fputcsv($output, $row); }
        fclose($output);
        exit;
    }
}