<?php
$pageTitle = 'Préfixes';
$bodyClass = 'page-operateur';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Préfixes</h1>
    </div>
    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="panel-card p-4 mb-4">
    <form action="<?= base_url('operateur/prefixes/ajouter') ?>" method="post" class="row g-3 align-items-end">
        <?= csrf_field() ?>
        <div class="col-md-3">
            <label class="form-label">Nouveau préfixe</label>
            <input type="text" name="prefixe" class="form-control glass-input" placeholder="ex: 033" required maxlength="3" value="<?= esc(set_value('prefixe')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Opérateur associé</label>
            <select name="operateur_id" id="operateur_id" class="form-select" required>
                <option value="">Choisir un opérateur</option>
                <?php foreach ($operateurs as $operateur): ?>
                    <option value="<?= esc($operateur['id']) ?>" <?= set_value('operateur_id') == $operateur['id'] ? 'selected' : '' ?>>
                        <?= esc($operateur['nom']) ?>
                    </option>
                <?php endforeach; ?>
                <option value="autre" <?= set_value('operateur_id') === 'autre' ? 'selected' : '' ?>>Autre</option>
            </select>
        </div>
        
        <!-- Section "Autre" - CORRIGÉE -->
        <div class="col-md-6" id="new-operateur-wrap" style="display: none;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nouvel opérateur</label>
                    <input type="text" name="nouveau_operateur" class="form-control glass-input" placeholder="Nom du nouvel opérateur" value="<?= esc(set_value('nouveau_operateur')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Commission</label>
                    <input type="number" name="commission" class="form-control glass-input" placeholder="Commission" value="<?= esc(set_value('commission')) ?>" step="0.01" min="0">
                </div>
            </div>
        </div>
        
        <div class="col-12 d-grid d-md-flex justify-content-md-end">
            <button type="submit" class="btn btn-light app-btn">Ajouter le préfixe</button>
        </div>
    </form>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Préfixe</th>
                    <th>Opérateur</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($associations as $p): ?>
                    <tr>
                        <td><?= esc($p['prefixe']) ?></td>
                        <td><?= esc($p['operateur_nom']) ?></td>
                        <td class="text-end">
                            <form action="<?= base_url('operateur/prefixes/supprimer/' . $p['operateur_id'] . '/' . $p['prefixe_id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce préfixe ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>

<script>
    const operateurSelect = document.getElementById('operateur_id');
    const newOperateurWrap = document.getElementById('new-operateur-wrap');
    const newOperateurInput = newOperateurWrap?.querySelector('input[name="nouveau_operateur"]');

    operateurSelect?.addEventListener('change', function () {
        const show = this.value === 'autre';
        newOperateurWrap.style.display = show ? 'block' : 'none';
        if (newOperateurInput) {
            newOperateurInput.required = show;
        }
    });

    // Vérification initiale
    if (operateurSelect?.value === 'autre') {
        newOperateurWrap.style.display = 'block';
        if (newOperateurInput) {
            newOperateurInput.required = true;
        }
    }
</script>
<?= $this->endSection() ?>