# Répartition des tâches — Examen Final S4 (Simulateur Mobile Money)

Binôme : **Edinah** & **Mpiaro**
Stack : PHP / CodeIgniter 4, SQLite embarqué, Bootstrap
Livraison V1 : 13h — tag `v1`

---

## Base commune (à faire ensemble, en premier)

- [ok] **Init projet CodeIgniter 4**
  - Config `.env` : `database.default.DBDriver = SQLite3`, chemin du fichier `.db`
  - Vérifier que le serveur démarre (`php spark serve`)
- [ok] **Dépôt Git public**
  - Créer le repo, README avec le lien du formulaire
  - Branche `main` réservée à la version finale, travailler sur `dev`
- [ok] **Remplir le formulaire Google en tout début de projet**
- [ok] **Schéma de base de données**
  - `clients` (id, nom, numero_telephone, date_creation)
  - `comptes` (id, client_id, solde)
  - `types_operation` (id, libelle : dépôt/retrait/transfert)
   -`operateur` (id, prefixe_id ,nom)
  - `prefixes_operateur` (id, prefixe ex: 033/037)
  - `baremes_frais` (id,opérateur_id,type_operation_id, montant_min, montant_max, frais)
  - `operations` (id, compte_id, type_operation_id, montant, frais_applique, date, compte_destinataire_id nullable pour transfert)
- [ok] **Fichier `base.sql` à la racine du projet (obligatoire, 1 seul fichier)**
  - Contient TOUT : `CREATE TABLE` pour les 6 tables, `CREATE VIEW` si besoin, `INSERT` pour les données de départ (préfixes 033/037, barèmes de frais du tableau, 2-3 clients de test)
  - Placé à la racine bdu repo, à côté du dossier `app/` — pas dans `app/Database/`
  - Charger ce fichier dans la base SQLite au démarrage (script d'init ou commande manuelle `sqlite3 base.db < base.sql`)

---

## Edinah — Côté Opérateur

- [ok] **Configuration des préfixes valables**
  - Modèle `PrefixeModel` + table `prefixes_operateur`
  - Contrôleur `OperateurController::prefixes()` : formulaire CRUD (ajouter/supprimer un préfixe)
  - Vue `operateur/prefixes.php` avec table Bootstrap + form d'ajout
- [ok] **Types d'opérations + barèmes de frais**
  - Modèle `TypeOperationModel`, `BaremeModel`
  - CRUD des tranches (montant_min, montant_max, frais) par type d'opération
  - Vue avec table éditable (form inline ou modal Bootstrap) reproduisant le tableau de l'énoncé
  - Validation : pas de chevauchement de tranches
- [ok] **Vue "Situation des gains"**
  - Requête agrégée : somme des `frais_applique` sur `operations` filtrées par type = retrait/transfert
  - Affichage total + éventuellement par période (jour/tout)
- [ok] **Vue "Situation des comptes clients"**
  - Liste des clients avec leur solde courant
  - Recherche/filtre par numéro de téléphone
- [ok] **Layout/dashboard admin**
  - Template Bootstrap commun (sidebar ou navbar) pour toutes les vues opérateur
  - Menu : Préfixes / Types & Barèmes / Gains / Comptes clients

---

## Mpiaro — Côté Client

- [ok] **Login automatique par numéro de téléphone**
  - Contrôleur `ClientController::login()` : formulaire avec juste le numéro
  - Vérifier le préfixe (doit correspondre à un `prefixes_operateur` valide)
  - Si le numéro n'existe pas en base → création automatique du client + compte (solde 0), pas de formulaire d'inscription séparé
  - Stocker le client connecté en session
- [ok] **Vue solde du compte**
  - Page d'accueil client après login : affichage du solde courant
- [ok] **Dépôt automatique**
  - Formulaire montant → création directe d'une `operation` type "dépôt", solde += montant
  - Pas de frais sur le dépôt (à confirmer selon barème)
- [ok] **Retrait automatique**
  - Formulaire montant → calcul du frais selon la tranche correspondante (`baremes_frais`)
  - Vérifier solde suffisant (montant + frais)
  - solde -= (montant + frais), enregistrer l'opération avec le frais appliqué
- [ok] **Transfert entre comptes**
  - Formulaire montant + numéro destinataire
  - Vérifier que le destinataire existe (ou le créer si logique similaire au dépôt)
  - Appliquer le barème de frais transfert, débiter l'émetteur, créditer le destinataire
  - Enregistrer une opération avec `compte_destinataire_id`
- [ok] **Historique des opérations**
  - Liste chronologique des opérations du client connecté (type, montant, frais, date)
  - Table Bootstrap, tri par date décroissante

---

## Intégration finale _ v1(ensemble)

- [ok] Vérifier que les frais définis côté opérateur s'appliquent correctement côté client
- [ok] Tests bout en bout : login → dépôt → transfert → retrait → historique
- [ok] Vérifier la vue "Situation des gains" après une série d'opérations de test
- [ok] Nettoyage code + commentaires courts
- [ok] Créer et pousser le tag `v1` :
  - `git tag v1`
  - `git push origin v1`
  - (fait après avoir mergé/mis la version finale sur `main`)
  
  ---
 
## Version 2

### Edinah — Côté Opérateur
 
- [ok] **Configuration des préfixes des autres opérateurs**
  - Étendre `prefixes_operateur` ou table dédiée `autres_operateurs` (id, prefixe, nom) — ex: 032, 031
  - Réutiliser le CRUD déjà fait pour les préfixes, en distinguant "mon opérateur" vs "opérateur externe"
- [ok] **Commission additionnelle sur transferts vers un autre opérateur**
  - Ajouter un champ `commission_pourcentage` (table `baremes_frais` ou nouvelle table dédiée aux transferts inter-opérateurs)
  - Lors d'un transfert vers un numéro d'un autre préfixe : frais habituel + (montant × commission%)
- [ok] **Page "Situation des gains" : séparer opérateur / autres opérateurs**
  - Adapter la requête `gains()` : `GROUP BY` selon si `compte_destinataire` appartient à ton opérateur ou à un opérateur externe (déduit du préfixe du destinataire)
  - Deux blocs distincts dans la vue : gains internes / gains venant des transferts externes
- [ok] **Vue "Montants à envoyer à chaque opérateur"**
  - Somme des montants transférés (hors frais/commission) groupés par opérateur externe destinataire — représente ce que ton opérateur doit reverser aux autres opérateurs
### Mpiaro — Côté Client
 
- [ok] **Option "inclure les frais de retrait lors de l'envoi"**
  - Checkbox/toggle sur le formulaire de transfert
  - Si cochée : le frais est ajouté au montant envoyé au destinataire au lieu d'être déduit du solde de l'émetteur seul (à clarifier avec l'énoncé qui paie quoi exactement)
- [ok] **Envoi multiple vers plusieurs numéros**
  - Formulaire avec liste de numéros destinataires (ajout dynamique de champs, ex: bouton "+ ajouter un numéro")
  - Montant total saisi une fois, divisé équitablement entre le nombre de numéros
  - Une ligne `operations` (avec `compte_destinataire_id`) créée par destinataire, chacune avec sa part du montant + son frais
---
 
## Intégration finale V2 (ensemble)
 
- [ok] Tester un transfert vers un préfixe externe (frais + commission corrects)
- [ok] Vérifier séparation des gains dans la vue opérateur
- [ok] Tester l'envoi multiple avec répartition du montant
- [ok] Créer et pousser le tag `v2` :
  - `git tag v2`
  - `git push origin v2`
 
## Aléa 4280 - Promotion transfert
- [ok]  Creation table Promotion_transfert 
- [ok]  Ajout donnée test
- [ok]  Creation Model Promotion 
- [ok]  Application dans ClientController

## Aléa 4285 - Notion Epargne (lors d un transfert vers mon compte)
- [OK]  Creation table Compte_epargne
- [OK]  Création CompteEpargneModel
- [OK]  Création page pour insertion pourcentage Compte_epargne
- [ ]  Solde actuel encore à faire
- [OK]  Enregistrement de Compte_epargne pour session client_id
- [ ]  Modification dans ClientController conçernant doTransfert()
