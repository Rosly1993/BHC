<?php
namespace App\Models;
use CodeIgniter\Model;

class PurchaseModel extends Model {
    protected $table = 'tbl_purchases';
    protected $primaryKey = 'Id';
    protected $allowedFields = ['PurchaseDate', 'ReferenceNo', 'TotalAmount', 'AttachmentPath', 'CreatedBy', 'CreatedAt'];
}