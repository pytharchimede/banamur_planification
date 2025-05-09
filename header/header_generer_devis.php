<?php
include 'auth_check.php';
require_once 'model/Database.php';
require_once 'model/Devis.php';
require_once 'model/Client.php';
require_once 'model/Offre.php';

$pdo = Database::getConnection();

$devisModel = new Devis($pdo);
$clientModel = new Client($pdo);
$offreModel = new Offre($pdo);

$devis = $devisModel->getAllDevis();
$code_devis = $devisModel->getNextCode();
$clients = $clientModel->getAllClients();
$offres = $offreModel->getAllOffres();
