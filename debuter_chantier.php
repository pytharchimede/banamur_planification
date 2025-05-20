<?php
session_start();
require_once 'model/Database.php';
require_once 'model/Devis.php';
require_once 'model/Offre.php';
require_once 'model/Client.php';
require_once 'model/UniteMesure.php';
require_once 'model/Chantier.php';
require_once 'model/Operation.php';
require_once 'model/Designation.php';
require_once 'model/Precision.php';
require_once 'model/Utils.php';

$databaseObj = new Database();
$pdo = $databaseObj->getConnection();

$devisId = isset($_GET['devisId']) ? intval($_GET['devisId']) : 0;
$devisModel = new Devis($pdo);
$devis = $devisModel->getDevisById($devisId);
if (!$devis) die("Devis introuvable.");

$offreModel = new Offre($pdo);
$offre = $offreModel->getOffreById($devis['offre_id']);
if (!$offre) die("Offre introuvable.");

$clientModel = new Client($pdo);
$client = $clientModel->getClientById($devis['client_id']);
if (!$client) die("Client introuvable.");

$uniteModel = new UniteMesure($pdo);
$unites = $uniteModel->getAll();
if (!$unites) die("Aucune unité de mesure trouvée.");

$debourses = $devisModel->getDeboursesByDevisId($devisId);
if (!$debourses) die("Aucun déboursé trouvé.");

$chantierModel = new Chantier($pdo);
$lastIndex = $chantierModel->getLastIndex();
$codeChantier = 'CH' . str_pad($lastIndex + 1, 4, '0', STR_PAD_LEFT);

// Récupération des lignes du devis
$lignesDevis = $devisModel->getLignesDevis($devisId);

// Récupérer toutes les lignes de déboursé liées aux déboursés de ce devis
$precisionModel = new PrecisionFiche($pdo);
$precisions = $precisionModel->getByDevis($devisId);

// Pour la démo JS, on prépare tout côté client
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Débuter un chantier</title>
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

        .wizard-nav {
            margin-bottom: 40px;
        }

        .wizard-nav .step {
            display: inline-block;
            min-width: 120px;
            text-align: center;
            color: #888;
            font-weight: bold;
            border-bottom: 3px solid #ffe066;
            padding-bottom: 8px;
            margin-right: 10px;
        }

        .wizard-nav .step.active {
            color: #111;
            border-bottom: 3px solid #ffc107;
            background: #ffe066;
            border-radius: 8px 8px 0 0;
        }

        .form-section {
            background: #fffde7;
            border-radius: 8px;
            box-shadow: 0 2px 8px #0001;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #ffe066;
        }

        .btn-primary,
        .btn-success {
            background: #ffc107;
            border: none;
            color: #111;
            font-weight: bold;
        }

        .btn-primary:hover,
        .btn-success:hover {
            background: #ffe066;
            color: #111;
        }

        .btn-outline-dark {
            border-color: #ffc107;
            color: #111;
        }

        .btn-outline-dark:hover {
            background: #ffc107;
            color: #111;
        }

        .table thead {
            background: #ffe066;
        }

        .stepper-btns {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
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

    <div class="container py-4">
        <div class="wizard-nav text-center mb-4">
            <span class="step" id="step-nav-1">1. Chantier</span>
            <span class="step" id="step-nav-2">2. Opérations</span>
            <span class="step" id="step-nav-3">3. Déboursés</span>
            <span class="step" id="step-nav-4">4. Précisions</span>
        </div>

        <form id="wizardForm">
            <!-- Etape 1 -->
            <div class="form-section" id="step-1">
                <h3 class="mb-4"><i class="fas fa-warehouse"></i> Étape 1 : Créer un chantier (Devis)</h3>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Titre du chantier</label>
                        <input type="text" name="titre" class="form-control" value="<?= htmlspecialchars($offre['reference_offre']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Code chantier</label>
                        <input type="text" name="code" class="form-control" value="<?= htmlspecialchars($codeChantier) ?>" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Client</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($client['nom_client']) ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Offre</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($offre['num_offre']) ?>" disabled>
                    </div>
                </div>
            </div>
            <!-- Etape 2 -->
            <div class="form-section d-none" id="step-2">
                <h3 class="mb-4"><i class="fas fa-list"></i> Étape 2 : Intégration des opérations (Lignes devis)</h3>
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Qté</th>
                            <th>Unité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="table-operations">
                    </tbody>
                </table>
            </div>
            <!-- Etape 3 -->
            <div class="form-section d-none" id="step-3">
                <h3 class="mb-4"><i class="fas fa-coins"></i> Étape 3 : Intégration des désignations (Titres déboursés)</h3>
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Montant</th>
                            <th>Opération liée</th>
                        </tr>
                    </thead>
                    <tbody id="table-debourses">
                    </tbody>
                </table>
            </div>
            <!-- Etape 4 -->
            <div class="form-section d-none" id="step-4">
                <h3 class="mb-4"><i class="fas fa-info-circle"></i> Étape 4 : Intégration des précisions (Lignes déboursés)</h3>
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Catégorie</th>
                            <th>Précision</th>
                            <th>Montant</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Ligne devis ID</th>
                            <th>Désignation ligne devis</th>
                            <th>Déboursé ID</th>
                        </tr>
                    </thead>
                    <tbody id="table-precisions">
                    </tbody>
                </table>
            </div>
            <div class="stepper-btns mt-3">
                <button type="button" class="btn btn-outline-dark" id="prevBtn" disabled><i class="fas fa-arrow-left"></i> Précédent</button>
                <button type="button" class="btn btn-primary" id="nextBtn">Suivant <i class="fas fa-arrow-right"></i></button>
            </div>
        </form>
    </div>
    <script>
        const lignesDevis = <?= json_encode($lignesDevis) ?>;
        const unites = <?= json_encode($unites) ?>;
        const debourses = <?= json_encode($debourses) ?>;
        const precisions = <?= json_encode($precisions) ?>;

        function getUniteSymbole(id) {
            const u = unites.find(u => u.id == id);
            return u ? u.symbole : '';
        }

        function fillOperations() {
            let html = '';
            console.log(debourses);

            lignesDevis.forEach(ligne => {
                html += `<tr>
                    <td>${ligne.designation}</td>
                    <td>${ligne.quantite}</td>
                    <td>${getUniteSymbole(ligne.unite_id)}</td>
                    <td>${Number(ligne.prix).toLocaleString()}</td>
                    <td>${Number(ligne.total).toLocaleString()}</td>
                </tr>`;
            });
            document.getElementById('table-operations').innerHTML = html;
        }

        let currentStep = 1;
        const totalSteps = 4;
        const stepNavs = [...document.querySelectorAll('.wizard-nav .step')];

        function showStep(step) {
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById('step-' + i).classList.toggle('d-none', i !== step);
                stepNavs[i - 1].classList.toggle('active', i === step);
            }
            document.getElementById('prevBtn').disabled = (step === 1);
            document.getElementById('nextBtn').innerHTML = (step === totalSteps) ? 'Valider <i class="fas fa-check"></i>' : 'Suivant <i class="fas fa-arrow-right"></i>';
        }
        showStep(currentStep);

        function fillDebourses() {
            document.getElementById('table-debourses').innerHTML = debourses.map(d =>
                `<tr>
                    <td>${d.designation_ligne}</td>
                    <td>${Number(d.montant_debourse).toLocaleString()}</td>
                    <td>${d.ligne_devis_id}</td>
                </tr>`
            ).join('');
        }

        function fillPrecisions() {
            console.log('Précisions :', precisions);
            document.getElementById('table-precisions').innerHTML = precisions.map(p =>
                `<tr>
            <td>${p.categorie}</td>
            <td>${p.designation}</td>
            <td>${Number(p.montant).toLocaleString()}</td>
            <td>${p.date_debut ?? ''}</td>
            <td>${p.date_fin ?? ''}</td>
            <td>${p.ligne_devis_id}</td>
            <td>${p.designation_ligne}</td>
            <td>${p.debourse_id}</td>
        </tr>`
            ).join('');
        }

        document.getElementById('nextBtn').onclick = function() {
            if (currentStep === 1) {
                const titre = document.querySelector('[name="titre"]').value.trim();
                const code = document.querySelector('[name="code"]').value.trim();
                if (!titre || !code) {
                    alert('Veuillez remplir le titre et le code du chantier.');
                    return;
                }
            }
            if (currentStep === 2) fillDebourses();
            if (currentStep === 3) fillPrecisions();

            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                if (currentStep === 2) fillOperations();
            } else {
                const formData = new FormData(document.getElementById('wizardForm'));
                fetch('request/creer_chantier.php', {
                    method: 'POST',
                    body: formData
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        window.location.href = 'details_chantier.php?id=' + res.chantierId;
                    } else {
                        alert(res.message || 'Erreur lors de la validation.');
                    }
                });
            }
        };
        document.getElementById('prevBtn').onclick = function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        };
    </script>
    <script>
        const precisions = <?= json_encode($precisions) ?>;
    </script>
</body>

</html>