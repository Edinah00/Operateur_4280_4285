<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table         = 'operateur';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'nom',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    public function getByPrefixeId(int $prefixeId)
    {
        return $this->select('operateur.*')
            ->join('operateur_prefixes', 'operateur_prefixes.operateur_id = operateur.id')
            ->where('operateur_prefixes.prefixe_id', $prefixeId)
            ->first();
    }

    public function getOperateurIdFromNumero(string $numero): ?int
    {
        $prefixe = substr($numero, 0, 3);

        $prefixeModel = new PrefixeOperateurModel();
        $prefixeRow   = $prefixeModel->getByPrefixe($prefixe);

        if (!$prefixeRow) {
            return null;
        }

        $operateur = $this->getByPrefixeId((int) $prefixeRow['id']);

        return $operateur ? (int) $operateur['id'] : null;
    }

    public function listAvecPrefixes(): array
    {
        return $this->db->table('operateur')
            ->select('operateur.id, operateur.nom, GROUP_CONCAT(prefixes_operateur.prefixe, \', \') AS prefixes')
            ->join('operateur_prefixes', 'operateur_prefixes.operateur_id = operateur.id', 'left')
            ->join('prefixes_operateur', 'prefixes_operateur.id = operateur_prefixes.prefixe_id', 'left')
            ->groupBy('operateur.id, operateur.nom')
            ->orderBy('operateur.nom')
            ->get()
            ->getResultArray();
    }

    public function trouverParNumero(string $numero)
    {
        $prefixeSaisi = substr($numero, 0, 3);

        return $this->select('operateur.*')
                     ->join('operateur_prefixes', 'operateur_prefixes.operateur_id = operateur.id')
                     ->join('prefixes_operateur', 'prefixes_operateur.id = operateur_prefixes.prefixe_id')
                     ->where('prefixes_operateur.prefixe', $prefixeSaisi)
                     ->first();
    }
}
