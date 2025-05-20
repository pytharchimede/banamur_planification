<?php
require_once '../model/Database.php';

$pdo = (new Database())->getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$designation = $_POST['designation'] ?? '';
$montant = $_POST['montant'] ?? 0;
$date_debut = $_POST['date_debut'] ?? null;
$date_fin = $_POST['date_fin'] ?? null;
$devis_id = $_POST['devis_id'] ?? 0;
$responsable_id = $_POST['responsable_id'] ?? 1; // À adapter selon ton système utilisateur

if (!$designation || !$montant || !$devis_id) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

try {
    // 1. Ajouter la ligne dans ligne_devis_banamur
    $stmt = $pdo->prepare("INSERT INTO ligne_devis_banamur (devis_id, designation, prix, quantite, unite_id, total, groupe)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $prix = $montant;
    $quantite = 1;
    $unite_id = 1; // à adapter si besoin
    $total = $montant;
    $groupe = ''; // plus de catégorie
    $stmt->execute([$devis_id, $designation, $prix, $quantite, $unite_id, $total, $groupe]);
    $ligne_devis_id = $pdo->lastInsertId();

    // 2. Ajouter le déboursé dans debourse_banamur
    $stmt2 = $pdo->prepare("INSERT INTO debourse_banamur (devis_id, ligne_devis_id, montant_debourse, responsable_id, date_debut, date_fin)
        VALUES (?, ?, ?, ?, ?, ?)");
    $stmt2->execute([$devis_id, $ligne_devis_id, $montant, $responsable_id, $date_debut, $date_fin]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
