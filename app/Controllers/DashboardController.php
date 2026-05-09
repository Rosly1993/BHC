<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // 1. Critical Inventory Count (Stock < 10)
        $data['criticalCount'] = $this->db->table('tbl_med_inventory')
            ->where('Qty <', 10)->countAllResults();

        // 2. Total Issuances Today
        $data['todayIssuance'] = $this->db->table('tbl_issuance')
            ->where('DATE(DateIssued)', date('Y-m-d'))->countAllResults();

        // 3. Top 5 Patients (Most Issued Records)
        $data['topPatients'] = $this->db->table('tbl_issuance')
            ->select('IssuedTo, COUNT(Id) as visit_count')
            ->groupBy('IssuedTo')
            ->orderBy('visit_count', 'DESC')
            ->limit(5)->get()->getResult();

        return view('dashboard', $data);
    }

    public function getChartData()
    {
        // Data for Top 5 Issued Medications (Pie Chart)
        $issuedMeds = $this->db->table('tbl_issuance i')
            ->select('m.Description, SUM(i.Qty) as total_qty')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->groupBy('i.Med_id')
            ->orderBy('total_qty', 'DESC')
            ->limit(5)->get()->getResult();

        return $this->response->setJSON([
            'issuedMeds' => $issuedMeds
        ]);
    }
}