<?php
$pageTitle = 'Insertion Compte Epargne';
$bodyClass = 'page-auth';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card transfer-card">
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mt-2 mb-2">Compte Epargne</h1>
        <p class="muted-copy mb-4">Pourcentage.</p>
        <p class="muted-copy mb-4">Solde Epargne Actuel : </p> 
        <!-- <div class="detail-value mt-1"><?= esc($solde) ?></div> -->
        <form action="<?= base_url('client/insertionCompteEpargne') ?>" method="post" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">Pourcentage à epargner (%)</label>
                <input type="number" step="1" min="1" name="pourcentage" class="form-control glass-input" required>
            </div>
            <div class="d-grid gap-2 mt-2">
                <button type="submit" class="btn btn-light app-btn">Enregistrer</button>
                <a href="<?= base_url('client') ?>" class="btn btn-outline-light app-btn">Retour</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
