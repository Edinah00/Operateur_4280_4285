<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table            = 'comptes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'client_id',
        'solde',
    ];

    protected $useTimestamps = false;

    public function getByClientId(int $clientId)
    {
        return $this->where('client_id', $clientId)->first();
    }
    public function listAvecClient(): array
    {
        return $this->db->table('v_comptes')
            ->orderBy('nom')
            ->get()
            ->getResultArray();
    }
}