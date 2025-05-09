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

    $offreModel->ajouterOffre($data);

    header('Location: ../liste_offre.php');
    exit();
}
