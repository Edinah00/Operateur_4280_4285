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
        'operateur_id',
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
        return $this->db->table('operations')
            ->select('operations.*, types_operation.libelle AS type_libelle, compte_source.client_id AS client_id_source, client_source.nom AS client_nom_source, client_source.numero_telephone AS client_numero_source, compte_dest.client_id AS client_id_dest, client_dest.nom AS client_nom_dest, client_dest.numero_telephone AS client_numero_dest')
            ->join('types_operation', 'types_operation.id = operations.type_operation_id')
            ->join('comptes AS compte_source', 'compte_source.id = operations.compte_id')
            ->join('clients AS client_source', 'client_source.id = compte_source.client_id')
            ->join('comptes AS compte_dest', 'compte_dest.id = operations.compte_destinataire_id', 'left')
            ->join('clients AS client_dest', 'client_dest.id = compte_dest.client_id', 'left')
            ->where('operations.compte_id', $compteId)
            ->orderBy('operations.date', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function detailHistorique(int $operationId, int $compteId): ?array
    {
        return $this->db->table('operations')
            ->select('operations.*, types_operation.libelle AS type_libelle, compte_source.client_id AS client_id_source, client_source.nom AS client_nom_source, client_source.numero_telephone AS client_numero_source, compte_dest.client_id AS client_id_dest, client_dest.nom AS client_nom_dest, client_dest.numero_telephone AS client_numero_dest')
            ->join('types_operation', 'types_operation.id = operations.type_operation_id')
            ->join('comptes AS compte_source', 'compte_source.id = operations.compte_id')
            ->join('clients AS client_source', 'client_source.id = compte_source.client_id')
            ->join('comptes AS compte_dest', 'compte_dest.id = operations.compte_destinataire_id', 'left')
            ->join('clients AS client_dest', 'client_dest.id = compte_dest.client_id', 'left')
            ->where('operations.id', $operationId)
            ->where('operations.compte_id', $compteId)
            ->get()
            ->getRowArray() ?: null;
    }
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
