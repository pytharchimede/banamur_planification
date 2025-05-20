<?php
class PrecisionFiche
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Intègre toutes les lignes de déboursé du devis dans precision_fiche_banamur
     */
    public function integrateFromDevis($devisId, $chantierId)
    {
        // On récupère tous les déboursés du devis
        $stmtDebourse = $this->pdo->prepare("SELECT id FROM debourse_banamur WHERE devis_id = ?");
        $stmtDebourse->execute([$devisId]);
        $debourses = $stmtDebourse->fetchAll(PDO::FETCH_ASSOC);

        foreach ($debourses as $debourse) {
            $debourseId = $debourse['id'];
            // On récupère toutes les lignes de déboursé pour ce déboursé
            $stmtLignes = $this->pdo->prepare("SELECT * FROM ligne_debourse_banamur WHERE debourse_id = ?");
            $stmtLignes->execute([$debourseId]);
            while ($ligne = $stmtLignes->fetch(PDO::FETCH_ASSOC)) {
                $this->pdo->prepare("INSERT INTO precision_fiche_banamur (chantier_id, designation_id, libelle, montant)
                    VALUES (?, ?, ?, ?)")
                    ->execute([
                        $chantierId,
                        $debourseId, // correspond à designation_id (le déboursé parent)
                        $ligne['libelle'],
                        $ligne['montant']
                    ]);
            }
        }
    }

    public function getByChantier($chantierId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM precision_fiche_banamur WHERE chantier_id = ?");
        $stmt->execute([$chantierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les lignes de déboursé liées à un devis
     * @param int $devisId
     * @return array
     */
    public function getByDevis($devisId)
    {
        // Récupère toutes les lignes de déboursé liées aux déboursés de ce devis
        $stmt = $this->pdo->prepare("
        SELECT p.*, d.ligne_devis_id, l.designation AS designation_ligne
        FROM debourse_banamur d
        JOIN ligne_debourse_banamur p ON p.debourse_id = d.id
        JOIN ligne_devis_banamur l ON l.id = d.ligne_devis_id
        WHERE d.devis_id = ?
    ");
        $stmt->execute([$devisId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
