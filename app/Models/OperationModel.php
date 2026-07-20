<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['compte_id', 'type_operation_id', 'montant', 'frais_applique', 'date', 'compte_destinataire_id'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function gainsParType(): array
    {
        return $this->db->table('v_gains_par_type')
            ->get()
            ->getResultArray();
    }

    public function gainsTotal(): float
    {
        return (float) ($this->selectSum('frais_applique')->first()['frais_applique'] ?? 0);
    }
}