<?php

require_once 'model/Database.php';
require_once 'model/Devis.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

// Récupérer les filtres depuis $_GET
$filtres = [
    'date_debut' => $_GET['date_debut'] ?? null,
    'date_fin' => $_GET['date_fin'] ?? null,
    'emis_par' => $_GET['emis_par'] ?? null,
    'destine_a' => $_GET['destine_a'] ?? null,
];

// Utiliser la méthode du modèle pour récupérer les devis filtrés
$devis = $devisModel->getDevisFiltres($filtres);

// Calcul du montant total TTC des devis affichés
$total_ttc = 0;
$nb_devis = 0;
foreach ($devis as $de) {
    $total_ttc += $de['total_ttc'];
    $nb_devis++;
}
