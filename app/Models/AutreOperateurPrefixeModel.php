<?php

namespace App\Models;

use CodeIgniter\Model;

class AutreOperateurPrefixeModel extends Model
{
    protected $table         = 'autre_operateur_prefixes';
    protected $primaryKey    = null; // cle primaire composite, pas d'auto-increment
    protected $allowedFields = [
        'operateur_id',
        'prefixe_id',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    public function listAvecDetails(): array
    {
        return $this->db->table('autre_operateur_prefixes')
            ->select('autre_operateur_prefixes.operateur_id, autre_operateur_prefixes.prefixe_id, prefixes_operateur.prefixe, autre_operateur.nom AS operateur_nom')
            ->join('prefixes_operateur', 'prefixes_operateur.id = autre_operateur_prefixes.prefixe_id')
            ->join('autre_operateur', 'autre_operateur.id = autre_operateur_prefixes.operateur_id')
            ->orderBy('autre_operateur.nom')
            ->get()
            ->getResultArray();
    }
}
