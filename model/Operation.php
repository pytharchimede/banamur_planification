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
            $this->pdo->prepare("INSERT INTO operation_banamur (chantier_id, ligne_devis_id, designation, quantite, unite_id, prix, total)
                VALUES (?, ?, ?, ?, ?, ?, ?)")
                ->execute([
                    $chantierId,
                    $ligne['id'], // <-- correspond à ligne_devis_id
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

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM operation_banamur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOperationIdByChantierAndLigneDevis($chantierId, $ligneDevisId)
    {
        $stmt = $this->pdo->prepare("SELECT id FROM operation_banamur WHERE chantier_id = ? AND ligne_devis_id = ?");
        $stmt->execute([$chantierId, $ligneDevisId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id'] : null;
    }
}
