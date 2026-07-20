<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table = 'baremes_frais';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operateur_id', 'type_operation_id', 'montant_min', 'montant_max', 'frais'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function listAvecType(int $operateurId): array
    {
        return $this->db->table('v_baremes')
            ->where('operateur_id', $operateurId)
            ->orderBy('libelle')
            ->orderBy('montant_min')
            ->get()
            ->getResultArray();
    }
}