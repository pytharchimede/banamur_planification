<?php
session_start();
require_once '../model/Database.php';
require_once '../model/Devis.php';

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Récupération de l'ID du devis à modifier
    $devisId = $_POST['devisId'] ?? null;
    if (!$devisId) {
        die("ID du devis manquant.");
    }

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
    // Si aucun logo uploadé, garder l'ancien ou mettre par défaut
    if (empty($data['logo'])) {
        $ancienDevis = $devisModel->getDevisById($devisId);
        $data['logo'] = $ancienDevis['logo_client'] ?? 'default_logo.jpg';
    }

    // Préparation des lignes de devis (JSON envoyé par AJAX)
    $lignes = [];
    if (isset($_POST['lignes'])) {
        $lignes = json_decode($_POST['lignes'], true);
        foreach ($lignes as &$ligne) {
            $ligne = [
                'designation'   => $ligne['designation'],
                'prix_unitaire' => $ligne['prix_unitaire'],
                'quantite'      => $ligne['quantite'],
                'unite_id'      => $ligne['unite_id'],
                'prix_total'    => $ligne['prix_total'],
                'groupe'        => isset($ligne['groupe']) ? $ligne['groupe'] : null,
            ];
        }
        unset($ligne);
    }

    // Mise à jour du devis via la classe
    $success = $devisModel->updateDevis($devisId, $data, $lignes);

    $_SESSION['devisId'] = $devisId;
    if ($success) {
        echo "<h1>Devis modifié avec succès</h1>";
    } else {
        echo "<h1>Erreur lors de la modification du devis</h1>";
    }
}
