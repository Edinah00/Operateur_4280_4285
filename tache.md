# Répartition des tâches — Examen Final S4 (Simulateur Mobile Money)

Binôme : **Edinah** & **Mpiaro**
Stack : PHP / CodeIgniter 4, SQLite embarqué, Bootstrap
Livraison V1 : 13h — tag `v1`

---

## Base commune (à faire ensemble, en premier)

- [ ] **Init projet CodeIgniter 4**
  - Config `.env` : `database.default.DBDriver = SQLite3`, chemin du fichier `.db`
  - Vérifier que le serveur démarre (`php spark serve`)
- [ ] **Dépôt Git public**
  - Créer le repo, README avec le lien du formulaire
  - Branche `main` réservée à la version finale, travailler sur `dev`
- [ ] **Remplir le formulaire Google en tout début de projet**
- [ ] **Schéma de base de données**
  - `clients` (id, nom, numero_telephone, date_creation)
  - `comptes` (id, client_id, solde)
  - `types_operation` (id, libelle : dépôt/retrait/transfert)
  - `baremes_frais` (id, type_operation_id, montant_min, montant_max, frais)
  - `prefixes_operateur` (id, prefixe ex: 033/037)
  - `operations` (id, compte_id, type_operation_id, montant, frais_applique, date, compte_destinataire_id nullable pour transfert)
- [ ] **Fichier `base.sql` à la racine du projet (obligatoire, 1 seul fichier)**
  - Contient TOUT : `CREATE TABLE` pour les 6 tables, `CREATE VIEW` si besoin, `INSERT` pour les données de départ (préfixes 033/037, barèmes de frais du tableau, 2-3 clients de test)
  - Placé à la racine bdu repo, à côté du dossier `app/` — pas dans `app/Database/`
  - Charger ce fichier dans la base SQLite au démarrage (script d'init ou commande manuelle `sqlite3 base.db < base.sql`)

---

## Edinah — Côté Opérateur

- [ ] **Configuration des préfixes valables**
  - Modèle `PrefixeModel` + table `prefixes_operateur`
  - Contrôleur `OperateurController::prefixes()` : formulaire CRUD (ajouter/supprimer un préfixe)
  - Vue `operateur/prefixes.php` avec table Bootstrap + form d'ajout
- [ ] **Types d'opérations + barèmes de frais**
  - Modèle `TypeOperationModel`, `BaremeModel`
  - CRUD des tranches (montant_min, montant_max, frais) par type d'opération
  - Vue avec table éditable (form inline ou modal Bootstrap) reproduisant le tableau de l'énoncé
  - Validation : pas de chevauchement de tranches
- [ ] **Vue "Situation des gains"**
  - Requête agrégée : somme des `frais_applique` sur `operations` filtrées par type = retrait/transfert
  - Affichage total + éventuellement par période (jour/tout)
- [ ] **Vue "Situation des comptes clients"**
  - Liste des clients avec leur solde courant
  - Recherche/filtre par numéro de téléphone
- [ ] **Layout/dashboard admin**
  - Template Bootstrap commun (sidebar ou navbar) pour toutes les vues opérateur
  - Menu : Préfixes / Types & Barèmes / Gains / Comptes clients

---

## Mpiaro — Côté Client

- [ ] **Login automatique par numéro de téléphone**
  - Contrôleur `ClientController::login()` : formulaire avec juste le numéro
  - Vérifier le préfixe (doit correspondre à un `prefixes_operateur` valide)
  - Si le numéro n'existe pas en base → création automatique du client + compte (solde 0), pas de formulaire d'inscription séparé
  - Stocker le client connecté en session
- [ ] **Vue solde du compte**
  - Page d'accueil client après login : affichage du solde courant
- [ ] **Dépôt automatique**
  - Formulaire montant → création directe d'une `operation` type "dépôt", solde += montant
  - Pas de frais sur le dépôt (à confirmer selon barème)
- [ ] **Retrait automatique**
  - Formulaire montant → calcul du frais selon la tranche correspondante (`baremes_frais`)
  - Vérifier solde suffisant (montant + frais)
  - solde -= (montant + frais), enregistrer l'opération avec le frais appliqué
- [ ] **Transfert entre comptes**
  - Formulaire montant + numéro destinataire
  - Vérifier que le destinataire existe (ou le créer si logique similaire au dépôt)
  - Appliquer le barème de frais transfert, débiter l'émetteur, créditer le destinataire
  - Enregistrer une opération avec `compte_destinataire_id`
- [ ] **Historique des opérations**
  - Liste chronologique des opérations du client connecté (type, montant, frais, date)
  - Table Bootstrap, tri par date décroissante

---

## Intégration finale _ v1(ensemble)

- [ ] Vérifier que les frais définis côté opérateur s'appliquent correctement côté client
- [ ] Tests bout en bout : login → dépôt → transfert → retrait → historique
- [ ] Vérifier la vue "Situation des gains" après une série d'opérations de test
- [ ] Nettoyage code + commentaires courts
- [ ] Créer et pousser le tag `v1` :
  - `git tag v1`
  - `git push origin v1`
  - (fait après avoir mergé/mis la version finale sur `main`)