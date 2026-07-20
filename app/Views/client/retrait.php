<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Retrait</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 400px; margin-top: 100px;">
    <h3>Retrait</h3>
    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erreur') ?></div>
    <?php endif; ?>
    <form action="/client/retrait" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Montant (Ar)</label>
            <input type="number" step="1" min="1" name="montant" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-warning w-100">Valider</button>
        <a href="/client" class="btn btn-link w-100">Retour</a>
    </form>
</div>
</body>
</html>