<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;  
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\OperateurModel;
use App\Models\OperateurPrefixeModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;
use Config\Database;

class OperateurController extends BaseController
{
    private const OPERATEUR_ID = 1;

    private function requireLogin()
    {
        if (!session()->get('operateur_id') || session()->get('role') !== 'operateur') {
            return redirect()->to('/operateur/login');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $operateurModel = new OperateurModel();

        return view('operateur/index', [
            'operateurs' => $operateurModel->findAll(),
        ]);
    }

    public function login()
    {
        $operateur = (new OperateurModel())->first();

        if (! $operateur) {
            return redirect()->to('/client/login')->with('erreur', "Aucun opérateur n'est configuré.");
        }

        session()->set([
            'role'          => 'operateur',
            'operateur_id'   => (int) $operateur['id'],
            'operateur_nom'  => $operateur['nom'],
        ]);

        return redirect()->to('/operateur');
    }

    public function logout()
    {
        session()->remove(['role', 'operateur_id', 'operateur_nom']);

        return redirect()->to('/client/login');
    }

    // prefixes
    public function prefixes()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $operateurModel = new OperateurModel();
        $prefixeModel   = new PrefixeModel();
        $association    = new OperateurPrefixeModel();

        return view('operateur/prefixes', [
            'operateurs'  => $operateurModel->findAll(),
            'prefixes'    => $prefixeModel->findAll(),
            'associations'=> $association->listAvecDetails(),
        ]);
    }

    public function ajouterPrefixe()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $prefixe = trim((string) $this->request->getPost('prefixe'));
        $operateurId = $this->request->getPost('operateur_id');
        $nouveauOperateur = trim((string) $this->request->getPost('nouveau_operateur'));

        if ($prefixe === '') {
            return redirect()->back()->withInput()->with('erreur', 'Le préfixe est obligatoire.');
        }

        $db = Database::connect();
        $db->transStart();

        $prefixeModel = new PrefixeModel();
        if ($prefixeModel->where('prefixe', $prefixe)->first()) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Ce préfixe existe déjà.');
        }

        $prefixeId = $prefixeModel->insert(['prefixe' => $prefixe], true);

        if (! $prefixeId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Impossible de créer le préfixe.');
        }

        $operateurModel = new OperateurModel();
        if ($operateurId === 'autre') {
            if ($nouveauOperateur === '') {
                $db->transRollback();
                return redirect()->back()->withInput()->with('erreur', 'Le nom du nouvel opérateur est obligatoire.');
            }

            $operateurId = $operateurModel->insert(['nom' => $nouveauOperateur], true);

            if (! $operateurId) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('erreur', 'Impossible de créer le nouvel opérateur.');
            }
        } else {
            if ($operateurModel->find((int) $operateurId) === null) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('erreur', 'L’opérateur sélectionné est invalide.');
            }
            $operateurId = (int) $operateurId;
        }

        $dejaLie = $db->table('operateur_prefixes')
            ->where('prefixe_id', $prefixeId)
            ->get()
            ->getRowArray();

        if ($dejaLie) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Ce préfixe est déjà associé à un opérateur.');
        }

        $association = new OperateurPrefixeModel();
        $association->insert([
            'operateur_id' => $operateurId,
            'prefixe_id'   => $prefixeId,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('erreur', 'L’ajout du préfixe a échoué.');
        }

        return redirect()->to('/operateur/prefixes')->with('succes', 'Préfixe et association enregistrés.');
    }

    public function supprimerPrefixe(int $id)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        (new OperateurPrefixeModel())->where('prefixe_id', $id)->delete();
        (new PrefixeModel())->delete($id);

        return redirect()->to('/operateur/prefixes');
    }

    public function supprimerAssociationPrefixe(int $operateurId, int $prefixeId)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $db = Database::connect();
        $db->transStart();

        $db->table('operateur_prefixes')
            ->where('operateur_id', $operateurId)
            ->where('prefixe_id', $prefixeId)
            ->delete();

        (new PrefixeModel())->delete($prefixeId);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('erreur', 'La suppression a échoué.');
        }

        return redirect()->to('/operateur/prefixes')->with('succes', 'Préfixe supprimé.');
    }
    
    public function baremes()
{
    if ($redirect = $this->requireLogin()) {
        return $redirect;
    }

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
    if ($redirect = $this->requireLogin()) {
        return $redirect;
    }

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
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        (new BaremeFraisModel())->delete($id);

        return redirect()->to('/operateur/baremes');
    }

    public function modifierBareme(int $id)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        (new BaremeFraisModel())->update($id, [
            'montant_min' => $this->request->getPost('montant_min'),
            'montant_max' => $this->request->getPost('montant_max'),
            'frais' => $this->request->getPost('frais'),
        ]);

        return redirect()->to('/operateur/baremes');
    }

    public function gains()
{
    if ($redirect = $this->requireLogin()) {
        return $redirect;
    }

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
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $model = new CompteModel();

        return view('operateur/comptes', [
            'comptes' => $model->listAvecClient(),
        ]);
    }
}
