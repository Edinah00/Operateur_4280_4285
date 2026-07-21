<?php
$pageTitle  = $pageTitle ?? 'IT-Money';
$bodyClass  = trim(($bodyClass ?? '') . ' app-body');
$layoutMode = $layoutMode ?? 'app';
$role       = session()->get('role');

$navItems = [];
if ($role === 'client') {
    $navItems = [
        ['label' => 'Tableau de bord', 'href' => base_url('client')],
        ['label' => 'Dépôt', 'href' => base_url('client/depot')],
        ['label' => 'Retrait', 'href' => base_url('client/retrait')],
        ['label' => 'Transfert', 'href' => base_url('client/transfert')],
        ['label' => 'Transfert multiple', 'href' => base_url('client/transfert-multiple')],
        ['label' => 'Historique', 'href' => base_url('client/historique')],
        ['label' => 'Compte Epargne', 'href' => base_url('client/insertionCompteEpargne')],
        ['label' => 'Déconnexion', 'href' => base_url('client/logout'), 'class' => 'nav-logout'],
    ];
} elseif ($role === 'operateur') {
    $navItems = [
        ['label' => 'Accueil', 'href' => base_url('operateur')],
        ['label' => 'Barèmes', 'href' => base_url('operateur/baremes')],
        ['label' => 'Préfixes', 'href' => base_url('operateur/prefixes')],
        ['label' => 'Autres opérateurs', 'href' => base_url('operateur/autres-operateurs')],
        ['label' => 'Montants à envoyer', 'href' => base_url('operateur/montants-a-envoyer')],
        ['label' => 'Comptes', 'href' => base_url('operateur/comptes')],
        ['label' => 'Gains', 'href' => base_url('operateur/gains')],
        ['label' => 'Déconnexion', 'href' => base_url('operateur/logout'), 'class' => 'nav-logout'],
    ];
} else {
    $navItems = [
        ['label' => 'Connexion client', 'href' => base_url('client/login')],
        ['label' => 'Connexion opérateur', 'href' => base_url('operateur/login')],
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($pageTitle) ?> | IT-Money</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body class="<?= esc($bodyClass) ?>">
    <div class="app-gradient"></div>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
        <div class="container">
            <a class="navbar-brand fw-semibold" href="<?= $role === 'client' ? base_url('client') : ($role === 'operateur' ? base_url('operateur') : base_url('client/login')) ?>">IT-Money</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appNav" aria-controls="appNav" aria-expanded="false" aria-label="Basculer la navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="appNav">
                <ul class="navbar-nav ms-auto gap-lg-2">
                    <?php foreach ($navItems as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= esc($item['class'] ?? '') ?>" href="<?= esc($item['href']) ?>"><?= esc($item['label']) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container app-main py-4 py-lg-5">
        <?php if ($message = session()->getFlashdata('succes')): ?>
            <div class="alert alert-success shadow-sm border-0"><?= esc($message) ?></div>
        <?php endif; ?>
        <?php if ($message = session()->getFlashdata('erreur')): ?>
            <div class="alert alert-danger shadow-sm border-0"><?= esc($message) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
