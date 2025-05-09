<?php
require_once 'model/Database.php';
require_once 'model/Offre.php';

$pdo = Database::getConnection();
$offreModel = new Offre($pdo);
$offers = $offreModel->getAllOffres();
