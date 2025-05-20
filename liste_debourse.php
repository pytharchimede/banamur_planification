<?php
include 'auth_check.php';
include 'header/header_liste_devis.php';
require_once 'model/Database.php';
require_once 'model/Devis.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

// Récupération de tous les déboursés
$debourses = $devisModel->getAllDeboursesWithDevis();

foreach ($debourses as &$deb) {
    $deb['libelle_ligne'] = $devisModel->getLibelleLigneDevisByDebourseId($deb['id']);
}
unset($deb);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Déboursés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .debours-card {
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            border-radius: 18px;
            transition: transform .2s;
            border: none;
        }

        .debours-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: 0 8px 32px rgba(0, 123, 255, 0.15);
        }

        .debours-header {
            background: linear-gradient(90deg, #305496 60%, #00b894 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            padding: 1rem 1.5rem;
        }

        .debours-footer {
            background: #f8f9fa;
            border-radius: 0 0 18px 18px;
            padding: 1rem 1.5rem;
        }

        .debours-actions .btn {
            min-width: 120px;
        }

        .badge-devis {
            background: #00b894;
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://fidest.ci/logo/new_logo_banamur.jpg" alt="Logo" style="height:40px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php $page = "liste_debourse";
                include 'menu.php'; ?>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h1 class="text-center mb-4 wow-title"><i class="fas fa-coins"></i> Liste des Déboursés</h1>
        <div class="row g-4">
            <?php if (empty($debourses)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">Aucun déboursé trouvé.</div>
                </div>
            <?php else: ?>
                <?php foreach ($debourses as $deb): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card debours-card h-100">
                            <div class="debours-header d-flex flex-column gap-2">
                                <div>
                                    <span class="badge badge-devis mb-2">
                                        <i class="fas fa-file-invoice"></i> Devis <?= htmlspecialchars($deb['numero_devis']) ?>
                                    </span>
                                </div>
                                <div class="fs-5 fw-bold">
                                    <i class="fas fa-coins"></i> Déboursé #<?= $deb['id'] ?>
                                </div>
                                <div class="small">Client : <span class="fw-semibold"><?= htmlspecialchars($deb['destine_a']) ?></span></div>
                                <div class="small">Opération : <span class="fw-semibold"><?= htmlspecialchars($deb['libelle_ligne']) ?></span></div>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-3">
                                    <li><i class="fas fa-calendar-day text-primary"></i> <b>Début :</b> <?= htmlspecialchars($deb['date_debut'] ?? '-') ?></li>
                                    <li><i class="fas fa-calendar-check text-success"></i> <b>Fin :</b> <?= htmlspecialchars($deb['date_fin'] ?? '-') ?></li>
                                    <li><i class="fas fa-money-bill-wave text-success"></i> <b>Montant :</b> <?= number_format($deb['montant_debourse'] ?? 0, 0, ',', ' ') ?> FCFA</li>
                                </ul>
                            </div>
                            <div class="debours-footer d-flex justify-content-between debours-actions">
                                <a href="voir_debourse.php?devisId=<?= $deb['devis_id'] ?>" class="btn btn-outline-primary" title="Consulter">
                                    <i class="fas fa-eye"></i> Consulter
                                </a>
                                <a href="request/export_debourse_pdf.php?devis_id=<?= $deb['devis_id'] ?>" class="btn btn-success" target="_blank" title="Exporter PDF">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                                <a href="request/export_debourse_excel.php?devis_id=<?= $deb['devis_id'] ?>" class="btn btn-outline-success" target="_blank" title="Exporter Excel">
                                    <i class="fas fa-file-excel"></i> Excel
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer text-white text-center py-3 mt-5">
        <div class="container">
            <p>&copy; <?= gmdate('Y'); ?> BANAMUR INDUSTRIES & TECH. Tous droits réservés.</p>
            <div class="social-icons">
                <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin-in"></a>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>