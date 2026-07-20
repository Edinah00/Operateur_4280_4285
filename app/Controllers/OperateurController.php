<?php

namespace App\Controllers;

use App\Models\BaremeFraisModel;  
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\OperateurModel;
use App\Models\OperateurPrefixeModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;
use App\Models\AutreOperateurModel;
use App\Models\AutreOperateurPrefixeModel;
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
    $commission = (float) $this->request->getPost('commission') ?: 0;

    if ($prefixe === '') {
        return redirect()->back()->withInput()->with('erreur', 'Le préfixe est obligatoire.');
    }

    $db = Database::connect();
    $db->transStart();

    // 1. Vérifier si le préfixe existe déjà
    $prefixeModel = new PrefixeModel();
    if ($prefixeModel->where('prefixe', $prefixe)->first()) {
        $db->transRollback();
        return redirect()->back()->withInput()->with('erreur', 'Ce préfixe existe déjà.');
    }

    // 2. Créer le préfixe
    $prefixeId = $prefixeModel->insert(['prefixe' => $prefixe], true);

    if (!$prefixeId) {
        $db->transRollback();
        return redirect()->back()->withInput()->with('erreur', 'Impossible de créer le préfixe.');
    }

    // 3. Gérer l'opérateur
    if ($operateurId === 'autre') {
        // Cas "Autre" : créer un nouvel opérateur dans autre_operateur
        if ($nouveauOperateur === '') {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Le nom du nouvel opérateur est obligatoire.');
        }

        // Insérer dans la table autre_operateur
        $autreOperateurModel = new AutreOperateurModel();
        $autreOperateurId = $autreOperateurModel->insert([
            'nom' => $nouveauOperateur,
            'commission_pourcentage' => $commission
        ], true);

        if (!$autreOperateurId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Impossible de créer le nouvel opérateur.');
        }

        // 4. Associer le préfixe à cet autre opérateur
        $db->table('autre_operateur_prefixes')->insert([
            'operateur_id' => $autreOperateurId,
            'prefixe_id' => $prefixeId
        ]);

    } else {
        // Cas "Opérateur existant" : vérifier qu'il existe dans la table operateur
        $operateurModel = new OperateurModel();
        $operateur = $operateurModel->find((int) $operateurId);
        
        if ($operateur === null) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'L\'opérateur sélectionné est invalide.');
        }

        // Vérifier si le préfixe est déjà associé à un opérateur (principal ou autre)
        $dejaLie = $db->table('operateur_prefixes')
            ->where('prefixe_id', $prefixeId)
            ->get()
            ->getRowArray();

        if ($dejaLie) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Ce préfixe est déjà associé à un opérateur.');
        }

        // 4. Associer le préfixe à l'opérateur principal
        $db->table('operateur_prefixes')->insert([
            'operateur_id' => (int) $operateurId,
            'prefixe_id' => $prefixeId
        ]);
    }

    $db->transComplete();

    if (!$db->transStatus()) {
        return redirect()->back()->withInput()->with('erreur', 'L\'ajout du préfixe a échoué.');
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

////AUTRE OPERATEUR
    public function autresOperateurs()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $autreOperateurModel = new AutreOperateurModel();
        $prefixeModel        = new PrefixeModel();
        $association         = new AutreOperateurPrefixeModel();

        return view('operateur/autres_operateurs', [
            'autresOperateurs' => $autreOperateurModel->findAll(),
            'prefixes'         => $prefixeModel->findAll(),
            'associations'     => $association->listAvecDetails(),
        ]);
    }

    public function ajouterAutreOperateur()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $prefixe    = trim((string) $this->request->getPost('prefixe'));
        $nom        = trim((string) $this->request->getPost('nom'));
        $commission = $this->request->getPost('commission_pourcentage');

        if ($prefixe === '' || $nom === '') {
            return redirect()->back()->withInput()->with('erreur', 'Le préfixe et le nom sont obligatoires.');
        }

        if ($commission === null || $commission === '' || (float) $commission < 0) {
            return redirect()->back()->withInput()->with('erreur', 'Commission invalide.');
        }

        $db = Database::connect();
        $db->transStart();

        $prefixeModel = new PrefixeModel();
        if ($prefixeModel->where('prefixe', $prefixe)->first()) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Ce préfixe existe déjà (interne ou externe).');
        }

        $prefixeId = $prefixeModel->insert(['prefixe' => $prefixe], true);

        $autreOperateurModel = new AutreOperateurModel();
        $operateurId = $this->request->getPost('operateur_id');

        if ($operateurId === 'nouveau' || !$operateurId) {
            $operateurId = $autreOperateurModel->insert([
                'nom'                     => $nom,
                'commission_pourcentage'  => $commission,
            ], true);
        } else {
            $operateurId = (int) $operateurId;
            if ($autreOperateurModel->find($operateurId) === null) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('erreur', 'Opérateur externe invalide.');
            }
        }

        if (! $prefixeId || ! $operateurId) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', "L'ajout a échoué.");
        }

        (new AutreOperateurPrefixeModel())->insert([
            'operateur_id' => $operateurId,
            'prefixe_id'   => $prefixeId,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('erreur', "L'ajout a échoué.");
        }

        return redirect()->to('/operateur/autres-operateurs')->with('succes', 'Opérateur externe et préfixe enregistrés.');
    }

    public function supprimerAutreOperateurPrefixe(int $operateurId, int $prefixeId)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $db = Database::connect();
        $db->transStart();

        $db->table('autre_operateur_prefixes')
            ->where('operateur_id', $operateurId)
            ->where('prefixe_id', $prefixeId)
            ->delete();

        (new PrefixeModel())->delete($prefixeId);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('erreur', 'La suppression a échoué.');
        }

        return redirect()->to('/operateur/autres-operateurs')->with('succes', 'Préfixe externe supprimé.');
    }

    public function modifierCommission(int $id)
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $commission = $this->request->getPost('commission_pourcentage');

        if ($commission === null || $commission === '' || (float) $commission < 0) {
            return redirect()->back()->with('erreur', 'Commission invalide.');
        }

        (new AutreOperateurModel())->update($id, [
            'commission_pourcentage' => $commission,
        ]);

        return redirect()->to('/operateur/autres-operateurs')->with('succes', 'Commission mise à jour.');
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

    // gains internes : transferts/retraits vers nos propres clients (autre_operateur_id NULL)
    $data['total_gains_interne'] = $operationModel
        ->selectSum('frais_applique', 'total')
        ->where('operateur_id', $operateurId)
        ->whereIn('type_operation_id', [2, 3])
        ->where('autre_operateur_id IS NULL')
        ->first();

    // gains externes : le frais de transfert reste chez nous meme si le destinataire est chez un autre operateur
    $data['total_gains_externe'] = $operationModel
        ->selectSum('frais_applique', 'total')
        ->where('operateur_id', $operateurId)
        ->whereIn('type_operation_id', [2, 3])
        ->where('autre_operateur_id IS NOT NULL')
        ->first();

    // detail par type, uniquement les operations internes (comme en v1)
    $data['gains_par_type'] = $operationModel
        ->select('types_operation.libelle, SUM(operations.frais_applique) as total')
        ->join('types_operation', 'types_operation.id = operations.type_operation_id')
        ->where('operations.operateur_id', $operateurId)
        ->whereIn('operations.type_operation_id', [2, 3])
        ->where('operations.autre_operateur_id IS NULL')
        ->groupBy('types_operation.libelle')
        ->findAll();

    return view('operateur/gains', $data);
}

    // montants (+ commissions) a reverser a chaque operateur externe suite aux transferts sortants
    public function montantsAEnvoyer()
    {
        if ($redirect = $this->requireLogin()) {
            return $redirect;
        }

        $operationModel = new OperationModel();

        $data['lignes'] = $operationModel
            ->select('autre_operateur.nom, SUM(operations.montant) AS montant_total, SUM(operations.commission_appliquee) AS commission_total, COUNT(*) AS nombre')
            ->join('autre_operateur', 'autre_operateur.id = operations.autre_operateur_id')
            ->where('operations.autre_operateur_id IS NOT NULL')
            ->groupBy('autre_operateur.nom')
            ->orderBy('autre_operateur.nom')
            ->findAll();

        return view('operateur/montants_a_envoyer', $data);
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
