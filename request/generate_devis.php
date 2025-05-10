<?php
session_start();
require_once '../model/Database.php';
require_once '../model/Devis.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    // Récupération des données du formulaire
    $data = [
        'numero_devis'     => $_POST['numeroDevis'] ?? '',
        'delai_livraison'  => $_POST['delaiLivraison'] ?? '',
        'date_emission'    => $_POST['dateEmission'] ?? '',
        'date_expiration'  => $_POST['dateExpiration'] ?? '',
        'emis_par'         => $_POST['emisPar'] ?? '',
        'destine_a'        => $_POST['destineA'] ?? '',
        'termes_conditions' => $_POST['termesConditions'] ?? '',
        'pied_de_page'     => $_POST['piedDePage'] ?? '',
        'total_ht'         => $_POST['totalHT'] ?? '0',
        'total_ttc'        => $_POST['totalTTC'] ?? '0',
        'logo'             => '', // à gérer ci-dessous
        'client_id'        => $_POST['client_id'] ?? null,
        'offre_id'         => $_POST['offre_id'] ?? null,
        'tva_facturable'   => $_POST['tvaFacturable'] ?? '0',
        'publier_devis'    => $_POST['publierDevis'] ?? '0',
        'tva'              => $_POST['tva'] ?? '0',
        'correspondant'    => $_POST['correspondant'] ?? '',
    ];

    // Gestion du logo
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['logo']['tmp_name'];
        $fileName = $_FILES['logo']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = 'logo_' . time() . '.' . $fileExtension;
        $uploadFileDir = '../logo/';
        $dest_path = $uploadFileDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $data['logo'] = $newFileName;
        }
    }
    // Si aucun logo uploadé, logo par défaut
    if (empty($data['logo'])) {
        $data['logo'] = 'default_logo.jpg';
    }

    // Préparation des lignes de devis (nouveau format : JSON envoyé par AJAX)
    $lignes = [];
    if (isset($_POST['lignes'])) {
        $lignes = json_decode($_POST['lignes'], true);
        // On ne garde que les champs nécessaires : designation, prix, quantite, unite_id, total
        foreach ($lignes as &$ligne) {
            $ligne = [
                'designation' => $ligne['designation'],
                'prix'        => $ligne['prix'],
                'quantite'    => $ligne['quantite'],
                'unite_id'    => $ligne['unite_id'],
                'total'       => $ligne['total'],
                'groupe'      => isset($ligne['groupe']) ? $ligne['groupe'] : null,
            ];
        }
        unset($ligne);
    }

    error_log(print_r($lignes, true)); // Pour déboguer les lignes de devis


    // Création du devis via la classe
    $devisId = $devisModel->creerDevis($data, $lignes);

    $_SESSION['devisId'] = $devisId;
    echo "<h1>Devis enregistré avec succès</h1>";
}
