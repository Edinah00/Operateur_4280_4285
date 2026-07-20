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
            <div class="mb-3">
                <label class="form-label">Numéro destinataire</label>
                <input type="text" name="numero_destinataire" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Montant (Ar)</label>
                <input type="number" step="1" min="1" name="montant" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="frais_retrait_inclus" value="1" class="form-check-input" id="fraisInclus">
                <label class="form-check-label" for="fraisInclus">
                    Inclure les frais de retrait (le destinataire pourra retirer sans y penser)
                </label>
            </div>
            <button type="submit" class="btn btn-info w-100">Valider</button>
            <a href="/client" class="btn btn-link w-100">Retour</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
