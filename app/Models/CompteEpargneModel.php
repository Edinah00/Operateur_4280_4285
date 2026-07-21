<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteEpargneModel extends Model
{
    protected $table            = 'comptes_epargne';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'client_id',
        'solde',
        'pourcentage'
    ];

    protected $useTimestamps = false;

    public function getByClientId(int $clientId)
    {
        return $this->where('client_id', $clientId)->first();
    }
}