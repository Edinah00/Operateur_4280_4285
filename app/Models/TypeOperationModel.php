<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table = 'types_operation';
    protected $primaryKey = 'id';
    protected $allowedFields = ['libelle'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}