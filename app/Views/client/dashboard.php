<?php
$pageTitle = 'Accueil client';
$bodyClass = 'page-client';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="hero-card mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-7">
            <div class="hero-kicker">IT-Money</div>
            <h1 class="hero-title mb-3">Bonjour, <?= esc(session()->get('client_nom')) ?></h1>
            <p class="hero-subtitle mb-4">Solde, dépôt, retrait, transfert.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= base_url('client/depot') ?>" class="btn btn-light app-btn">Dépôt</a>
                <a href="<?= base_url('client/retrait') ?>" class="btn btn-outline-light app-btn">Retrait</a>
                <a href="<?= base_url('client/transfert') ?>" class="btn btn-outline-light app-btn">Transfert</a>
                <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-light app-btn">Historique</a>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="mini-card h-100">
                <div class="detail-key mb-2">Solde actuel</div>
                <div class="metric-value"><?= number_format($compte['solde'] ?? 0, 0, ',', ' ') ?> Ar</div>
                <p class="muted-copy mt-3 mb-0">Votre compte est prêt pour les dépôts, retraits et transferts.</p>
            </div>
        </div>
    </div>
</section>

<section class="soft-grid cols-3">
    <div class="metric-card panel-card">
        <div class="detail-key">Profil</div>
        <div class="detail-value mt-2"><?= esc(session()->get('client_nom')) ?></div>
        <div class="muted-copy small mt-1"><?= esc(session()->get('client_numero')) ?></div>
    </div>
    <div class="metric-card panel-card">
        <div class="detail-key">Compte</div>
        <div class="detail-value mt-2">Actif</div>
        <div class="muted-copy small mt-1">Historique disponible</div>
    </div>
    <div class="metric-card panel-card">
        <div class="detail-key">Accès rapide</div>
        <div class="detail-value mt-2">4 actions</div>
        <div class="muted-copy small mt-1">Opérations principales</div>
    </div>
</section>
<?= $this->endSection() ?>
