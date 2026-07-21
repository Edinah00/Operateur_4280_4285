<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ClientModel;
use App\Models\AutreOperateurModel;
use App\Models\CompteModel;
use App\Models\OperationModel;
use App\Models\OperateurModel;
use App\Models\PrefixeOperateurModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\CompteEpargneModel;
use Config\Database;

class ClientController extends BaseController
{
    protected $clientModel;
    protected $compteModel;
    protected $operationModel;
    protected $operateurModel;
    protected $typeOperationModel;
    protected $baremeFraisModel;
    protected $compteEpargneModel;

    public function __construct()
    {
        $this->clientModel        = new ClientModel();
        $this->compteModel        = new CompteModel();
        $this->operationModel     = new OperationModel();
        $this->operateurModel     = new OperateurModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->baremeFraisModel   = new BaremeFraisModel();
        $this->compteEpargneModel   = new CompteEpargneModel();
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
    // private function getOrCreateCompteEpargneForClient(int $clientId): array
    // {
    //     $compte = $this->compteEpargneModel->getByClientId($clientId);

    //     if ($compte) {
    //         return $compte;
    //     }

    //     $this->compteEpargneModel->insert([
    //                 'client_id'              => session()->get('client_id'),
    //                 'solde' => 0,
    //                 'pourcentage' => 1,
    //             ],true);

    //     return $this->compteModel->find($compteId);
    // }

    private function getOperateurDepuisNumero(string $numero): ?array
    {
        $prefixe = substr($numero, 0, 3);
        $prefixeRow = (new PrefixeOperateurModel())->getByPrefixe($prefixe);

        if (! $prefixeRow) {
            return null;
        }

        $operateurInterne = $this->operateurModel->getByPrefixeId((int) $prefixeRow['id']);
        if ($operateurInterne) {
            return [
                'type' => 'operateur',
                'id'   => (int) $operateurInterne['id'],
                'nom'  => $operateurInterne['nom'],
            ];
        }

        $autreOperateur = (new AutreOperateurModel())->getByPrefixeId((int) $prefixeRow['id']);
        if ($autreOperateur) {
            return [
                'type'                   => 'autre_operateur',
                'id'                     => (int) $autreOperateur['id'],
                'nom'                    => $autreOperateur['nom'],
                'commission_pourcentage' => (float) $autreOperateur['commission_pourcentage'],
            ];
        }

        return null;
    }

    private function preparerTransfert(string $numeroDest, float $montant, bool $fraisInclusCoche): array|ResponseInterface
    {
        $numeroEmet      = (string) session()->get('client_numero');
        $compteEmet      = $this->getOrCreateCompteForClient((int) session()->get('client_id'));
        $operateurEmetId = $this->operateurModel->getOperateurIdFromNumero($numeroEmet);
        $operateurDest   = $this->getOperateurDepuisNumero($numeroDest);
        $typeTransfert   = $this->typeOperationModel->getIdByLibelle('transfert');
        $typeRetrait     = $this->typeOperationModel->getIdByLibelle('retrait');

        if ($numeroDest === $numeroEmet) {
            return redirect()->back()->withInput()->with('erreur', 'Impossible de transférer vers soi-même.');
        }

        if ($operateurEmetId === null || $typeTransfert === null || $typeRetrait === null) {
            return redirect()->back()->withInput()->with('erreur', 'Configuration des opérations indisponible.');
        }

        if ($operateurDest === null) {
            return redirect()->back()->withInput()->with('erreur', 'Numéro destinataire invalide.');
        }

        $fraisTransfert = $this->baremeFraisModel->getFrais($operateurEmetId, $typeTransfert, $montant);
        if ($fraisTransfert === null) {
            return redirect()->back()->withInput()->with('erreur', 'Aucun barème de transfert trouvé pour ce montant.');
        }

        $estMemeOperateur = $operateurDest['type'] === 'operateur' && $operateurDest['id'] === $operateurEmetId;
        $fraisRetraitEstime = 0;

        if ($fraisInclusCoche && $estMemeOperateur) {
            $fraisRetraitEstime = $this->baremeFraisModel->getFrais($operateurEmetId, $typeRetrait, $montant) ?? 0;
        }

        $montantRecu = $montant + $fraisRetraitEstime;
        $commissionAppliquee = 0.0;
        $autreOperateurId = null;
        $numeroDestinataireExterne = null;

        if ($operateurDest['type'] === 'autre_operateur') {
            $autreOperateurId = (int) $operateurDest['id'];
            $commissionAppliquee = round($montant * ((float) $operateurDest['commission_pourcentage'] / 100), 2);
            $montantRecu = $montant;
            $numeroDestinataireExterne = $numeroDest;
        }

        $totalDebitEmetteur = $montantRecu + $fraisTransfert + $commissionAppliquee;

        if ($compteEmet['solde'] < $totalDebitEmetteur) {
            return redirect()->back()->withInput()->with('erreur', 'Solde insuffisant (montant + frais).');
        }

        return [
            'numero_emet'                => $numeroEmet,
            'numero_dest'                => $numeroDest,
            'montant'                    => $montant,
            'frais_inclus'               => $fraisInclusCoche,
            'compte_emet'                => $compteEmet,
            'operateur_emet_id'          => $operateurEmetId,
            'operateur_dest'             => $operateurDest,
            'type_transfert_id'          => $typeTransfert,
            'type_retrait_id'            => $typeRetrait,
            'frais_transfert'            => $fraisTransfert,
            'frais_retrait_estime'       => $fraisRetraitEstime,
            'montant_recu'               => $montantRecu,
            'commission_appliquee'       => $commissionAppliquee,
            'autre_operateur_id'         => $autreOperateurId,
            'numero_destinataire_externe'=> $numeroDestinataireExterne,
            'total_debit_emetteur'       => $totalDebitEmetteur,
            'est_meme_operateur'         => $estMemeOperateur,
        ];
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

    public function insertionCompteEpargne()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        $compteEp        = $this->compteModel->getByClientId(session()->get('client_id'));

        return view('client/insertionCompteEpargne', $compteEp);
    }

    

    public function apercuTransfert()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $montant    = (float) $this->request->getPost('montant');
        $numeroDest = trim((string) $this->request->getPost('numero_destinataire'));
        $fraisInclusCoche = (bool) $this->request->getPost('frais_retrait_inclus');

        $preview = $this->preparerTransfert($numeroDest, $montant, $fraisInclusCoche);
        if ($preview instanceof ResponseInterface) {
            return $preview;
        }

        return view('client/transfert_preview', $preview);
    }
    public function doInsertionCompteEpargne()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        $pourcentage    = (float) $this->request->getPost('pourcentage');
        // Detecter compte existant
        $compte        = $this->compteModel->getByClientId(session()->get('client_id'));
        // Creaation du compte si non 
        if (!$compte) {
            $this->compteEpargneModel->insert([
                    'client_id'              => session()->get('client_id'),
                    'solde' => 0,
                    'pourcentage' => $pourcentage,
                ]);
        }
        else {
            $this->compteEpargneModel->update($compte['id'], [
            'pourcentage' => $pourcentage,
            ]);
        }
        // Enregistrer dans base

        return view('client/insertionCompteEpargne');
    }

    public function doTransfert()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $montant    = (float) $this->request->getPost('montant');
        $numeroDest = trim((string) $this->request->getPost('numero_destinataire'));
        $fraisInclusCoche = (bool) $this->request->getPost('frais_retrait_inclus');

        $transfert = $this->preparerTransfert($numeroDest, $montant, $fraisInclusCoche);
        if ($transfert instanceof ResponseInterface) {
            return $transfert;
        }

        $db = Database::connect();
        $db->transStart();

        $this->compteModel->update($transfert['compte_emet']['id'], [
            'solde' => $transfert['compte_emet']['solde'] - $transfert['total_debit_emetteur'],
        ]);

        $compteDest = null;
        if ($transfert['operateur_dest']['type'] === 'operateur') {
            $clientDest = $this->clientModel->getClientByPhoneNumber($numeroDest);
            if (! $clientDest) {
                $destId = $this->clientModel->insert([
                    'nom'              => $numeroDest,
                    'numero_telephone' => $numeroDest,
                ]);
                if ($destId === false) {
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('erreur', 'Impossible de créer le destinataire.');
                }
                $this->compteModel->insert(['client_id' => $destId, 'solde' => 0]);
                $clientDest = $this->clientModel->find($destId);
                if (! $clientDest) {
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('erreur', 'Impossible de récupérer le destinataire.');
                }
            }
            $compteDest = $this->compteModel->getByClientId($clientDest['id']);
            if (! $compteDest) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('erreur', 'Compte destinataire introuvable.');
            }
            // Trouver ou creer le compte Epargne 
            $compteEp = $this->compteEpargneModel->getByClientId($clientDest['id']);

            if (!$compteEp) {
                return redirect()->back()->withInput()->with('erreur', 'Compte Epargne destinataire introuvable.');
            }
            $soldeEpargne = round($transfert['montant_recu'] * ((float) $compteEp['pourcentage'] / 100), 2);
            $montant_recu = $transfert['montant_recu'] - $soldeEpargne;
            $this->compteModel->update($compteDest['id'], [
                'solde' => $compteDest['solde'] + $montant_recu,
            ]);
            $this->compteEpargneModel->update($compteEp['id'], [
                'solde' => $compteEp['solde'] + $soldeEpargne,
            ]);
        }
        
        $this->operationModel->insert([
            'compte_id'              => $transfert['compte_emet']['id'],
            'type_operation_id'      => $transfert['type_transfert_id'],
            'montant'                => $transfert['montant_recu'],
            'frais_applique'         => $transfert['frais_transfert'],
            'operateur_id'           => $transfert['operateur_emet_id'],
            'compte_destinataire_id' => $compteDest['id'] ?? null,
            'autre_operateur_id'     => $transfert['autre_operateur_id'],
            'commission_appliquee'   => $transfert['commission_appliquee'],
            'numero_destinataire_externe' => $transfert['numero_destinataire_externe'],
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()->with('erreur', 'Le transfert a échoué. Veuillez réessayer.');
        }

        $message = $transfert['frais_inclus']
            ? "Transfert effectué. Montant reçu: {$transfert['montant_recu']} Ar."
            : "Transfert effectué. Montant reçu: {$transfert['montant']} Ar.";

        return redirect()->to('/client')->with('succes', $message);
    }
    public function transfertMultiple()
    {
        if ($redirect = $this->requireLogin()) return $redirect;
        return view('client/transfert_multiple');
    }

    public function doTransfertMultiple()
    {
        if ($redirect = $this->requireLogin()) return $redirect;

        $numeros            = $this->request->getPost('numeros') ?? [];
        $montantTotal       = (float) $this->request->getPost('montant_total');
        $fraisInclusCoche   = (bool) $this->request->getPost('frais_retrait_inclus');
        $numeroEmet         = session()->get('client_numero');
        $compteEmet         = $this->compteModel->getByClientId(session()->get('client_id'));

        $numeros = array_values(array_filter(array_map('trim', $numeros)));

        if (count($numeros) < 2) {
            return redirect()->back()->withInput()->with('erreur', 'Indique au moins 2 numéros destinataires.');
        }

        if (in_array($numeroEmet, $numeros, true)) {
            return redirect()->back()->withInput()->with('erreur', 'Tu ne peux pas t\'envoyer à toi-même.');
        }

        $operateurEmetId = $this->operateurModel->getOperateurIdFromNumero($numeroEmet);

        foreach ($numeros as $numero) {
            $opId = $this->operateurModel->getOperateurIdFromNumero($numero);
            if ($opId === null || $opId !== $operateurEmetId) {
                return redirect()->back()->withInput()->with('erreur', "Le numéro $numero n'appartient pas au même opérateur.");
            }
        }

        $nombreDest          = count($numeros);
        $montantParPersonne  = round($montantTotal / $nombreDest, 2);
        $typeTransfert       = $this->typeOperationModel->getIdByLibelle('transfert');
        $typeRetrait         = $this->typeOperationModel->getIdByLibelle('retrait');

        // Frais de transfert par personne (obligatoire)
        $fraisTransfertUnitaire = $this->baremeFraisModel->getFrais($operateurEmetId, $typeTransfert, $montantParPersonne);
        if ($fraisTransfertUnitaire === null) {
            return redirect()->back()->withInput()->with('erreur', 'Aucun barème trouvé pour ce montant par personne.');
        }

        // Frais de retrait estimé par personne, seulement si la case est cochée (même logique que doTransfert())
        $fraisRetraitUnitaire = 0;
        if ($fraisInclusCoche) {
            $fraisRetraitUnitaire = $this->baremeFraisModel->getFrais($operateurEmetId, $typeRetrait, $montantParPersonne) ?? 0;
        }

        $creditParPersonne    = $montantParPersonne + $fraisRetraitUnitaire;
        $coutTotalParPersonne = $creditParPersonne + $fraisTransfertUnitaire;
        $totalDebitEmetteur   = $coutTotalParPersonne * $nombreDest;

        if ($compteEmet['solde'] < $totalDebitEmetteur) {
            return redirect()->back()->withInput()->with('erreur', 'Solde insuffisant pour couvrir montant + frais total.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $soldeEmet = $compteEmet['solde'];

            foreach ($numeros as $numero) {
                $clientDest = $this->clientModel->getClientByPhoneNumber($numero);
                if (!$clientDest) {
                    $destId = $this->clientModel->insert([
                        'nom'              => $numero,
                        'numero_telephone' => $numero,
                    ]);
                    $this->compteModel->insert(['client_id' => $destId, 'solde' => 0]);
                    $clientDest = $this->clientModel->find($destId);
                }
                $compteDest = $this->compteModel->getByClientId($clientDest['id']);

                $soldeEmet -= $coutTotalParPersonne;

                $this->compteModel->update($compteDest['id'], [
                    'solde' => $compteDest['solde'] + $creditParPersonne,
                ]);

                $this->operationModel->insert([
                    'compte_id'              => $compteEmet['id'],
                    'type_operation_id'      => $typeTransfert,
                    'montant'                => $creditParPersonne,
                    'frais_applique'         => $fraisTransfertUnitaire,
                    'compte_destinataire_id' => $compteDest['id'],
                    'operateur_id'           => $operateurEmetId,
                ]);
            }

            $this->compteModel->update($compteEmet['id'], ['solde' => $soldeEmet]);

            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('erreur', 'Erreur technique, transaction annulée : ' . $e->getMessage());
        }

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('erreur', 'Erreur lors de l\'envoi multiple, rien n\'a été appliqué.');
        }

        $message = $fraisInclusCoche
            ? "Envoyé $creditParPersonne Ar à $nombreDest destinataires."
            : "Envoyé $montantParPersonne Ar à $nombreDest destinataires.";

        return redirect()->to('/client')->with('succes', $message);
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
