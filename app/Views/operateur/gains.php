<?php
$pageTitle = 'Gains';
$bodyClass = 'page-operateur';
$totalInterne = (float) ($total_gains_interne['total'] ?? 0);
$totalExterne = (float) ($total_gains_externe['total'] ?? 0);
$total = $totalInterne + $totalExterne;
$operateurCourant = null;
foreach ($operateurs as $op) {
    if ((int) $op['id'] === (int) $operateur_id) {
        $operateurCourant = $op;
        break;
    }
}
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Gains</h1>
    </div>
    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="panel-card p-4 mb-4">
    <form method="get" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Opérateur</label>
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

<section class="soft-grid cols-2 mb-4">
    <div class="metric-card panel-card">
        <div class="detail-key">Total des gains</div>
        <div class="metric-value mt-2"><?= number_format($total, 0, ',', ' ') ?> Ar</div>
    </div>
    <div class="metric-card panel-card">
        <div class="detail-key">Opérateur courant</div>
        <div class="detail-value mt-2"><?= esc($operateurCourant['nom'] ?? '—') ?></div>
    </div>
</section>

<section class="soft-grid cols-2 mb-4">
    <div class="metric-card panel-card">
        <div class="detail-key">Gains internes (nos clients)</div>
        <div class="metric-value mt-2"><?= number_format($totalInterne, 0, ',', ' ') ?> Ar</div>
    </div>
    <div class="metric-card panel-card">
        <div class="detail-key">Gains sur transferts vers un autre opérateur</div>
        <div class="metric-value mt-2"><?= number_format($totalExterne, 0, ',', ' ') ?> Ar</div>
        <a href="<?= base_url('operateur/montants-a-envoyer') ?>" class="small">Voir les montants à reverser →</a>
    </div>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th class="text-end">Gains</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gains_par_type as $g): ?>
                    <tr>
                        <td><?= esc($g['libelle']) ?></td>
                        <td class="text-end"><?= number_format($g['total'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
