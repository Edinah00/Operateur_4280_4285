<?php
$pageTitle = 'Envoi multiple';
$bodyClass = 'page-auth';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="auth-wrap">
    <div class="auth-card multiple-transfer-card">
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mt-2 mb-2">Envoi multiple</h1>
        <p class="muted-copy mb-4">Même opérateur. Répartition égale.</p>

        <?php if ($error = session()->getFlashdata('erreur')): ?>
            <div class="alert alert-danger"><?= esc($error) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('client/transfert-multiple') ?>" method="post" class="vstack gap-3">
            <?= csrf_field() ?>

            <div id="numeros-container" class="vstack gap-2">
                <?php $anciensNumeros = old('numeros') ?? ['', '']; ?>
                <?php foreach ($anciensNumeros as $valeur): ?>
                    <input type="text" name="numeros[]" class="form-control glass-input" placeholder="Numéro destinataire" value="<?= esc($valeur) ?>" required>
                <?php endforeach; ?>
            </div>

            <button type="button" class="btn btn-outline-light app-btn" id="btn-ajouter">Ajouter un numéro</button>

            <div>
                <label class="form-label">Montant total (Ar)</label>
                <input type="number" step="1" min="1" name="montant_total" class="form-control glass-input" value="<?= esc(old('montant_total')) ?>" required>
            </div>

            <div class="form-check">
                <input type="checkbox" name="frais_retrait_inclus" value="1" class="form-check-input" id="fraisInclusMulti" <?= old('frais_retrait_inclus') ? 'checked' : '' ?>>
                <label class="form-check-label" for="fraisInclusMulti">
                    Inclure les frais destinataire
                </label>
            </div>

            <button type="submit" class="btn btn-light app-btn w-100">Envoyer</button>
            <a href="<?= base_url('client') ?>" class="btn btn-outline-light app-btn w-100">Retour</a>
        </form>
    </div>
</div>

<script>
document.getElementById('btn-ajouter').addEventListener('click', function () {
    const container = document.getElementById('numeros-container');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'numeros[]';
    input.className = 'form-control glass-input';
    input.placeholder = 'Numéro destinataire';
    input.required = true;
    container.appendChild(input);
});
</script>
<?= $this->endSection() ?>
