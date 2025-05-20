<?php
class Operation
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function integrateFromDevis($devisId, $chantierId)
    {
        // À adapter selon ta structure de lignes de devis
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_devis_banamur WHERE devis_id = ?");
        $stmt->execute([$devisId]);
        while ($ligne = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->pdo->prepare("INSERT INTO operation_banamur (chantier_id, designation, quantite, unite_id, prix, total)
                VALUES (?, ?, ?, ?, ?, ?)")
                ->execute([
                    $chantierId,
                    $ligne['designation'],
                    $ligne['quantite'],
                    $ligne['unite_id'],
                    $ligne['prix'],
                    $ligne['total']
                ]);
        }
    }

    public function getByChantier($chantierId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM operation_banamur WHERE chantier_id = ?");
        $stmt->execute([$chantierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
