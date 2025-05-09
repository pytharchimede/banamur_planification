<?php
// Inclure les fichiers nécessaires
require_once 'model/Database.php';
require_once 'model/User.php';

$pdo = Database::getConnection();
$userModel = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_pro = $_POST['mail_pro'];
    $password = $_POST['password'];
    $hashedPassword = hash("sha512", $password);

    $data = [
        'mail_pro' => $mail_pro,
        'password' => $hashedPassword,
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'modifier_devis' => isset($_POST['modifier_devis']) ? 1 : 0,
        'visualiser_devis' => isset($_POST['visualiser_devis']) ? 1 : 0,
        'soumettre_devis' => isset($_POST['soumettre_devis']) ? 1 : 0,
        'masquer_devis' => isset($_POST['masquer_devis']) ? 1 : 0,
        'envoyer_devis' => isset($_POST['envoyer_devis']) ? 1 : 0,
        'valider_devis' => isset($_POST['valider_devis']) ? 1 : 0,
    ];

    $userModel->ajouterUtilisateur($data);

    header('Location: liste_utilisateur.php');
    exit;
}
