<?php
$pageTitle = 'Aperçu transfert';
$bodyClass = 'page-client';
$destLabel = $operateur_dest['type'] === 'autre_operateur'
    ? ($operateur_dest['nom'] ?? 'Opérateur externe')
    : ($operateur_dest['nom'] ?? 'Opérateur');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Aperçu transfert</h1>
    </div>
    <a href="<?= base_url('client/transfert') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="row g-4">
    <div class="col-lg-7">
        <div class="panel-card p-4 h-100">
            <div class="soft-grid cols-2">
                <div>
                    <div class="detail-key">Destinataire</div>
                    <div class="detail-value mt-1"><?= esc($numero_dest) ?></div>
                </div>
                <div>
                    <div class="detail-key">Opérateur</div>
                    <div class="detail-value mt-1"><?= esc($destLabel) ?></div>
                </div>
                <div>
                    <div class="detail-key">Montant</div>
                    <div class="detail-value mt-1"><?= number_format($montant, 0, ',', ' ') ?> Ar</div>
                </div>
                <div>
                    <div class="detail-key">Montant reçu</div>
                    <div class="detail-value mt-1"><?= number_format($montant_recu, 0, ',', ' ') ?> Ar</div>
                </div>
                <div>
                    <div class="detail-key">Frais transfert</div>
                    <div class="detail-value mt-1"><?= number_format($frais_transfert, 0, ',', ' ') ?> Ar</div>
                </div>
                <div>
                    <div class="detail-key">Débit total</div>
                    <div class="detail-value mt-1"><?= number_format($total_debit_emetteur, 0, ',', ' ') ?> Ar</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="panel-card p-4 h-100">
            <div class="detail-key mb-2">Détail</div>
            <?php if ($operateur_dest['type'] === 'autre_operateur'): ?>
                <div class="mb-3">
                    <div class="detail-key">Commission</div>
                    <div class="detail-value mt-1"><?= number_format((float) $commission_appliquee, 0, ',', ' ') ?> Ar</div>
                </div>
            <?php else: ?>
                <div class="mb-3">
                    <div class="detail-key">Frais destinataire</div>
                    <div class="detail-value mt-1"><?= $frais_retrait_estime > 0 ? number_format($frais_retrait_estime, 0, ',', ' ') . ' Ar' : 'Non appliqué' ?></div>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <div class="detail-key">Numéro émetteur</div>
                <div class="detail-value mt-1"><?= esc($numero_emet) ?></div>
            </div>

            <form action="<?= base_url('client/transfert') ?>" method="post" class="vstack gap-2 mt-4">
                <?= csrf_field() ?>
                <input type="hidden" name="numero_destinataire" value="<?= esc($numero_dest) ?>">
                <input type="hidden" name="montant" value="<?= esc($montant) ?>">
                <?php if (! empty($frais_inclus)): ?>
                    <input type="hidden" name="frais_retrait_inclus" value="1">
                <?php endif; ?>
                <button type="submit" class="btn btn-light app-btn w-100">Confirmer</button>
                <a href="<?= base_url('client/transfert') ?>" class="btn btn-outline-light app-btn w-100">Modifier</a>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
