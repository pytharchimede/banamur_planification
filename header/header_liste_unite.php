<?php
require_once 'model/Database.php';
require_once 'model/UniteMesure.php';

$pdo = Database::getConnection();
$uniteModel = new UniteMesure($pdo);

$unites = $uniteModel->getAll();


$page = "liste_unite";
