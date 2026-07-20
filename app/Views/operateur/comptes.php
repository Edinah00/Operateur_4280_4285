<?php
$pageTitle = 'Comptes';
$bodyClass = 'page-operateur';
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="page-shell mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div>
        <div class="hero-kicker">IT-Money</div>
        <h1 class="h3 mb-1">Comptes</h1>
    </div>
    <a href="<?= base_url('operateur') ?>" class="btn btn-outline-light app-btn">Retour</a>
</section>

<section class="table-card">
    <div class="table-responsive">
        <table class="table table-darkish align-middle mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Téléphone</th>
                    <th class="text-end">Solde</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comptes as $c): ?>
                    <tr>
                        <td><?= esc($c['nom']) ?></td>
                        <td><?= esc($c['numero_telephone']) ?></td>
                        <td class="text-end"><?= number_format($c['solde'], 0, ',', ' ') ?> Ar</td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</section>
<?= $this->endSection() ?>
