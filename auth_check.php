<?php
require_once 'model/Auth.php';

if (!Auth::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Vous pouvez également ajouter des contrôles de permissions ici si nécessaire
// Exemple: vérifiez si l'utilisateur a le droit de visualiser une page spécifique
// if (!$user->hasPermission($_SESSION['user_id'], 'view_page')) {
//     die("Accès refusé.");
// }
