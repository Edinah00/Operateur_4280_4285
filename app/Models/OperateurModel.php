<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table         = 'operateur';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    public function getByPrefixeId(int $prefixeId)
    {
        return $this->where('prefixe_id', $prefixeId)->first();
    }

    public function getOperateurIdFromNumero(string $numero): ?int
    {
        $prefixe = substr($numero, 0, 3);

        $prefixeModel = new PrefixeOperateurModel();
        $prefixeRow   = $prefixeModel->getByPrefixe($prefixe);

        if (!$prefixeRow) {
            return null;
        }

        $operateur = $this->getByPrefixeId((int) $prefixeRow['id']);

        return $operateur ? (int) $operateur['id'] : null;
    }
}