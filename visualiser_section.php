<?php
include 'auth_check.php';
require_once 'model/Database.php';
require_once 'model/Devis.php';

$databaseObj = new Database();
$pdo = $databaseObj->getConnection();
$devisModel = new Devis($pdo);

$devisId = $_GET['devisId'] ?? null;
$section = $_GET['section'] ?? null;

if (!$devisId || !$section) {
    die('Paramètres manquants.');
}

$devis = $devisModel->getDevisById($devisId);

if (!$devis) {
    die('Devis introuvable.');
}

// Récupération de la valeur à afficher
$label = '';
$value = '';
switch ($section) {
    case 'description':
        $label = 'Description';
        $value = $devis['description'] ?? '';
        break;
    case 'delai':
        $label = 'Délai de réalisation';
        $value = $devis['delai_livraison'] ?? '';
        break;
    case 'conditions':
        $label = 'Conditions financières';
        $value = $devis['conditions'] ?? '';
        break;
    case 'garantie':
        $label = 'Garantie';
        $value = $devis['garantie'] ?? '';
        break;
    default:
        die('Section inconnue.');
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Visualiser <?= htmlspecialchars($label) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h3 class="mb-4">Visualisation : <?= htmlspecialchars($label) ?></h3>
        <form>
            <div class="mb-3">
                <label class="form-label"><?= htmlspecialchars($label) ?></label>
                <textarea class="form-control" rows="8" readonly><?= htmlspecialchars($value) ?></textarea>
            </div>
            <button type="button" class="btn btn-secondary" onclick="window.close();">Fermer</button>
        </form>
    </div>
</body>

</html>