<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table         = 'operateur';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'prefixe_id',
        'nom',
    ];
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    // retrouve l'operateur a partir d'un numero de telephone (via son prefixe)
    public function trouverParNumero(string $numero)
    {
        $prefixeSaisi = substr($numero, 0, 3);

        return $this->select('operateur.*')
                     ->join('prefixes_operateur', 'prefixes_operateur.id = operateur.prefixe_id')
                     ->where('prefixes_operateur.prefixe', $prefixeSaisi)
                     ->first();
    }
}