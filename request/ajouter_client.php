<?php
require_once '../model/Database.php';
require_once '../model/Client.php';

$pdo = Database::getConnection();
$clientModel = new Client($pdo);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = [
        'code_client' => $_POST['code_client'],
        'nom_client' => $_POST['nom_client'],
        'localisation_client' => $_POST['localisation_client'],
        'commune_client' => $_POST['commune_client'],
        'bp_client' => $_POST['bp_client'],
        'pays_client' => $_POST['pays_client'],
        'date_creat_client' => $_POST['date_creat_client'],
    ];

    $clientModel->ajouterClient($data);

    header('Location: ../liste_client.php');
    exit();
}
