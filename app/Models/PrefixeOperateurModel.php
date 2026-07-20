<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table         = 'prefixes_operateur';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    public function getByPrefixe(string $prefixe)
    {
        return $this->where('prefixe', $prefixe)->first();
    }
}