<?php
require_once 'model/Database.php';
require_once 'model/User.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pdo = Database::getConnection();
$userModel = new User($pdo);

$userId = $_SESSION['user_id'];

// Récupérer les données de l'utilisateur via la classe
$user = $userModel->getUserById($userId);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}

// Traitement du formulaire de mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $password = $_POST['password'];
    $hashedPassword = !empty($password) ? hash("sha512", $password) : $user['password'];

    // Traitement de la photo de profil
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo = $_FILES['photo'];
        $targetDir = "photo/";
        $targetFile = $targetDir . basename($photo['name']);
        move_uploaded_file($photo['tmp_name'], $targetFile);
    } else {
        $targetFile = $user['photo'] ?: '';
    }

    // Mise à jour via la classe
    $userModel->modifierUtilisateur($userId, [
        'nom' => $nom,
        'prenom' => $prenom,
        'password' => $hashedPassword,
        'photo' => $targetFile
    ]);

    header('Location: profil.php');
    exit;
}
