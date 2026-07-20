<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table         = 'types_operation';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    public function getIdByLibelle(string $libelle): ?int
    {
        $row = $this->where('libelle', $libelle)->first();
        return $row ? (int) $row['id'] : null;
    }
}