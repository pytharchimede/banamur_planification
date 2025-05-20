<?php
require_once 'model/Database.php';
require_once 'model/Chantier.php';
require_once 'model/Client.php';
require_once 'model/Devis.php';

$pdo = (new Database())->getConnection();
$chantierModel = new Chantier($pdo);
$chantiers = $chantierModel->getAll(); // À implémenter si besoin

$clientModel = new Client($pdo);
$devisModel = new Devis($pdo);

$page = "liste_chantier";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des chantiers</title>
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
        }

        .card-header {
            background: #ffe066;
            color: #111;
            font-weight: bold;
        }

        .table thead {
            background: #ffe066;
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
        <div class="section-title"><i class="fas fa-warehouse"></i> Liste des chantiers</div>
        <div class="card mb-4 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Titre</th>
                            <th>Code</th>
                            <th>Client</th>
                            <th>Devis</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($chantiers)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aucun chantier trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php
                            $clientModel = new Client($pdo);
                            $devisModel = new Devis($pdo);
                            foreach ($chantiers as $chantier):
                                $client = $clientModel->getClientById($chantier['client_id']);
                                $devis = $devisModel->getDevisById($chantier['devis_id']);
                            ?>
                                <tr>
                                    <td><i class="fas fa-hard-hat text-warning"></i> <?= htmlspecialchars($chantier['id']) ?></td>
                                    <td><?= htmlspecialchars($chantier['titre']) ?></td>
                                    <td><?= htmlspecialchars($chantier['code']) ?></td>
                                    <td><?= htmlspecialchars($client['nom_client'] ?? '-') ?></td>
                                    <td>
                                        <?= htmlspecialchars($devis['numero_devis'] ?? '-') ?>
                                        <?php if (!empty($devis['offre_ref'])): ?>
                                            <br><small class="text-muted">Offre : <?= htmlspecialchars($devis['offre_ref']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($chantier['date_creation'] ?? '') ?></td>
                                    <td>
                                        <a href="details_chantier.php?id=<?= $chantier['id'] ?>" class="btn btn-sm btn-w">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="choisir_devis_pour_chantier.php" class="btn btn-dark">
            <i class="fas fa-plus"></i> Nouveau chantier
        </a>
    </div>
</body>

</html>