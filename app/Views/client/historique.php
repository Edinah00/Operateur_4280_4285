<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 750px; margin-top: 60px; margin-bottom: 60px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Historique des opérations</h3>
        <a href="/client" class="btn btn-outline-secondary btn-sm">← Retour</a>
    </div>

    <?php if (empty($operations)): ?>
        <div class="alert alert-info">Aucune opération pour le moment.</div>
    <?php else: ?>
        <table class="table table-hover bg-white align-middle">
            <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th class="text-end">Montant</th>
                <th class="text-end">Frais</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($operations as $op): ?>
                <?php
                    $libelle = $op['type_libelle'];
                    $badgeClass = match ($libelle) {
                        'depot'     => 'bg-success',
                        'retrait'   => 'bg-warning text-dark',
                        'transfert' => 'bg-info text-dark',
                        default     => 'bg-secondary',
                    };
                    $labelAffiche = match ($libelle) {
                        'depot'     => 'Dépôt',
                        'retrait'   => 'Retrait',
                        'transfert' => 'Transfert',
                        default     => ucfirst($libelle),
                    };
                    $signe = $libelle === 'depot' ? '+' : '-';
                ?>
                <tr>
                    <td><?= esc(date('d/m/Y H:i', strtotime($op['date']))) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $labelAffiche ?></span></td>
                    <td class="text-end"><?= $signe ?><?= number_format($op['montant'], 0, ',', ' ') ?> Ar</td>
                    <td class="text-end"><?= number_format($op['frais_applique'], 0, ',', ' ') ?> Ar</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>