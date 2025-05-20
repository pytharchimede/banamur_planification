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
            SELECT d.*, l.designation AS designation_ligne
            FROM debourse_banamur d
            JOIN lignes_devis l ON l.id = d.ligne_devis_id
            WHERE d.devis_id = ?
        ");
        $debourseStmt->execute([$devisId]);
        $debourses = $debourseStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($debourses as $ligne) {
            // Vérifier si la désignation existe déjà pour éviter les doublons (optionnel)
            $check = $this->pdo->prepare("SELECT id FROM designation_banamur WHERE chantier_id = ? AND operation_id = ?");
            $check->execute([$chantierId, $ligne['ligne_devis_id']]);
            if (!$check->fetch()) {
                $this->pdo->prepare("INSERT INTO designation_banamur (chantier_id, operation_id, designation, montant)
                    VALUES (?, ?, ?, ?)")
                    ->execute([
                        $chantierId,
                        $ligne['ligne_devis_id'],
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
}
