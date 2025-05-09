<?php

session_start();
require_once '../model/Database.php';
require_once '../model/Devis.php';

// Vérifiez que le devisId est défini dans la session ou dans l'URL
if (!isset($_SESSION['devisId']) && !isset($_GET['devisId'])) {
    die('ID de devis non défini.');
}

// Prioriser l'ID du devis reçu via $_GET
if (isset($_GET['devisId'])) {
    $devisId = $_GET['devisId'];
    $_SESSION['devisId'] = $devisId;
} else {
    $devisId = $_SESSION['devisId'];
}

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

// Masquer le devis via la classe
$devisModel->masquerDevis($devisId);

unset($_SESSION['devisId']);

header('Location: ../liste_devis.php');
exit();
