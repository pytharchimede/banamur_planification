<?php
require_once 'model/Database.php';
require_once 'model/Devis.php';
require_once 'model/Client.php';

$pdo = (new Database())->getConnection();
$devisModel = new Devis($pdo);
$clientModel = new Client($pdo);

// Récupérer tous les devis sans chantier associé
$devisSansChantier = $devisModel->getDevisSansChantier(); // À implémenter dans Devis.php

$page = "liste_chantier";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Choisir un devis pour débuter un chantier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: #fffbe6;
        }

        .navbar {
            background: #111;
        }

        .navbar-brand,
        .navbar-nav .nav-link {
            color: #ffc107 !important;
        }

        .navbar-nav .nav-link.active {
            color: #111 !important;
            background: #ffe066;
            border-radius: 5px;
        }

        .section-title {
            color: #111;
            background: #ffe066;
            border-radius: 8px;
            padding: 10px 20px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .card {
            border: 1px solid #ffe066;
            border-radius: 10px;
            transition: box-shadow .2s;
        }

        .card:hover {
            box-shadow: 0 4px 24px #ffe06677;
        }

        .card-header {
            background: #ffe066;
            color: #111;
            font-weight: bold;
        }

        .btn-w {
            background: #ffc107;
            color: #111;
            border: none;
        }

        .btn-w:hover {
            background: #111;
            color: #ffc107;
        }
    </style>
</head>

<body>
    <?php include 'menu_decaissement.php'; ?>
    <div class="container">
        <div class="section-title"><i class="fas fa-file-signature"></i> Choisir un devis pour débuter un chantier</div>
        <div class="row g-4">
            <?php if (empty($devisSansChantier)): ?>
                <div class="col-12 text-center text-muted">
                    <i class="fas fa-check-circle fa-2x"></i><br>
                    Tous les devis ont déjà un chantier associé.
                </div>
            <?php else: ?>
                <?php foreach ($devisSansChantier as $devis):
                    $client = $clientModel->getClientById($devis['client_id']);
                ?>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">
                                <i class="fas fa-file-contract text-warning"></i> Devis #<?= htmlspecialchars($devis['numero_devis']) ?>
                            </div>
                            <div class="card-body">
                                <p><b>Client :</b> <?= htmlspecialchars($client['nom_client'] ?? '-') ?></p>
                                <p><b>Offre :</b> <?= htmlspecialchars($devis['offre_id'] ?? '-') ?></p>
                                <p><b>Date :</b> <?= htmlspecialchars($devis['created_at'] ?? '-') ?></p>
                                <p><b>Montant :</b> <?= number_format($devis['total_ttc'], 2, ',', ' ') ?> €</p>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <a href="debuter_chantier.php?devis_id=<?= $devis['id'] ?>" class="btn btn-w w-100">
                                    <i class="fas fa-play"></i> Débuter ce chantier
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            <?php endif ?>
        </div>
    </div>
</body>

</html>