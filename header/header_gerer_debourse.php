<?php

require_once 'model/Database.php';
require_once 'model/Devis.php';
require_once 'model/User.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);
$userModel = new User($pdo);


// Récupération du devis et de ses lignes
$devisId = intval($_GET['devisId'] ?? 0);
// À adapter selon ta structure :
$devis = $devisModel->getDevisById($devisId); // fonction à créer
$lignes = $devisModel->getLignesDevis($devisId); // fonction à créer
$utilisateurs = $userModel->getUtilisateurs(); // fonction à créer

// Pour chaque ligne, récupérer le déboursé existant (si déjà saisi)
$debourses = $devisModel->getDeboursesByDevis($devisId); // fonction à créer, indexé par ligne_devis_id

$page = "liste_debourse"; // Pour activer le menu