<?php
$pageTitle = 'Transfert';
$bodyClass = 'page-auth';
$layoutMode = 'auth';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card">
        <div class="hero-kicker">Opération</div>
        <h1 class="h3 mt-2 mb-3">Transfert</h1>
        <p class="muted-copy">Envoyez un montant vers un autre numéro enregistré dans le système.</p>

        <form action="<?= base_url('client/transfert') ?>" method="post" class="vstack gap-3 mt-4">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">Numéro destinataire</label>
                <input type="text" name="numero_destinataire" class="form-control glass-input" placeholder="0337654321" required>
            </div>
            <div>
                <label class="form-label">Montant (Ar)</label>
                <input type="number" step="1" min="1" name="montant" class="form-control glass-input" required>
            </div>
            <button type="submit" class="btn btn-light app-btn w-100">Valider le transfert</button>
            <a href="<?= base_url('client') ?>" class="btn btn-outline-light app-btn w-100">Retour au tableau de bord</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
