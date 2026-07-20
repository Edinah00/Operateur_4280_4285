<?php
$pageTitle = 'Montants à envoyer';
$bodyClass = 'page-operateur';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Montants à envoyer aux autres opérateurs</h1>
        <p class="hero-subtitle mb-0">Montants transférés (+ commission) à reverser suite aux transferts sortants vers des numéros externes.</p>
    </div>
    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Opérateur externe</th>
                    <th class="text-end">Nb transferts</th>
                    <th class="text-end">Montant transféré</th>
                    <th class="text-end">Commission due</th>
                    <th class="text-end">Total à reverser</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lignes)): ?>
                    <tr><td colspan="5" class="text-center text-muted">Aucun transfert externe pour le moment.</td></tr>
                <?php endif; ?>
                <?php foreach ($lignes as $l): ?>
                    <tr>
                        <td><?= esc($l['nom']) ?></td>
                        <td class="text-end"><?= (int) $l['nombre'] ?></td>
                        <td class="text-end"><?= number_format((float) $l['montant_total'], 0, ',', ' ') ?> Ar</td>
                        <td class="text-end"><?= number_format((float) $l['commission_total'], 0, ',', ' ') ?> Ar</td>
                        <td class="text-end fw-semibold"><?= number_format((float) $l['montant_total'] + (float) $l['commission_total'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
<p class="small text-muted mt-3">
    Le frais de transfert appliqué au client reste acquis à notre opérateur (voir « Gains »).
    Seuls le montant envoyé et la commission sont dus à l'opérateur externe.
</p>
<?= $this->endSection() ?>
