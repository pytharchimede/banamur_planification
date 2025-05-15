<?php
require_once '../model/Database.php';
require_once '../model/Devis.php';
header('Content-Type: application/json');

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

$devis_id = $_POST['devis_id'] ?? null;
$designation = $_POST['designation'] ?? null;
$categorie = $_POST['categorie'] ?? '';
$montant = $_POST['montant'] ?? null;
$date_debut = $_POST['date_debut'] ?? null;
$date_fin = $_POST['date_fin'] ?? null;

if (!$devis_id || !$designation || !$montant || !$date_debut || !$date_fin) {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
    exit;
}

error_log("devis_id=$devis_id, designation=$designation, categorie=$categorie, montant=$montant, date_debut=$date_debut, date_fin=$date_fin");
$debourse_id = $devisModel->addDebourse($devis_id, $date_debut, $date_fin);

if ($debourse_id) {
    $devisModel->addSousLigneDebourseByDebourseId($debourse_id, $categorie, $designation, $montant, $date_debut, $date_fin);
    $devisModel->updateDebourseResume($debourse_id);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout']);
}
