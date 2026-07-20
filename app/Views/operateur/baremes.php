<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Baremes de frais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Baremes de frais</h1>
<!-- filtre en haut de page -->
<form method="get">
    <select name="operateur_id" onchange="this.form.submit()">
        <?php foreach ($operateurs as $op): ?>
            <option value="<?= $op['id'] ?>" <?= $op['id'] == $operateur_id ? 'selected' : '' ?>>
                <?= esc($op['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>
    <form action="<?= base_url('operateur/baremes/ajouter') ?>" method="post" class="row g-2 mb-4">
          <select name="operateur_id">
        <?php foreach ($operateurs as $op): ?>
            <option value="<?= $op['id'] ?>"><?= esc($op['nom']) ?></option>
        <?php endforeach; ?>
    </select>
   
        <div class="col-auto">
            <select name="type_operation_id" class="form-select" required>
                <?php foreach ($types as $t): ?>
                <option value="<?= $t['id'] ?>"><?= esc($t['libelle']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-auto">
            <input type="number" name="montant_min" class="form-control" placeholder="Montant min" required>
        </div>
        <div class="col-auto">
            <input type="number" name="montant_max" class="form-control" placeholder="Montant max" required>
        </div>
        <div class="col-auto">
            <input type="number" name="frais" class="form-control" placeholder="Frais" required>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
        
    </form>

    <table class="table table-bordered">
        <thead>
            <tr><th>Type</th><th>Min</th><th>Max</th><th>Frais</th><th></th></tr>
        </thead>
        <tbody>
            <?php foreach ($baremes as $b): ?>
            <tr>
                <form action="<?= base_url('operateur/baremes/modifier/' . $b['id']) ?>" method="post">
                <td><?= esc($b['libelle']) ?></td>
                <td><input type="number" name="montant_min" value="<?= $b['montant_min'] ?>" class="form-control form-control-sm"></td>
                <td><input type="number" name="montant_max" value="<?= $b['montant_max'] ?>" class="form-control form-control-sm"></td>
                <td><input type="number" name="frais" value="<?= $b['frais'] ?>" class="form-control form-control-sm"></td>
                <td>
                    <button type="submit" class="btn btn-sm btn-secondary">Modifier</button>
                </form>
                    <a href="<?= base_url('operateur/baremes/supprimer/' . $b['id']) ?>"
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Supprimer ce bareme ?')">Supprimer</a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>