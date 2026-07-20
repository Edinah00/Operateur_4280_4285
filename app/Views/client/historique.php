<?php
$pageTitle = 'Historique';
$bodyClass = 'page-client';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Historique</h1>
    </div>
    <a href="<?= base_url('client') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th class="text-end">Montant</th>
                    <th class="text-end">Frais</th>
                    <th class="text-end">Détail</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($operations)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-white-50">Aucune opération pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($operations as $op): ?>
                        <?php
                            $libelle = $op['type_libelle'];
                            $badgeClass = match ($libelle) {
                                'depot'     => 'text-bg-success',
                                'retrait'   => 'text-bg-warning',
                                'transfert' => 'text-bg-info',
                                default     => 'text-bg-secondary',
                            };
                            $labelAffiche = match ($libelle) {
                                'depot'     => 'Dépôt',
                                'retrait'   => 'Retrait',
                                'transfert' => 'Transfert',
                                default     => ucfirst($libelle),
                            };
                            $signe = $libelle === 'depot' ? '+' : '-';
                        ?>
                        <tr>
                            <td><?= esc(date('d/m/Y H:i', strtotime($op['date']))) ?></td>
                            <td><span class="badge <?= $badgeClass ?> badge-soft"><?= esc($labelAffiche) ?></span></td>
                            <td class="text-end"><?= $signe ?><?= number_format($op['montant'], 0, ',', ' ') ?> Ar</td>
                            <td class="text-end"><?= number_format($op['frais_applique'], 0, ',', ' ') ?> Ar</td>
                            <td class="text-end">
                                <a href="<?= base_url('client/historique/' . $op['id']) ?>" class="btn btn-sm btn-detail-history">Détail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
