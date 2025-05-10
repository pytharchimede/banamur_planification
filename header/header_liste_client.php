<?php
include 'auth_check.php';
require_once 'model/Database.php';
require_once 'model/Client.php';

$pdo = Database::getConnection();
$clientModel = new Client($pdo);
$clients = $clientModel->getAllClients();


$page = "liste_client";
