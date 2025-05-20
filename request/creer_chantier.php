<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../model/Database.php';
require_once '../model/Chantier.php';
require_once '../model/Operation.php';
require_once '../model/Designation.php';
require_once '../model/Precision.php';
require_once '../model\Devis.php';

header('Content-Type: application/json');

try {
    $pdo = (new Database())->getConnection();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Création du chantier
    $titre = $_POST['titre'] ?? '';
    $code = $_POST['code'] ?? '';
    $devisId = $_POST['devis_id'] ?? 0;
    $clientId = $_POST['client_id'] ?? 0;

    if (!$titre || !$code || !$devisId || !$clientId) {
        throw new Exception("Données manquantes. POST=" . json_encode($_POST));
    }

    $chantierModel = new Chantier($pdo);
    $chantierId = $chantierModel->add([
        'titre' => $titre,
        'code' => $code,
        'devis_id' => $devisId,
        'client_id' => $clientId
    ]);

    // 2. Intégration des opérations
    $operationModel = new Operation($pdo);
    $integrationOperations = $operationModel->integrateFromDevis($devisId, $chantierId);
    if ($integrationOperations === false) {
        throw new Exception("Erreur lors de l'intégration des opérations.");
    }

    // 3. Intégration des désignations (débourses)
    $designationModel = new Designation($pdo);
    $integrationDesignations = $designationModel->integrateFromDevis($devisId, $chantierId);
    if ($integrationDesignations === false) {
        throw new Exception("Erreur lors de l'intégration des désignations.");
    }

    // 4. Intégration des précisions (lignes de déboursé)
    $precisionModel = new PrecisionFiche($pdo);
    $integrationPrecisions = $precisionModel->integrateFromDevis($devisId, $chantierId);
    if ($integrationPrecisions === false) {
        throw new Exception("Erreur lors de l'intégration des précisions.");
    }

    echo json_encode(['success' => true, 'chantierId' => $chantierId]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'type' => 'PDOException',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'post' => $_POST
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'type' => 'Exception',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
        'post' => $_POST
    ]);
}
