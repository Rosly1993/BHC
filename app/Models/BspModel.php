<?php

namespace App\Models;

use CodeIgniter\Model;

class BspModel extends Model
{
    protected $DBGroup          = 'bsp_data'; 
    protected $table            = 'bsp_rates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'country',
        'unit',
        'symbol',
        'euro_rate',
        'usd_rate', // Added based on your table description
        'php_rate',
        'created_at',
        'reference_text',
        'file_name'
    ];

    protected $useTimestamps = false;

    // --- HELPER METHODS START HERE (Inside the class) ---

    public function getYears()
    {
        return $this->select("YEAR(created_at) as year")
                    ->distinct()
                    ->orderBy('year', 'DESC')
                    ->findAll();
    }

    public function getMonthsByYear($year)
    {
        return $this->select("MONTH(created_at) as month_num, MONTHNAME(created_at) as month_name")
                    ->where("YEAR(created_at)", $year)
                    ->distinct()
                    ->orderBy('month_num', 'ASC')
                    ->findAll();
    }

    public function getDatesByMonth($year, $month)
    {
        // We use DATE() to group by day regardless of the time stored
        return $this->select("DATE(created_at) as full_date")
                    ->where("YEAR(created_at)", $year)
                    ->where("MONTH(created_at)", $month)
                    ->distinct()
                    ->orderBy('full_date', 'DESC')
                    ->findAll();
    }
} // <--- Make sure this brace is at the very end of the file