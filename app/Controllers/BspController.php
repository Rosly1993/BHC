<?php

namespace App\Controllers;

use App\Models\BspModel;

class BspController extends BaseController
{
    protected $bspModel;

    public function __construct()
    {
        // The model automatically switches to the 'bsp_data' group 
        // because of the $DBGroup property you set.
        $this->bspModel = new BspModel();
    }

   // App/Controllers/BspController.php

public function index($year = null, $month = null, $date = null)
{
    $data['year']  = $year;
    $data['month'] = $month;
    $data['date']  = $date;

    if ($date) {
        // Step 4: Show the final table data
        $data['rates'] = $this->bspModel->where('DATE(created_at)', $date)->findAll();
        $data['view']  = 'table';
    } elseif ($month) {
        // Step 3: Show Dates Card
        $data['items'] = $this->bspModel->getDatesByMonth($year, $month);
        $data['view']  = 'dates';
    } elseif ($year) {
        // Step 2: Show Months Card
        $data['items'] = $this->bspModel->getMonthsByYear($year);
        $data['view']  = 'months';
    } else {
        // Step 1: Show Years Card
        $data['items'] = $this->bspModel->getYears();
        $data['view']  = 'years';
    }

    return view('bsp_data', $data);
}
public function latest()
{
    // 1. Find the most recent date in the database
    $latestDateRow = $this->bspModel->select('DATE(created_at) as last_date')
                                    ->orderBy('created_at', 'DESC')
                                    ->first();
    
    $lastDate = $latestDateRow['last_date'] ?? date('Y-m-d');

    // 2. Fetch all currencies for that specific date
    $rates = $this->bspModel->where('DATE(created_at)', $lastDate)->findAll();

    $data = [
        'rates'     => $rates,
        'last_date' => $lastDate,
        'title'     => 'Latest Market Rates'
    ];

    return view('bsp_latest', $data);
}
}