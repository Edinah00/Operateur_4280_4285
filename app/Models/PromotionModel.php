<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    protected $table = 'promotion';
    protected $primaryKey = 'id';
    protected $allowedFields = ['type_operation_id','promotion_pourcentage'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}