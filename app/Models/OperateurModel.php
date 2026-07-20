<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table         = 'operateur';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'prefixe_id',
        'nom',
    ];
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
    public function trouverParNumero(string $numero)
    {
        $prefixeSaisi = substr($numero, 0, 3);

        return $this->select('operateur.*')
                     ->join('prefixes_operateur', 'prefixes_operateur.id = operateur.prefixe_id')
                     ->where('prefixes_operateur.prefixe', $prefixeSaisi)
                     ->first();
    }
}