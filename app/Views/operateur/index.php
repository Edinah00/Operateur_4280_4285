<?php
$pageTitle = 'Accueil opérateur';
$bodyClass = 'page-operateur';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="hero-card mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <div class="hero-kicker">IT-Money</div>
            <h1 class="hero-title mb-3">Accueil opérateur</h1>
            <p class="hero-subtitle mb-4">Barèmes, préfixes, comptes, gains.</p>
            <div class="d-flex flex-wrap gap-2">
                <a class="btn btn-light app-btn" href="<?= base_url('operateur/baremes') ?>">Barèmes</a>
                <a class="btn btn-outline-light app-btn" href="<?= base_url('operateur/prefixes') ?>">Préfixes</a>
                <a class="btn btn-outline-light app-btn" href="<?= base_url('operateur/comptes') ?>">Comptes</a>
                <a class="btn btn-outline-light app-btn" href="<?= base_url('operateur/gains') ?>">Gains</a>
            </div>
        </div>
    </div>
</section>

<section class="soft-grid cols-3">
    <div class="metric-card panel-card">
        <div class="detail-key">Barèmes</div>
        <div class="detail-value mt-2">Tarification</div>
    </div>
    <div class="metric-card panel-card">
        <div class="detail-key">Préfixes</div>
        <div class="detail-value mt-2">Numéros</div>
    </div>
    <div class="metric-card panel-card">
        <div class="detail-key">Gains</div>
        <div class="detail-value mt-2">Frais</div>
    </div>
</section>
<?= $this->endSection() ?>
