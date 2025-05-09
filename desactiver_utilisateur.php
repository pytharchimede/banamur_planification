<?php
require_once 'model/Database.php';
require_once 'model/User.php';

$pdo = Database::getConnection();
$userModel = new User($pdo);

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $userModel->desactiverUtilisateur($userId);

    // Redirection après la désactivation
    header('Location: liste_utilisateur.php');
    exit;
}
