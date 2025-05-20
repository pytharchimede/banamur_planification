<?php
class Designation
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function integrateFromDevis($devisId, $chantierId)
    {
        // Récupération des déboursés pour ce devis
        $debourseStmt = $this->pdo->prepare("
            SELECT debourse_banamur.*, ligne_devis_banamur.designation AS designation_ligne
            FROM debourse_banamur
            JOIN ligne_devis_banamur ON ligne_devis_banamur.id = debourse_banamur.ligne_devis_id
            WHERE debourse_banamur.devis_id = ?
        ");
        $debourseStmt->execute([$devisId]);
        $debourses = $debourseStmt->fetchAll(PDO::FETCH_ASSOC);

        // Charger le modèle Operation
        require_once __DIR__ . '/Operation.php';
        $operationModel = new Operation($this->pdo);

        foreach ($debourses as $ligne) {
            // Récupérer le vrai operation_id pour ce chantier et cette ligne_devis
            $operationId = $operationModel->getOperationIdByChantierAndLigneDevis($chantierId, $ligne['ligne_devis_id']);
            if (!$operationId) continue; // sécurité

            // Vérifier si la désignation existe déjà pour éviter les doublons (optionnel)
            $check = $this->pdo->prepare("SELECT id FROM designation_banamur WHERE chantier_id = ? AND operation_id = ?");
            $check->execute([$chantierId, $operationId]);
            if (!$check->fetch()) {
                $this->pdo->prepare("INSERT INTO designation_banamur (chantier_id, operation_id, designation, montant)
                    VALUES (?, ?, ?, ?)")
                    ->execute([
                        $chantierId,
                        $operationId,
                        $ligne['designation_ligne'],
                        $ligne['montant_debourse']
                    ]);
            }
        }
    }

    public function getByChantier($chantierId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM designation_banamur WHERE chantier_id = ?");
        $stmt->execute([$chantierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDesignationIdByChantierAndDebourse($chantierId, $debourseId)
    {
        // On récupère la ligne_devis_id du déboursé
        $stmt = $this->pdo->prepare("SELECT ligne_devis_id FROM debourse_banamur WHERE id = ?");
        $stmt->execute([$debourseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        $ligneDevisId = $row['ligne_devis_id'];

        // On récupère l'operation_id correspondant dans operation_banamur
        $stmt2 = $this->pdo->prepare("SELECT id FROM operation_banamur WHERE chantier_id = ? AND ligne_devis_id = ?");
        $stmt2->execute([$chantierId, $ligneDevisId]);
        $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        if (!$row2) return null;
        $operationId = $row2['id'];

        // On récupère la désignation correspondante
        $stmt3 = $this->pdo->prepare("SELECT id FROM designation_banamur WHERE chantier_id = ? AND operation_id = ?");
        $stmt3->execute([$chantierId, $operationId]);
        $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        return $row3 ? $row3['id'] : null;
    }
}
