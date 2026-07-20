<?php
$pageTitle = 'Détail opération';
$bodyClass = 'page-client';

$type = $operation['type_libelle'];
$isTransfer = $type === 'transfert';
$labelAffiche = match ($type) {
    'depot'     => 'Dépôt',
    'retrait'   => 'Retrait',
    'transfert' => 'Transfert',
    default     => ucfirst($type),
};
$badgeClass = match ($type) {
    'depot'     => 'text-bg-success',
    'retrait'   => 'text-bg-warning',
    'transfert' => 'text-bg-info',
    default     => 'text-bg-secondary',
};
$isExternal = ! empty($operation['autre_operateur_id']);
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Détail</h1>
    </div>
    <a href="<?= base_url('client/historique') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="row g-4">
    <div class="col-lg-7">
        <div class="panel-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="badge <?= $badgeClass ?> badge-soft"><?= esc($labelAffiche) ?></span>
                <span class="muted-copy"><?= esc(date('d/m/Y H:i', strtotime($operation['date']))) ?></span>
            </div>

            <div class="soft-grid cols-2">
                <div>
                    <div class="detail-key">Montant</div>
                    <div class="detail-value mt-1"><?= number_format($operation['montant'], 0, ',', ' ') ?> Ar</div>
                </div>
                <div>
                    <div class="detail-key">Frais</div>
                    <div class="detail-value mt-1"><?= number_format($operation['frais_applique'], 0, ',', ' ') ?> Ar</div>
                </div>
                <div>
                    <div class="detail-key">Compte source</div>
                    <div class="detail-value mt-1"><?= esc($operation['client_nom_source']) ?></div>
                    <div class="muted-copy small"><?= esc($operation['client_numero_source']) ?></div>
                </div>
                <div>
                    <div class="detail-key">Opérateur</div>
                    <div class="detail-value mt-1"><?= esc($isExternal ? ($operation['autre_operateur_nom'] ?? 'Externe') : ('ID #' . $operation['operateur_id'])) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel-card p-4 h-100">
            <div class="detail-key mb-2">Résumé</div>
            <h2 class="h4 mb-3">Opération enregistrée</h2>

            <?php if ($isTransfer): ?>
                <div class="mb-3">
                    <div class="detail-key">Destination</div>
                    <?php if ($isExternal): ?>
                        <div class="detail-value mt-1"><?= esc($operation['numero_destinataire_externe'] ?? '—') ?></div>
                        <div class="muted-copy small"><?= esc($operation['autre_operateur_nom'] ?? 'Opérateur externe') ?></div>
                    <?php else: ?>
                        <div class="detail-value mt-1"><?= esc($operation['client_nom_dest'] ?? '—') ?></div>
                        <div class="muted-copy small"><?= esc($operation['client_numero_dest'] ?? '—') ?></div>
                    <?php endif; ?>
                </div>
                <?php if ($isExternal): ?>
                    <div class="mb-3">
                        <div class="detail-key">Commission</div>
                        <div class="detail-value mt-1"><?= number_format((float) ($operation['commission_appliquee'] ?? 0), 0, ',', ' ') ?> Ar</div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="mb-3">
                    <div class="detail-key">Destination</div>
                    <div class="detail-value mt-1">—</div>
                </div>
            <?php endif; ?>

            <div class="mini-card mt-4">
                <div class="detail-key">Type d'opération</div>
                <div class="detail-value mt-1"><?= esc($labelAffiche) ?></div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
