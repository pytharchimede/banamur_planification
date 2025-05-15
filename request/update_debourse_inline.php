<?php
require_once '../model/Database.php';
require_once '../model/Devis.php';
header('Content-Type: application/json');

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

// Vérifier si l'utilisateur est connecté
$id = $_POST['id'] ?? null;
$designation = $_POST['designation'] ?? null;
$montant = $_POST['montant'] ?? null;
$date_debut = $_POST['date_debut'] ?? null;
$date_fin = $_POST['date_fin'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

$ok = $devisModel->updateLigneDebourse($id, [
    'designation' => $designation,
    'montant' => $montant,
    'date_debut' => $date_debut,
    'date_fin' => $date_fin
]);

if ($ok) {
    // Recalculer montant total et période du déboursé parent
    $debourse = $devisModel->getDebourseByLigneDebourse($id);
    $devisModel->updateDebourseResume($debourse['id']); // <-- AJOUT ICI
    $totaux = $devisModel->getTotauxDebourse($debourse['id']);
    echo json_encode([
        'success' => true,
        'montant_debourse' => $totaux['montant_debourse'],
        'date_debut' => $totaux['date_debut'],
        'date_fin' => $totaux['date_fin']
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
}
