<?php
require_once 'model/Database.php';
require_once 'model/Chantier.php';
require_once 'model/Operation.php';
require_once 'model/Designation.php';
require_once 'model/Precision.php';

$pdo = (new Database())->getConnection();

$chantierId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($chantierId <= 0) {
    die("ID de chantier invalide.");
}

$chantierModel = new Chantier($pdo);
$chantier = $chantierModel->getById($chantierId);
if (!$chantier) {
    die("Chantier introuvable.");
}

$operationModel = new Operation($pdo);
$operations = $operationModel->getByChantier($chantierId);
if (!$operations) {
    die("Aucune opération trouvée pour ce chantier.");
}

$designationModel = new Designation($pdo);
$designations = $designationModel->getByChantier($chantierId);
if (!$designations) {
    die("Aucune désignation trouvée pour ce chantier.");
}

$precisionModel = new PrecisionFiche($pdo);
$precisions = $precisionModel->getByChantier($chantierId);
if (!$precisions) {
    die("Aucune précision trouvée pour ce chantier.");
}

if (!$chantier) die("Chantier introuvable.");
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails Chantier</title>
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
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-hard-hat"></i> Banamur</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="liste_devis.php">Devis</a></li>
                    <li class="nav-item"><a class="nav-link" href="liste_chantiers.php">Chantiers</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="section-title"><i class="fas fa-warehouse"></i> Chantier : <?= htmlspecialchars($chantier['titre']) ?> (<?= htmlspecialchars($chantier['code']) ?>)</div>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">Informations générales</div>
                    <div class="card-body">
                        <p><b>Client :</b> <?= htmlspecialchars($chantier['client_id']) ?></p>
                        <p><b>Devis :</b> <?= htmlspecialchars($chantier['devis_id']) ?></p>
                        <p><b>Date création :</b> <?= htmlspecialchars($chantier['date_creation']) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-title"><i class="fas fa-list"></i> Opérations (Lignes Devis)</div>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Qté</th>
                            <th>Unité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($operations as $op): ?>
                            <tr>
                                <td><?= htmlspecialchars($op['designation']) ?></td>
                                <td><?= htmlspecialchars($op['quantite']) ?></td>
                                <td><?= htmlspecialchars($op['unite_id']) ?></td>
                                <td><?= number_format($op['prix'], 0, ',', ' ') ?></td>
                                <td><?= number_format($op['total'], 0, ',', ' ') ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="section-title"><i class="fas fa-coins"></i> Désignations (Titres Déboursés)</div>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Montant</th>
                            <th>Opération liée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($designations as $des): ?>
                            <tr>
                                <td><?= htmlspecialchars($des['designation']) ?></td>
                                <td><?= number_format($des['montant'], 0, ',', ' ') ?></td>
                                <td>
                                    <?php
                                    $operation = $operationModel->getById($des['operation_id']);
                                    echo htmlspecialchars($operation ? $operation['designation'] : '');
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="section-title"><i class="fas fa-info-circle"></i> Précisions (Lignes déboursés)</div>
        <div class="card mb-4">
            <div class="card-body p-0">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Libellé</th>
                            <th>Montant</th>
                            <th>Désignation liée</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($precisions as $prec): ?>
                            <tr>
                                <td><?= htmlspecialchars($prec['libelle']) ?></td>
                                <td><?= number_format($prec['montant'], 0, ',', ' ') ?></td>
                                <td><?= htmlspecialchars($prec['designation_id']) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>