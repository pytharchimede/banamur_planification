<?php
// Inclure les fichiers nécessaires
require_once 'model/Database.php';
require_once 'model/User.php';

$pdo = Database::getConnection();
$userModel = new User($pdo);




// Vérifier si l'ID de l'utilisateur est fourni dans l'URL
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    // Récupérer les données de l'utilisateur via la classe
    $user = $userModel->getUserById($userId);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        echo "Utilisateur non trouvé.";
        exit;
    }
} else {
    echo "Aucun ID utilisateur spécifié.";
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_pro = $_POST['mail_pro'];
    $password = $_POST['password'];
    $hashedPassword = !empty($password) ? hash("sha512", $password) : $user['password'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $modifier_devis = isset($_POST['modifier_devis']) ? 1 : 0;
    $visualiser_devis = isset($_POST['visualiser_devis']) ? 1 : 0;
    $soumettre_devis = isset($_POST['soumettre_devis']) ? 1 : 0;
    $masquer_devis = isset($_POST['masquer_devis']) ? 1 : 0;
    $envoyer_devis = isset($_POST['envoyer_devis']) ? 1 : 0;
    $valider_devis = isset($_POST['valider_devis']) ? 1 : 0;

    $userModel->modifierUtilisateur($userId, [
        'mail_pro' => $mail_pro,
        'password' => $hashedPassword,
        'nom' => $nom,
        'prenom' => $prenom,
        'modifier_devis' => $modifier_devis,
        'visualiser_devis' => $visualiser_devis,
        'soumettre_devis' => $soumettre_devis,
        'masquer_devis' => $masquer_devis,
        'envoyer_devis' => $envoyer_devis,
        'valider_devis' => $valider_devis
    ]);

    // Redirection après modification
    header('Location: liste_utilisateur.php');
    exit;
}
