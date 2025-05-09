<?php
require_once '../model/Database.php';
require_once '../model/Devis.php';
require_once '../model/Client.php';
require_once '../model/Offre.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);
$clientModel = new Client($pdo);
$offreModel = new Offre($pdo);

// Vérifier si l'identifiant du devis est passé en paramètre
if (isset($_GET['devisId'])) {
    $id_devis = $_GET['devisId'];

    // Récupérer les détails du devis via la classe
    $devis = $devisModel->getDevisById($id_devis);
    if (!$devis) {
        die("Devis non trouvé.");
    }

    // Récupérer les lignes du devis via la classe
    $lignes_devis = $devisModel->getLignesDevis($id_devis);
} else {
    die("Identifiant du devis manquant.");
}

// Récupérer les clients via la classe
$clients = $clientModel->getAllClients();

// Récupérer les offres via la classe
$offres = $offreModel->getAllOffres();
