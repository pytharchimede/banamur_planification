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
}
