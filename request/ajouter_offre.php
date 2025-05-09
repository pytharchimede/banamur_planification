<?php
require_once '../model/Database.php';
require_once '../model/Offre.php';

$pdo = Database::getConnection();
$offreModel = new Offre($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'num_offre' => $_POST['num_offre'],
        'date_offre' => $_POST['date_offre'],
        'reference_offre' => $_POST['reference_offre'],
        'commercial_dedie' => $_POST['commercial_dedie'],
        'date_creat_offre' => $_POST['date_creat_offre'],
    ];

    // Ajout de l'offre et récupération de l'ID
    $offreModel->ajouterOffre($data);
    $offreId = $pdo->lastInsertId();

    // Gestion des fichiers joints via la classe Offre
    if (!empty($_FILES['offre_files']['name'][0])) {
        $offreModel->ajouterFichiersOffre($offreId, $_FILES['offre_files']);
    }

    header('Location: ../liste_offre.php');
    exit();
}
