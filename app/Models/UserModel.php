<?php
namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'tbl_user';
    protected $allowedFields = ['username', 'password', 'firstname','middlename','lastname','emailaddress','contactnumber','role','isactive'];
}