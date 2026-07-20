-- Vue : historique des opérations, avec le libellé du type au lieu de l'id
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