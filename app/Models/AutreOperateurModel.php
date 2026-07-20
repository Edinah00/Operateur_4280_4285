<?php

namespace App\Models;

use CodeIgniter\Model;

class AutreOperateurModel extends Model
{
    protected $table         = 'autre_operateur';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nom',
        'commission_pourcentage',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    // trouve l'autre operateur associe a un prefixe donne (via autre_operateur_prefixes)
    public function getByPrefixeId(int $prefixeId)
    {
        return $this->select('autre_operateur.*')
            ->join('autre_operateur_prefixes', 'autre_operateur_prefixes.operateur_id = autre_operateur.id')
            ->where('autre_operateur_prefixes.prefixe_id', $prefixeId)
            ->first();
    }

    // trouve l'autre operateur a partir d'un numero de telephone (via son prefixe)
    public function trouverParNumero(string $numero)
    {
        $prefixeSaisi = substr($numero, 0, 3);

        return $this->select('autre_operateur.*')
            ->join('autre_operateur_prefixes', 'autre_operateur_prefixes.operateur_id = autre_operateur.id')
            ->join('prefixes_operateur', 'prefixes_operateur.id = autre_operateur_prefixes.prefixe_id')
            ->where('prefixes_operateur.prefixe', $prefixeSaisi)
            ->first();
    }

    public function listAvecPrefixes(): array
    {
        return $this->db->table('autre_operateur')
            ->select('autre_operateur.id, autre_operateur.nom, autre_operateur.commission_pourcentage, GROUP_CONCAT(prefixes_operateur.prefixe, \', \') AS prefixes')
            ->join('autre_operateur_prefixes', 'autre_operateur_prefixes.operateur_id = autre_operateur.id', 'left')
            ->join('prefixes_operateur', 'prefixes_operateur.id = autre_operateur_prefixes.prefixe_id', 'left')
            ->groupBy('autre_operateur.id, autre_operateur.nom, autre_operateur.commission_pourcentage')
            ->orderBy('autre_operateur.nom')
            ->get()
            ->getResultArray();
    }
}
