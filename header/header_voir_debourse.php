<?php
require_once 'model/Database.php';
require_once 'model/User.php';
require_once 'model/Devis.php';


$devisId = intval($_GET['devisId'] ?? 0);

if ($devisId <= 0) {
    header("Location: liste_devis.php");
    exit;
}

$pdo = Database::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}
$devisModel = new Devis($pdo);
$userModel = new User($pdo);

$devis = $devisModel->getDevisById($devisId);
$lignes = $devisModel->getLignesDevis($devisId);
$debourses = $devisModel->getDeboursesByDevis($devisId);
$utilisateurs = $userModel->getUtilisateurs();

// Statistiques
$totalDebourse = 0;
$categoriesStats = [];
$nbSousLignes = 0;
foreach ($debourses as $debourse) {
    $sousLignes = $devisModel->getLignesDebourse($debourse['id']);
    foreach ($sousLignes as $sous) {
        $totalDebourse += $sous['montant'];
        $categoriesStats[$sous['categorie']] = ($categoriesStats[$sous['categorie']] ?? 0) + $sous['montant'];
        $nbSousLignes++;
    }
}

// Calculer le total général
$devisModel->syncAllDebourseMontants($devisId);


$page = "liste_debourse";
