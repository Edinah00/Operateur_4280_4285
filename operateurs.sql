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