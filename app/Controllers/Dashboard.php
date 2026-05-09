<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    protected $db;

    public function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Handle Date Filtering
        $start = $this->request->getGet('start') ?? date('Y-m-01'); 
        $end   = $this->request->getGet('end')   ?? date('Y-m-d');

        // 1. Critical Inventory Count (For the KPI Card)
        $criticalCount = $this->db->table('tbl_med_inventory')
                                  ->where('Qty <', 10)
                                  ->countAllResults();

        // 2. Critical Meds Details (For the Sidebar List)
        $criticalMeds = $this->db->table('tbl_med_inventory i')
            ->select('m.Description, m.Dosage, i.Qty, m.Id')
            ->join('tbl_med_list m', 'm.Id = i.Med_id')
            ->where('i.Qty <', 10)
            ->get()->getResult();

        // 3. Issuance Count for the period
        $issuanceCount = $this->db->table('tbl_issuance')
            ->where('DateIssued >=', $start . ' 00:00:00')
            ->where('DateIssued <=', $end . ' 23:59:59')
            ->countAllResults();

        // 4. Wrap everything in the $data array
        $data = [
            'criticalCount' => $criticalCount,
            'criticalMeds'  => $criticalMeds,
            'issuanceCount' => $issuanceCount,
            'startDate'     => $start,
            'endDate'       => $end,
            'title'         => 'Dashboard'
        ];

        return view('dashboard', $data);
    }

 public function getChartData()
{
    // 1. Top Meds - Force SUM to be a number and group by ID + Description
    $issuedMeds = $this->db->table('tbl_issuance i')
        ->select('m.Description, CAST(SUM(i.Qty) AS UNSIGNED) as total_qty')
        ->join('tbl_med_list m', 'm.Id = i.Med_id')
        ->groupBy('i.Med_id') // Grouping by ID is safer than Description
        ->orderBy('total_qty', 'DESC')
        ->limit(5)
        ->get()->getResult();

    // 2. Daily Trends
    $dailyTrends = $this->db->table('tbl_issuance')
        ->select('DATE(DateIssued) as date_label, COUNT(Id) as count')
        ->where('DateIssued >=', date('Y-m-d', strtotime('-7 days')))
        ->groupBy('date_label')
        ->orderBy('date_label', 'ASC')
        ->get()->getResult();

    return $this->response->setJSON([
        'issuedMeds'  => $issuedMeds,
        'dailyTrends' => $dailyTrends
    ]);
}
}