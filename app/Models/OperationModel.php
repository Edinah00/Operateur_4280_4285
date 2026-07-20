<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table            = 'operations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'compte_id',
        'type_operation_id',
        'montant',
        'frais_applique',
        'compte_destinataire_id',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'date';
    protected $updatedField  = '';

    public function historique(int $compteId)
    {
        return $this->db
            ->table('v_historique_operations')
            ->where('compte_id', $compteId)
            ->orderBy('date', 'DESC')
            ->get()
            ->getResultArray();
    }
}