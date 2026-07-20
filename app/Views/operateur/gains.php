<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Situation des gains</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Situation des gains</h1>

<!-- selecteur d'operateur -->
<form method="get">
    <select name="operateur_id" onchange="this.form.submit()">
        <?php foreach ($operateurs as $op): ?>
            <option value="<?= $op['id'] ?>" <?= $op['id'] == $operateur_id ? 'selected' : '' ?>>
                <?= esc($op['nom']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<h3>Total : <?= number_format($total_gains['total'] ?? 0, 0, ',', ' ') ?> Ar</h3>

<table>
    <tr><th>Type</th><th>Gains</th></tr>
    <?php foreach ($gains_par_type as $g): ?>
        <tr>
            <td><?= esc($g['libelle']) ?></td>
            <td><?= number_format($g['total'], 0, ',', ' ') ?> Ar</td>
        </tr>
    <?php endforeach; ?>
</table>
</body>
</html>