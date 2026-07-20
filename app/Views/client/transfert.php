<?php
$pageTitle = 'Transfert';
$bodyClass = 'page-auth';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card transfer-card">
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mt-2 mb-2">Transfert</h1>
        <p class="muted-copy mb-4">Montant et destinataire.</p>

        <form action="<?= base_url('client/transfert') ?>" method="post" class="vstack gap-3">
            <?= csrf_field() ?>
            <div>
                <label class="form-label">Numéro destinataire</label>
                <input type="text" name="numero_destinataire" class="form-control glass-input" placeholder="0331234567" required value="<?= esc(old('numero_destinataire')) ?>">
            </div>
            <div>
                <label class="form-label">Montant (Ar)</label>
                <input type="number" step="1" min="1" name="montant" class="form-control glass-input" required value="<?= esc(old('montant')) ?>">
            </div>
            <div class="form-check">
                <input type="checkbox" name="frais_retrait_inclus" value="1" class="form-check-input" id="fraisInclus" <?= old('frais_retrait_inclus') ? 'checked' : '' ?>>
                <label class="form-check-label" for="fraisInclus">
                    Inclure les frais destinataire si possible
                </label>
            </div>
            <div class="d-grid gap-2 mt-2">
                <button type="submit" formaction="<?= base_url('client/transfert/apercu') ?>" class="btn btn-outline-light app-btn">Voir l'aperçu</button>
                <button type="submit" class="btn btn-light app-btn">Envoyer</button>
                <a href="<?= base_url('client') ?>" class="btn btn-outline-light app-btn">Retour</a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
