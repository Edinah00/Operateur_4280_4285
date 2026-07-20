<?php
$pageTitle = 'Barèmes';
$bodyClass = 'page-operateur';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Barèmes</h1>
    </div>
    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="panel-card p-4 mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Opérateur sélectionné</label>
            <select name="operateur_id" class="form-select" onchange="this.form.submit()">
                <?php foreach ($operateurs as $op): ?>
                    <option value="<?= $op['id'] ?>" <?= $op['id'] == $operateur_id ? 'selected' : '' ?>>
                        <?= esc($op['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</section>

<section class="panel-card p-4 mb-4">
    <form action="<?= base_url('operateur/baremes/ajouter') ?>" method="post" class="row g-3 align-items-end">
        <?= csrf_field() ?>
        <div class="col-md-3">
            <label class="form-label">Opérateur</label>
            <select name="operateur_id" class="form-select">
                <?php foreach ($operateurs as $op): ?>
                    <option value="<?= $op['id'] ?>"><?= esc($op['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Type d'opération</label>
            <select name="type_operation_id" class="form-select" required>
                <?php foreach ($types as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= esc($t['libelle']) ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Montant min</label>
            <input type="number" name="montant_min" class="form-control glass-input" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Montant max</label>
            <input type="number" name="montant_max" class="form-control glass-input" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Frais</label>
            <input type="number" name="frais" class="form-control glass-input" required>
        </div>
        <div class="col-12 d-grid d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-light app-btn">Ajouter le barème</button>
        </div>
    </form>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Frais</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($baremes as $b): ?>
                    <tr>
                        <td><?= esc($b['libelle']) ?></td>
                        <td>
                            <input form="edit-bareme-<?= $b['id'] ?>" type="number" name="montant_min" value="<?= esc($b['montant_min']) ?>" class="form-control form-control-sm glass-input">
                        </td>
                        <td>
                            <input form="edit-bareme-<?= $b['id'] ?>" type="number" name="montant_max" value="<?= esc($b['montant_max']) ?>" class="form-control form-control-sm glass-input">
                        </td>
                        <td>
                            <input form="edit-bareme-<?= $b['id'] ?>" type="number" name="frais" value="<?= esc($b['frais']) ?>" class="form-control form-control-sm glass-input">
                        </td>
                        <td class="text-end">
                            <form id="edit-bareme-<?= $b['id'] ?>" action="<?= base_url('operateur/baremes/modifier/' . $b['id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-light">Modifier</button>
                            </form>
                            <a href="<?= base_url('operateur/baremes/supprimer/' . $b['id']) ?>" class="btn btn-sm btn-outline-danger ms-2" onclick="return confirm('Supprimer ce bareme ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
