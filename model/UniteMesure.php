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

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM unites_mesure WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBySymbole($symbole)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM unites_mesure WHERE symbole = :symbole");
        $stmt->execute(['symbole' => $symbole]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $symbole, $libelle)
    {
        $stmt = $this->pdo->prepare("UPDATE unites_mesure SET symbole = :symbole, libelle = :libelle WHERE id = :id");
        return $stmt->execute(['id' => $id, 'symbole' => $symbole, 'libelle' => $libelle]);
    }
}
