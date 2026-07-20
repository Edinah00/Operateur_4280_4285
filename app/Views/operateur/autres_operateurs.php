<?php
$pageTitle = 'Autres opérateurs';
$bodyClass = 'page-operateur';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Autres opérateurs</h1>
        <p class="hero-subtitle mb-0">Préfixes externes et commission appliquée sur les transferts sortants.</p>
    </div>
    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="panel-card p-4 mb-4">
    <form action="<?= base_url('operateur/autres-operateurs/ajouter') ?>" method="post" class="row g-3 align-items-end">
        <?= csrf_field() ?>
        <div class="col-md-3">
            <label class="form-label">Préfixe externe</label>
            <input type="text" name="prefixe" class="form-control glass-input" placeholder="ex: 032" required maxlength="3" value="<?= esc(set_value('prefixe')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Opérateur</label>
            <select name="operateur_id" id="operateur_id" class="form-select" required>
                <option value="nouveau">Nouvel opérateur</option>
                <?php foreach ($autresOperateurs as $op): ?>
                    <option value="<?= esc($op['id']) ?>"><?= esc($op['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3" id="new-nom-wrap">
            <label class="form-label">Nom de l'opérateur</label>
            <input type="text" name="nom" class="form-control glass-input" placeholder="ex: Orange Money" value="<?= esc(set_value('nom')) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label">Commission (%)</label>
            <input type="number" step="0.01" min="0" name="commission_pourcentage" class="form-control glass-input" placeholder="ex: 2.5" required value="<?= esc(set_value('commission_pourcentage')) ?>">
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-light app-btn">Ajouter</button>
        </div>
    </form>
    <p class="small text-muted mt-2 mb-0">
        La commission (%) s'applique sur le montant envoyé lors d'un transfert vers ce préfixe externe.
        Le frais de transfert habituel reste acquis à notre opérateur.
    </p>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Préfixe</th>
                    <th>Opérateur externe</th>
                    <th>Commission actuelle</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($associations as $a): ?>
                    <?php
                        $op = null;
                        foreach ($autresOperateurs as $o) {
                            if ((int) $o['id'] === (int) $a['operateur_id']) {
                                $op = $o;
                                break;
                            }
                        }
                    ?>
                    <tr>
                        <td><?= esc($a['prefixe']) ?></td>
                        <td><?= esc($a['operateur_nom']) ?></td>
                        <td style="min-width: 180px;">
                            <form action="<?= base_url('operateur/autres-operateurs/commission/' . $a['operateur_id']) ?>" method="post" class="d-flex gap-2">
                                <?= csrf_field() ?>
                                <input type="number" step="0.01" min="0" name="commission_pourcentage" class="form-control form-control-sm" value="<?= esc($op['commission_pourcentage'] ?? 0) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-light">OK</button>
                            </form>
                        </td>
                        <td class="text-end">
                            <form action="<?= base_url('operateur/autres-operateurs/supprimer/' . $a['operateur_id'] . '/' . $a['prefixe_id']) ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ce préfixe externe ?')">Supprimer</button>
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
    const newNomWrap = document.getElementById('new-nom-wrap');
    const newNomInput = newNomWrap?.querySelector('input');

    function toggleNom() {
        const show = operateurSelect?.value === 'nouveau';
        newNomWrap.style.display = show ? 'block' : 'none';
        if (newNomInput) newNomInput.required = show;
    }
    operateurSelect?.addEventListener('change', toggleNom);
    toggleNom();
</script>
<?= $this->endSection() ?>
