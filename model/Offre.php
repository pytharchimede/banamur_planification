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

    public function ajouterFichiersOffre($offreId, $files)
    {
        $uploadDir = '../uploads/offres/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($files['tmp_name'] as $key => $tmpName) {
            $fileName = basename($files['name'][$key]);
            $targetFile = $uploadDir . uniqid() . '_' . $fileName;
            if (move_uploaded_file($tmpName, $targetFile)) {
                $stmt = $this->pdo->prepare("INSERT INTO offre_fichiers_banamur (offre_id, file_path, file_name) VALUES (:offre_id, :file_path, :file_name)");
                $stmt->execute([
                    'offre_id' => $offreId,
                    'file_path' => $targetFile,
                    'file_name' => $fileName
                ]);
            }
        }
    }

    public function getFichiersByOffre($offreId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM offre_fichiers_banamur WHERE offre_id = :offre_id");
        $stmt->execute(['offre_id' => $offreId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
