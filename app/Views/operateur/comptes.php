<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Comptes clients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1>Comptes clients</h1>

    <table class="table table-bordered">
        <thead>
            <tr><th>Nom</th><th>Telephone</th><th>Solde</th></tr>
        </thead>
        <tbody>
            <?php foreach ($comptes as $c): ?>
            <tr>
                <td><?= esc($c['nom']) ?></td>
                <td><?= esc($c['numero_telephone']) ?></td>
                <td><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</body>
</html>