<?php
class Offre
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function getAllOffres()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM offre_banamur');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterOffre($data)
    {
        $sql = "INSERT INTO offre_banamur (num_offre, date_offre, reference_offre, commercial_dedie, date_creat_offre)
            VALUES (:num_offre, :date_offre, :reference_offre, :commercial_dedie, :date_creat_offre)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function getOffreById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM offre_banamur WHERE id_offre = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
