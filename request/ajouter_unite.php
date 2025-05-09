<?php
require_once '../model/Database.php';
require_once '../model/UniteMesure.php';

header('Content-Type: application/json');
$pdo = Database::getConnection();
$uniteModel = new UniteMesure($pdo);

if (!empty($_POST['symbole']) && !empty($_POST['libelle'])) {
    $symbole = trim($_POST['symbole']);
    $libelle = trim($_POST['libelle']);
    // Vérifie si le symbole existe déjà
    foreach ($uniteModel->getAll() as $u) {
        if (strtolower($u['symbole']) === strtolower($symbole)) {
            echo json_encode(['success' => false, 'error' => 'Ce symbole existe déjà.']);
            exit;
        }
    }
    $uniteModel->add($symbole, $libelle);
    echo json_encode(['success' => true, 'symbole' => $symbole, 'libelle' => $libelle]);
    exit;
}
echo json_encode(['success' => false, 'error' => 'Champs obligatoires manquants.']);
