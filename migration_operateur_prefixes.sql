BEGIN TRANSACTION;
PRAGMA foreign_keys = OFF;

ALTER TABLE operateur RENAME TO operateur_old;

CREATE TABLE operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE operateur_prefixes (
    operateur_id INTEGER NOT NULL,
    prefixe_id INTEGER NOT NULL,
    PRIMARY KEY (operateur_id, prefixe_id),
    UNIQUE(prefixe_id),
    FOREIGN KEY (operateur_id) REFERENCES operateur(id) ON DELETE CASCADE,
    FOREIGN KEY (prefixe_id) REFERENCES prefixes_operateur(id) ON DELETE CASCADE
);

INSERT INTO operateur (id, nom)
SELECT id, nom
FROM operateur_old;

INSERT INTO operateur_prefixes (operateur_id, prefixe_id)
SELECT id, prefixe_id
FROM operateur_old
WHERE prefixe_id IS NOT NULL;

DROP TABLE operateur_old;

PRAGMA foreign_keys = ON;
COMMIT;
