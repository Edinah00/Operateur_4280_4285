<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientModel;
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\OperateurModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use Config\Database;

class ClientController extends BaseController
{
    protected $clientModel;
    protected $compteModel;
    protected $operationModel;
    protected $operateurModel;
    protected $typeOperationModel;
    protected $baremeFraisModel;

    public function __construct()
    {
        $this->clientModel        = new ClientModel();
        $this->compteModel        = new CompteModel();
        $this->operationModel     = new OperationModel();
        $this->operateurModel     = new OperateurModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->baremeFraisModel   = new BaremeFraisModel();
    }

    private function requireLogin()
    {
        if (!session()->get('client_id') || session()->get('role') !== 'client') {
            return redirect()->to('/client/login');
        }
        return null;
    }

    private function getOrCreateCompteForClient(int $clientId): array
    {
        $compte = $this->compteModel->getByClientId($clientId);

        if ($compte) {
            return $compte;
        }

        $compteId = $this->compteModel->insert([
            'client_id' => $clientId,
            'solde'     => 0,
        ], true);

        return $this->compteModel->find($compteId);
    }

    public function index()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $compte = $this->getOrCreateCompteForClient((int) session()->get('client_id'));
        return view('client/dashboard', ['compte' => $compte]);
    }

    // ---------- Login ----------

    public function showLoginForm()
    {
        return view('client/login');
    }

    public function login()
    {
        $numero_telephone = trim($this->request->getPost('numero_telephone'));
        $nom = trim($this->request->getPost('nom')) ?: $numero_telephone;

        if (empty($numero_telephone)) {
            return redirect()->back()->with('erreur', 'Numéro requis.');
        }

        if ($this->operateurModel->getOperateurIdFromNumero($numero_telephone) === null) {
            return redirect()->back()->with('erreur', 'Préfixe inconnu, numéro invalide.');
        }

        $client = $this->clientModel->getClientByPhoneNumber($numero_telephone);

        if (!$client) {
            $clientId = $this->clientModel->insert([
                'nom'              => $nom,
                'numero_telephone' => $numero_telephone,
            ]);

            if ($clientId === false) {
                return redirect()->back()->withInput()->with('errors', $this->clientModel->errors());
            }

            $this->compteModel->insert([
                'client_id' => $clientId,
                'solde'     => 0,
            ]);
            $client = $this->clientModel->find($clientId);
        }

        $this->getOrCreateCompteForClient((int) $client['id']);

        session()->set([
            'role'          => 'client',   // ← ajouté
            'client_id'     => $client['id'],
            'client_nom'    => $client['nom'],
            'client_numero' => $client['numero_telephone'],
        ]);

        return redirect()->to('/client');
    }

    public function logout()
    {
        session()->remove(['role', 'client_id', 'client_nom', 'client_numero']);
        return redirect()->to('/client/login');
    }

    // ---------- Dépôt ----------

    public function depot()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('client/depot');
    }

    public function doDepot()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $montant = (float) $this->request->getPost('montant');
        if ($montant <= 0) {
            return redirect()->back()->with('erreur', 'Montant invalide.');
        }

        $typeId = $this->typeOperationModel->getIdByLibelle('depot');
        $operateurId = $this->operateurModel->getOperateurIdFromNumero((string) session()->get('client_numero'));

        if ($typeId === null || $operateurId === null) {
            return redirect()->back()->with('erreur', 'Configuration des opérations indisponible.');
        }

        $db = Database::connect();
        $db->transStart();
        $compte = $this->getOrCreateCompteForClient((int) session()->get('client_id'));

        $this->compteModel->update($compte['id'], [
            'solde' => $compte['solde'] + $montant,
        ]);

        $this->operationModel->insert([
            'compte_id'         => $compte['id'],
            'type_operation_id' => $typeId,
            'operateur_id'      => $operateurId,
            'montant'           => $montant,
            'frais_applique'    => 0,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('erreur', 'Le dépôt a échoué. Veuillez réessayer.');
        }

        return redirect()->to('/client')->with('succes', 'Dépôt effectué.');
    }

    // ---------- Retrait ----------

    public function retrait()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('client/retrait');
    }

    public function doRetrait()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $montant = (float) $this->request->getPost('montant');
        $numero  = session()->get('client_numero');
        $compte  = $this->getOrCreateCompteForClient((int) session()->get('client_id'));
        $operateurId = $this->operateurModel->getOperateurIdFromNumero($numero);
        $typeId      = $this->typeOperationModel->getIdByLibelle('retrait');

        if ($operateurId === null || $typeId === null) {
            return redirect()->back()->with('erreur', 'Configuration des opérations indisponible.');
        }

        $frais       = $this->baremeFraisModel->getFrais($operateurId, $typeId, $montant);

        if ($frais === null) {
            return redirect()->back()->with('erreur', 'Aucun barème trouvé pour ce montant.');
        }

        if ($compte['solde'] < ($montant + $frais)) {
            return redirect()->back()->with('erreur', 'Solde insuffisant (montant + frais).');
        }

        $db = Database::connect();
        $db->transStart();

        $this->compteModel->update($compte['id'], [
            'solde' => $compte['solde'] - ($montant + $frais),
        ]);

        $this->operationModel->insert([
            'compte_id'         => $compte['id'],
            'type_operation_id' => $typeId,
            'operateur_id'      => $operateurId,
            'montant'           => $montant,
            'frais_applique'    => $frais,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('erreur', 'Le retrait a échoué. Veuillez réessayer.');
        }

        return redirect()->to('/client')->with('succes', "Retrait effectué (frais: $frais Ar).");
    }

    // ---------- Transfert ----------

    public function transfert()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('client/transfert');
    }

    public function doTransfert()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $montant    = (float) $this->request->getPost('montant');
        $numeroDest = trim($this->request->getPost('numero_destinataire'));
        $numeroEmet = session()->get('client_numero');
        $compteEmet = $this->getOrCreateCompteForClient((int) session()->get('client_id'));
        $operateurId = $this->operateurModel->getOperateurIdFromNumero($numeroEmet);
        $typeId      = $this->typeOperationModel->getIdByLibelle('transfert');

        if ($operateurId === null || $typeId === null) {
            return redirect()->back()->with('erreur', 'Configuration des opérations indisponible.');
        }

        if ($numeroDest === $numeroEmet) {
            return redirect()->back()->with('erreur', 'Impossible de transférer vers soi-même.');
        }

        if ($this->operateurModel->getOperateurIdFromNumero($numeroDest) === null) {
            return redirect()->back()->with('erreur', 'Numéro destinataire invalide.');
        }

        $frais       = $this->baremeFraisModel->getFrais($operateurId, $typeId, $montant);

        if ($frais === null) {
            return redirect()->back()->with('erreur', 'Aucun barème trouvé pour ce montant.');
        }

        if ($compteEmet['solde'] < ($montant + $frais)) {
            return redirect()->back()->with('erreur', 'Solde insuffisant (montant + frais).');
        }

        $db = Database::connect();
        $db->transStart();

        $clientDest = $this->clientModel->getClientByPhoneNumber($numeroDest);
        if (! $clientDest) {
            $destId = $this->clientModel->insert([
                'nom'              => $numeroDest,
                'numero_telephone' => $numeroDest,
            ]);

            if ($destId === false) {
                $db->transRollback();
                return redirect()->back()->with('erreur', 'Impossible de créer le destinataire.');
            }

            $this->compteModel->insert(['client_id' => $destId, 'solde' => 0]);
            $clientDest = $this->clientModel->find($destId);
        }

        $compteDest = $this->getOrCreateCompteForClient((int) $clientDest['id']);

        $this->compteModel->update($compteEmet['id'], [
            'solde' => $compteEmet['solde'] - ($montant + $frais),
        ]);
        $this->compteModel->update($compteDest['id'], [
            'solde' => $compteDest['solde'] + $montant,
        ]);

        $this->operationModel->insert([
            'compte_id'              => $compteEmet['id'],
            'type_operation_id'      => $typeId,
            'operateur_id'           => $operateurId,
            'montant'                => $montant,
            'frais_applique'         => $frais,
            'compte_destinataire_id' => $compteDest['id'],
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->with('erreur', 'Le transfert a échoué. Veuillez réessayer.');
        }

        return redirect()->to('/client')->with('succes', "Transfert effectué (frais: $frais Ar).");
    }

    // ---------- Historique ----------

    public function historique()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $compte     = $this->getOrCreateCompteForClient((int) session()->get('client_id'));
        $operations = $this->operationModel->historique($compte['id']);

        return view('client/historique', ['operations' => $operations]);
    }

    public function historiqueDetail(int $id)
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $compte = $this->getOrCreateCompteForClient((int) session()->get('client_id'));
        $operation = $this->operationModel->detailHistorique($id, $compte['id']);

        if (! $operation) {
            return redirect()->to('/client/historique')->with('erreur', 'Détail introuvable.');
        }

        return view('client/historique_detail', [
            'operation' => $operation,
            'compte'    => $compte,
        ]);
    }
}
