<?php
class Chantier
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getLastIndex()
    {
        $stmt = $this->pdo->query("SELECT MAX(id) as max_id FROM chantier_banamur");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? intval($row['max_id']) : 0;
    }

    public function add($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO chantier_banamur (titre, code, devis_id, client_id) VALUES (:titre, :code, :devis_id, :client_id)");
        $stmt->execute([
            'titre' => $data['titre'],
            'code' => $data['code'],
            'devis_id' => $data['devis_id'],
            'client_id' => $data['client_id']
        ]);
        return $this->pdo->lastInsertId();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM chantier_banamur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
