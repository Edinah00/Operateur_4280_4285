<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Prefixes operateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Prefixes valables</h1>

    <form action="<?= base_url('operateur/prefixes/ajouter') ?>" method="post" class="row g-2 mb-4">
        <div class="col-auto">
            <input type="text" name="prefixe" class="form-control" placeholder="ex: 033" required maxlength="3">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr><th>Prefixe</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($prefixes as $p): ?>
            <tr>
                <td><?= esc($p['prefixe']) ?></td>
                <td>
                    <a href="<?= base_url('operateur/prefixes/supprimer/' . $p['id']) ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Supprimer ce prefixe ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>