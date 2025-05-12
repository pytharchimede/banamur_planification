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
        'telephone_client' => $_POST['telephone_client'],
        'logo_client' => null,
        'date_creat_client' => $_POST['date_creat_client'],
    ];

    // Gestion upload logo
    if (!empty($_FILES['logo_client']['name'])) {
        $targetDir = "../uploads/logos_clients/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $ext = pathinfo($_FILES['logo_client']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('logo_') . '.' . $ext;
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES['logo_client']['tmp_name'], $targetFile)) {
            $data['logo_client'] = $fileName;
        }
    }

    $clientModel->ajouterClient($data);

    header('Location: ../liste_client.php');
    exit();
}
