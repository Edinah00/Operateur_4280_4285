<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table         = 'baremes_frais';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    public function getFrais(int $operateurId, int $typeOperationId, float $montant): ?float
    {
        $row = $this->where('operateur_id', $operateurId)
            ->where('type_operation_id', $typeOperationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->first();

        return $row ? (float) $row['frais'] : null;
    }
    public function listAvecType(int $operateurId): array
    {
        return $this->select('baremes_frais.*, types_operation.libelle')
            ->join('types_operation', 'types_operation.id = baremes_frais.type_operation_id')
            ->where('baremes_frais.operateur_id', $operateurId)
            ->orderBy('types_operation.libelle')
            ->orderBy('baremes_frais.montant_min')
            ->findAll();
    }
}