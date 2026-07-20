<?php

namespace App\Models;

use CodeIgniter\Model;

class CompteModel extends Model
{
    protected $table = 'comptes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'solde'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function listAvecClient(): array
    {
        return $this->db->table('v_comptes')
            ->orderBy('nom')
            ->get()
            ->getResultArray();
    }
}