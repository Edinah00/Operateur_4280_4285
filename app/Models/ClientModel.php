<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'nom',
        'numero_telephone',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'date_creation';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules = [
        'nom' => [
            'label' => 'Nom',
            'rules' => 'required|min_length[3]|max_length[255]',
        ],
        'numero_telephone' => [
            'label' => 'Numéro de téléphone',
            'rules' => 'required|min_length[10]|max_length[13]|regex_match[/^[0-9+]+$/]|is_unique[clients.numero_telephone,id,{id}]',
        ],
    ];

    protected $validationMessages = [
        'nom' => [
            'required'   => 'Le nom du client est obligatoire.',
            'min_length' => 'Le nom doit contenir au moins {param} caractères.',
            'max_length' => 'Le nom ne peut pas dépasser {param} caractères.',
        ],
        'numero_telephone' => [
            'required'    => 'Le numéro de téléphone est obligatoire.',
            'min_length'  => 'Le numéro de téléphone doit contenir au moins {param} caractères.',
            'max_length'  => 'Le numéro de téléphone ne peut pas dépasser {param} caractères.',
            'regex_match' => 'Le numéro de téléphone ne doit contenir que des chiffres (et éventuellement un "+").',
            'is_unique'   => 'Ce numéro de téléphone est déjà utilisé par un autre client.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getClientByPhoneNumber($numero_telephone)
    {
        return $this->where('numero_telephone', $numero_telephone)->first();
    }
}