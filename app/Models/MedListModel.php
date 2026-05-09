<?php

namespace App\Models;

use CodeIgniter\Model;

class MedListModel extends Model
{
    protected $table      = 'tbl_med_list';
    protected $primaryKey = 'Id'; // Explicitly set to capitalized 'Id'
    
    // Ensure these match your database column casing exactly
    protected $allowedFields = ['Description', 'Dosage', 'Type', 'Isactive', 'AddedBy','DateAdded'];
}