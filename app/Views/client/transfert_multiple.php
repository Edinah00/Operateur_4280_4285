<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoi multiple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 500px; margin-top: 80px;">
    <h3>Envoi multiple</h3>
    <p class="text-muted">Même opérateur uniquement. Le montant total sera divisé équitablement.</p>

    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erreur') ?></div>
    <?php endif; ?>

    <form action="/client/transfert-multiple" method="post">
        <?= csrf_field() ?>

        <div id="numeros-container">
            <?php
                // old('numeros') renvoie un tableau si le formulaire a déjà été soumis, sinon null
                $anciensNumeros = old('numeros') ?? ['', '']; // 2 champs vides par défaut au premier chargement
            ?>
            <?php foreach ($anciensNumeros as $valeur): ?>
                <div class="mb-2">
                    <input type="text" name="numeros[]" class="form-control" placeholder="Numéro destinataire"
                           value="<?= esc($valeur) ?>" required>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" id="btn-ajouter">
            + Ajouter un numéro
        </button>

        <div class="mb-3">
            <label class="form-label">Montant total (Ar)</label>
            <input type="number" step="1" min="1" name="montant_total" class="form-control"
                   value="<?= old('montant_total') ?>" required>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="frais_retrait_inclus" value="1" class="form-check-input" id="fraisInclusMulti"
                <?= old('frais_retrait_inclus') ? 'checked' : '' ?>>
            <label class="form-check-label" for="fraisInclusMulti">
                Inclure les frais de retrait pour chaque destinataire
            </label>
        </div>

        <button type="submit" class="btn btn-info w-100">Envoyer</button>
        <a href="/client" class="btn btn-link w-100">Retour</a>
    </form>
</div>

<script>
document.getElementById('btn-ajouter').addEventListener('click', function () {
    const container = document.getElementById('numeros-container');
    const div = document.createElement('div');
    div.className = 'mb-2';
    div.innerHTML = '<input type="text" name="numeros[]" class="form-control" placeholder="Numéro destinataire" required>';
    container.appendChild(div);
});
</script>
</body>
</html>