<?php
class UniteMesure
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM unites_mesure ORDER BY libelle ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($symbole, $libelle)
    {
        $stmt = $this->pdo->prepare("INSERT INTO unites_mesure (symbole, libelle) VALUES (:symbole, :libelle)");
        return $stmt->execute(['symbole' => $symbole, 'libelle' => $libelle]);
    }
}
