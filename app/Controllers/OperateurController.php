<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;  
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;
use App\Models\OperateurModel;
class OperateurController extends BaseController
{
    private const OPERATEUR_ID = 1;

    // prefixes
    public function prefixes()
    {
        $model = new PrefixeModel();

        return view('operateur/prefixes', [
            'prefixes' => $model->findAll(),
        ]);
    }

    public function ajouterPrefixe()
    {
        $prefixe = $this->request->getPost('prefixe');

        if ($prefixe !== null && $prefixe !== '') {
            (new PrefixeModel())->insert(['prefixe' => $prefixe]);
        }

        return redirect()->to('/operateur/prefixes');
    }

    public function supprimerPrefixe(int $id)
    {
        (new PrefixeModel())->delete($id);

        return redirect()->to('/operateur/prefixes');
    }
    
    public function baremes()
{
    $operateurModel = new OperateurModel();
    $baremeModel    = new BaremeFraisModel();

    $operateurId = $this->request->getGet('operateur_id') ?? 1; // operateur par defaut/selectionne

    $data['operateurs']     = $operateurModel->findAll();
    $data['operateur_id']   = $operateurId;
    $data['baremes']        = $baremeModel->listAvecType($operateurId);
    $data['types']          = (new TypeOperationModel())->findAll();

    return view('operateur/baremes', $data);
}

public function ajouterBareme()
{
    $baremeModel = new BaremeFraisModel();
    $baremeModel->insert([
        'operateur_id'       => $this->request->getPost('operateur_id'), // <-- manquant avant
        'type_operation_id'  => $this->request->getPost('type_operation_id'),
        'montant_min'        => $this->request->getPost('montant_min'),
        'montant_max'        => $this->request->getPost('montant_max'),
        'frais'              => $this->request->getPost('frais'),
    ]);
    return redirect()->back();
}
    public function supprimerBareme(int $id)
    {
        (new BaremeFraisModel())->delete($id);

        return redirect()->to('/operateur/baremes');
    }

    public function modifierBareme(int $id)
    {
        (new BaremeFraisModel())->update($id, [
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais'),
        ]);

        return redirect()->to('/operateur/baremes');
    }

    public function gains()
{
    $operateurModel = new OperateurModel();
    $operationModel = new OperationModel();

    $operateurId = $this->request->getGet('operateur_id') ?? 1;

    $data['operateurs']   = $operateurModel->findAll();
    $data['operateur_id'] = $operateurId;

    // gains totaux, uniquement retrait + transfert (pas le depot)
    $data['total_gains'] = $operationModel
        ->selectSum('frais_applique', 'total')
        ->where('operateur_id', $operateurId)
        ->whereIn('type_operation_id', [2, 3]) // 2 = retrait, 3 = transfert
        ->first();

    // detail par type, pour affichage plus lisible
    $data['gains_par_type'] = $operationModel
        ->select('types_operation.libelle, SUM(operations.frais_applique) as total')
        ->join('types_operation', 'types_operation.id = operations.type_operation_id')
        ->where('operations.operateur_id', $operateurId)
        ->whereIn('operations.type_operation_id', [2, 3])
        ->groupBy('types_operation.libelle')
        ->findAll();

    return view('operateur/gains', $data);
}
    // situation des comptes clients
    public function comptes()
    {
        $model = new CompteModel();

        return view('operateur/comptes', [
            'comptes' => $model->listAvecClient(),
        ]);
    }
}