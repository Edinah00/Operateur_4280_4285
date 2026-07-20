<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 500px; margin-top: 60px;">
    <h3>Bonjour, <?= esc(session()->get('client_nom')) ?></h3>

    <?php if (session()->getFlashdata('succes')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('succes') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erreur') ?></div>
    <?php endif; ?>

    <div class="card p-4 text-center mb-4">
        <p class="text-muted mb-1">Solde actuel</p>
        <h1><?= number_format($compte['solde'], 0, ',', ' ') ?> Ar</h1>
    </div>

    <div class="d-grid gap-2">
        <a href="/client/depot" class="btn btn-success">Dépôt</a>
        <a href="/client/retrait" class="btn btn-warning">Retrait</a>
        <a href="/client/transfert" class="btn btn-info">Transfert</a>
        <a href="/client/historique" class="btn btn-outline-secondary">Historique</a>
        <a href="/client/logout" class="btn btn-outline-danger">Déconnexion</a>
    </div>
</div>
</body>
</html>