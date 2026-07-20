<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurPrefixeModel extends Model
{
    protected $table         = 'operateur_prefixes';
    protected $primaryKey    = 'operateur_id';
    protected $useAutoIncrement = false;
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'operateur_id',
        'prefixe_id',
    ];

    public function listAvecDetails(): array
    {
        return $this->db->table('operateur_prefixes')
            ->select('operateur_prefixes.operateur_id, operateur_prefixes.prefixe_id, operateur.nom AS operateur_nom, prefixes_operateur.prefixe')
            ->join('operateur', 'operateur.id = operateur_prefixes.operateur_id')
            ->join('prefixes_operateur', 'prefixes_operateur.id = operateur_prefixes.prefixe_id')
            ->orderBy('operateur.nom')
            ->orderBy('prefixes_operateur.prefixe')
            ->get()
            ->getResultArray();
    }
}
