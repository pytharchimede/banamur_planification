<?php
class Client
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
    public function getAllClients()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM client_banamur');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterClient($data)
    {
        $sql = "INSERT INTO client_banamur (code_client, nom_client, localisation_client, commune_client, bp_client, pays_client, date_creat_client)
            VALUES (:code_client, :nom_client, :localisation_client, :commune_client, :bp_client, :pays_client, :date_creat_client)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }
    public function getClientById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM client_banamur WHERE id_client = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
