-- prefixes_operateur
CREATE TABLE prefixes_operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    prefixe TEXT NOT NULL UNIQUE
);

-- operateur
CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- autre operateur
CREATE TABLE autre_operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    commission_pourcentage REAL NOT NULL DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- table associative operateur <-> prefixe
CREATE TABLE operateur_prefixes (
    operateur_id INTEGER NOT NULL,
    prefixe_id INTEGER NOT NULL,
    PRIMARY KEY (operateur_id, prefixe_id),
    UNIQUE(prefixe_id),
    FOREIGN KEY (operateur_id) REFERENCES operateur(id) ON DELETE CASCADE,
    FOREIGN KEY (prefixe_id) REFERENCES prefixes_operateur(id) ON DELETE CASCADE
);
-- table associative autre_operateur <-> prefixe
CREATE TABLE autre_operateur_prefixes (
    operateur_id INTEGER NOT NULL,
    prefixe_id INTEGER NOT NULL,
    PRIMARY KEY (operateur_id, prefixe_id),
    UNIQUE(prefixe_id),
    FOREIGN KEY (operateur_id) REFERENCES autre_operateur(id) ON DELETE CASCADE,
    FOREIGN KEY (prefixe_id) REFERENCES prefixes_operateur(id) ON DELETE CASCADE
);
-- clients
CREATE TABLE clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    numero_telephone TEXT NOT NULL UNIQUE,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- comptes
CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    solde REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- comptes_epargne
CREATE TABLE comptes_epargne (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id INTEGER NOT NULL,
    solde REAL NOT NULL DEFAULT 0,
    pourcentage REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- types_operation
CREATE TABLE types_operation (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL -- depot / retrait / transfert
);

-- baremes_frais
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    operateur_id INTEGER NOT NULL,
    type_operation_id INTEGER NOT NULL,
    montant_min REAL NOT NULL,
    montant_max REAL NOT NULL,
    frais REAL NOT NULL,
    FOREIGN KEY (operateur_id) REFERENCES operateur(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id)
);
-- Promotion_transfert 
CREATE TABLE promotion(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    promotion_pourcentage REAL NOT NULL DEFAULT 0,
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id)

);

INSERT INTO promotion(type_operation_id ,promotion_pourcentage) VALUES (3,0.1);
-- operations (historique)
CREATE TABLE operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    compte_id INTEGER NOT NULL,
    type_operation_id INTEGER NOT NULL,
    operateur_id INTEGER NOT NULL,
    montant REAL NOT NULL,
    frais_applique REAL NOT NULL DEFAULT 0,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    compte_destinataire_id INTEGER,
    FOREIGN KEY (compte_id) REFERENCES comptes(id),
    FOREIGN KEY (type_operation_id) REFERENCES types_operation(id),
    FOREIGN KEY (operateur_id) REFERENCES operateur(id),
    FOREIGN KEY (compte_destinataire_id) REFERENCES comptes(id)
);

-- prefixes valides
INSERT INTO prefixes_operateur (prefixe) VALUES ('038');
INSERT INTO prefixes_operateur (prefixe) VALUES ('034');
INSERT INTO prefixes_operateur (prefixe) VALUES ('037');
INSERT INTO prefixes_operateur (prefixe) VALUES ('032');

-- operateurs
INSERT INTO operateur (nom) VALUES ('Mvola-Yas');

-- associations operateur <-> prefixe
INSERT INTO operateur_prefixes (operateur_id, prefixe_id) VALUES (1, 1);
INSERT INTO operateur_prefixes (operateur_id, prefixe_id) VALUES (1, 2);

-- autres operateurs
INSERT INTO autre_operateur (nom, commission_pourcentage) VALUES ('Orange Money', 2.5);

INSERT INTO autre_operateur_prefixes (operateur_id, prefixe_id) VALUES (1, 3);
INSERT INTO autre_operateur_prefixes (operateur_id, prefixe_id) VALUES (1, 4);

-- types d'operation
INSERT INTO types_operation (libelle) VALUES ('depot');
INSERT INTO types_operation (libelle) VALUES ('retrait');
INSERT INTO types_operation (libelle) VALUES ('transfert');

-- baremes de frais, operateur 1 (033), retrait (type_operation_id = 2)
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 100, 1000, 50);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 1001, 5000, 50);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 5001, 10000, 100);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 10001, 25000, 200);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 25001, 50000, 400);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 50001, 100000, 800);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 100001, 250000, 1500);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 250001, 500000, 1500);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 500001, 1000000, 2500);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 2, 1000001, 2000000, 3000);

-- baremes de frais, operateur 1 (033), transfert (type_operation_id = 3) - meme grille
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 100, 1000, 50);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 1001, 5000, 50);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 5001, 10000, 100);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 10001, 25000, 200);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 25001, 50000, 400);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 50001, 100000, 800);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 100001, 250000, 1500);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 250001, 500000, 1500);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 500001, 1000000, 2500);
INSERT INTO baremes_frais (operateur_id, type_operation_id, montant_min, montant_max, frais) VALUES (1, 3, 1000001, 2000000, 3000);

-- clients de test
INSERT INTO clients (nom, numero_telephone) VALUES ('Rakoto', '0341234567');
INSERT INTO clients (nom, numero_telephone) VALUES ('Rabe', '0387654321');

-- comptes de test
INSERT INTO comptes (client_id, solde) VALUES (1, 50000);
INSERT INTO comptes (client_id, solde) VALUES (2, 20000);

CREATE VIEW v_historique_operations AS
SELECT
    operations.id,
    operations.compte_id,
    operations.type_operation_id,
    types_operation.libelle AS type_libelle,
    operations.montant,
    operations.frais_applique,
    operations.date,
    operations.compte_destinataire_id
FROM operations
JOIN types_operation ON types_operation.id = operations.type_operation_id;

CREATE VIEW v_baremes AS
SELECT baremes_frais.id, baremes_frais.operateur_id, baremes_frais.type_operation_id,
       baremes_frais.montant_min, baremes_frais.montant_max, baremes_frais.frais,
       types_operation.libelle
FROM baremes_frais
JOIN types_operation ON types_operation.id = baremes_frais.type_operation_id;

CREATE VIEW v_comptes AS
SELECT comptes.id, comptes.client_id, comptes.solde,
       clients.nom, clients.numero_telephone
FROM comptes
JOIN clients ON clients.id = comptes.client_id;

CREATE VIEW v_gains_par_type AS
SELECT types_operation.libelle,
       SUM(operations.frais_applique) AS total_frais,
       COUNT(*) AS nombre
FROM operations
JOIN types_operation ON types_operation.id = operations.type_operation_id
GROUP BY types_operation.libelle;




-- ajout sur operations pour tracer les transferts externes
ALTER TABLE operations ADD COLUMN autre_operateur_id INTEGER REFERENCES autre_operateur(id);
ALTER TABLE operations ADD COLUMN commission_appliquee REAL DEFAULT 0;
-- un transfert externe n'a pas de compte local pour le destinataire : on garde son numero
ALTER TABLE operations ADD COLUMN numero_destinataire_externe TEXT;

