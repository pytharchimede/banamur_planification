<?php
session_start();
require_once '../model/Database.php';
require_once '../model/Devis.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

// Utiliser une méthode du modèle pour récupérer les stats
$stats = $devisModel->getNombreDevisParJour();

$labels = [];
$data = [];

foreach ($stats as $row) {
    $labels[] = $row['date'];
    $data[] = (int)$row['count'];
}

echo json_encode([
    'labels' => $labels,
    'data' => $data
]);
