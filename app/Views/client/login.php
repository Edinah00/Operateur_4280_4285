<?php
$pageTitle = 'Connexion client';
$bodyClass = 'page-auth';
$layoutMode = 'auth';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="mb-4">
            <div class="hero-kicker">IT-Money</div>
            <h1 class="h3 mt-2 mb-2">Connexion client</h1>
            <p class="muted-copy mb-0">Connexion rapide.</p>
        </div>

        <form action="<?= base_url('client/login') ?>" method="post" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">Numéro de téléphone</label>
                <input type="text" name="numero_telephone" class="form-control glass-input" placeholder="0331234567" required>
            </div>
            <div>
                <label class="form-label">Nom (optionnel)</label>
                <input type="text" name="nom" class="form-control glass-input" placeholder="Rakoto">
            </div>
            <button type="submit" class="btn btn-light app-btn w-100">Se connecter</button>
        </form>

        <div class="mt-4 text-center">
            <a class="link-dark text-decoration-none" href="<?= base_url('operateur/login') ?>">Accès opérateur</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
