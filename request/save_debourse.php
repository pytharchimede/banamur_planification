<?php
header('Content-Type: application/json');
session_start();
require_once '../model/Database.php';
require_once '../model/Devis.php'; // adapte le chemin si besoin

// Vérification de la session utilisateur
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée.']);
    exit;
}

// Vérification de la méthode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
    exit;
}

// Initialisation de la connexion à la base de données
$pdo = Database::getConnection();




try {
    $devisId = intval($_POST['devis_id'] ?? 0);
    if (!$devisId) throw new Exception("Devis invalide.");

    $devisModel = new Devis($pdo); // $pdo doit être défini ou inclus

    // Parcours des lignes de devis
    foreach ($_POST['montant_debourse'] as $ligneId => $montant) {
        $responsable = $_POST['responsable_id'][$ligneId] ?? null;
        $date_debut = $_POST['date_debut'][$ligneId] ?? null;
        $date_fin = $_POST['date_fin'][$ligneId] ?? null;

        // Sauvegarde ou update du déboursé principal
        $devisModel->saveDebourseLigne($devisId, $ligneId, $montant, $responsable, $date_debut, $date_fin);

        // Suppression puis ajout des sous-lignes (simple, à améliorer si besoin)
        $devisModel->deleteSousLignesDebourse($ligneId);

        if (!empty($_POST['categorie'][$ligneId])) {
            foreach ($_POST['categorie'][$ligneId] as $k => $categorie) {
                $designation = $_POST['designation'][$ligneId][$k] ?? '';
                $montant_sous = $_POST['montant_sous_ligne'][$ligneId][$k] ?? 0;
                $date_debut_sous = $_POST['date_debut_sous_ligne'][$ligneId][$k] ?? null;
                $date_fin_sous = $_POST['date_fin_sous_ligne'][$ligneId][$k] ?? null;
                $devisModel->addSousLigneDebourse($ligneId, $categorie, $designation, $montant_sous, $date_debut_sous, $date_fin_sous);
            }
        }
    }

    echo json_encode(['success' => true, 'message' => 'Déboursé enregistré avec succès.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
