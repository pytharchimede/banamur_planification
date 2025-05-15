<?php
require_once '../model/Database.php';
require_once '../model/Devis.php';
header('Content-Type: application/json');

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

$debourse_id = $_POST['debourse_id'] ?? null;
$designation = $_POST['designation'] ?? null;
$categorie = $_POST['categorie'] ?? '';
$montant = $_POST['montant'] ?? null;
$date_debut = $_POST['date_debut'] ?? null;
$date_fin = $_POST['date_fin'] ?? null;

if (!$debourse_id || !$designation || !$montant || !$date_debut || !$date_fin) {
    echo json_encode(['success' => false, 'message' => 'Champs obligatoires manquants']);
    exit;
}

// Ajout de la sous-ligne via la classe Devis (pour gérer la catégorie)
$devisModel->addSousLigneDebourseByDebourseId($debourse_id, $categorie, $designation, $montant, $date_debut, $date_fin);

// Mettre à jour le résumé du déboursé
$devisModel->updateDebourseResume($debourse_id);

echo json_encode(['success' => true]);
