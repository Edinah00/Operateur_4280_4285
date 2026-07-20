<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion Client</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width: 400px; margin-top: 100px;">
    <h3 class="mb-3">Connexion Mobile Money</h3>

    <?php if (session()->getFlashdata('erreur')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('erreur') ?></div>
    <?php endif; ?>

    <form action="/client/login" method="post">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Numéro de téléphone</label>
            <input type="text" name="numero_telephone" class="form-control" placeholder="0331234567" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Nom (optionnel)</label>
            <input type="text" name="nom" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100">Se connecter</button>
    </form>
    <a href="/operateur">Se connecter en tant qu'opérateur</a>
</div>
</body>
</html>