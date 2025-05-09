<?php
// model/Devis.php
class Devis
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }



    // Validation générale (directeur général)
    public function validerGenerale($devisId)
    {
        $sql = "UPDATE devis_banamur SET validation_generale = 1 WHERE id = :devisId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':devisId', $devisId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Validation technique (directeur technique)
    public function validerTechnique($devisId)
    {
        $sql = "UPDATE devis_banamur SET validation_technique = 1 WHERE id = :devisId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':devisId', $devisId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Vérifie si la validation générale a été effectuée
    public function isValidGenerale($devisId)
    {
        $sql = "SELECT validation_generale FROM devis_banamur WHERE id = :devisId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':devisId', $devisId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['validation_generale']) && $result['validation_generale'] == 1;
    }

    // Vérifie si la validation technique a été effectuée
    public function isValidTechnique($devisId)
    {
        $sql = "SELECT validation_technique FROM devis_banamur WHERE id = :devisId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':devisId', $devisId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($result['validation_technique']) && $result['validation_technique'] == 1;
    }

    public function getAllDevis()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM devis_banamur');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getNextCode()
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as nb FROM devis_banamur');
        $stmt->execute();
        $nb_devis = $stmt->fetch(PDO::FETCH_ASSOC)['nb'];
        $index_actuel = $nb_devis + 1;
        return 'BAN-DEV-PAB-' . $index_actuel;
    }

    public function getDevisFiltres($filtres = [])
    {
        $sql = 'SELECT * FROM devis_banamur WHERE masque=0';
        $params = [];

        if (!empty($filtres['date_debut'])) {
            $sql .= ' AND date_emission >= :date_debut';
            $params['date_debut'] = $filtres['date_debut'];
        }
        if (!empty($filtres['date_fin'])) {
            $sql .= ' AND date_emission <= :date_fin';
            $params['date_fin'] = $filtres['date_fin'];
        }
        if (!empty($filtres['emis_par'])) {
            $sql .= ' AND emis_par LIKE :emis_par';
            $params['emis_par'] = '%' . $filtres['emis_par'] . '%';
        }
        if (!empty($filtres['destine_a'])) {
            $sql .= ' AND destine_a LIKE :destine_a';
            $params['destine_a'] = '%' . $filtres['destine_a'] . '%';
        }

        $sql .= ' ORDER BY id DESC';

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function creerDevis($data, $lignes)
    {
        $sql = "INSERT INTO devis_banamur (numero_devis, delai_livraison, date_emission, date_expiration, emis_par, destine_a, termes_conditions, pied_de_page, total_ht, total_ttc, logo, client_id, offre_id, tva_facturable, publier_devis, tva, correspondant)
                VALUES (:numero_devis, :delai_livraison, :date_emission, :date_expiration, :emis_par, :destine_a, :termes_conditions, :pied_de_page, :total_ht, :total_ttc, :logo, :client_id, :offre_id, :tva_facturable, :publier_devis, :tva, :correspondant)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        $devisId = $this->pdo->lastInsertId();

        // Enregistrer les lignes de devis
        foreach ($lignes as $ligne) {
            $sqlLigne = "INSERT INTO ligne_devis_banamur (devis_id, designation, prix, quantite, unite_id, total)
                         VALUES (:devis_id, :designation, :prix, :quantite, :unite_id, :total)";
            $stmtLigne = $this->pdo->prepare($sqlLigne);
            $stmtLigne->execute([
                'devis_id'   => $devisId,
                'designation' => $ligne['designation'],
                'prix'       => $ligne['prix'],
                'quantite'   => $ligne['quantite'],
                'unite_id'   => $ligne['unite_id'],
                'total'      => $ligne['total'],
            ]);
        }

        return $devisId;
    }

    public function getNombreDevisParJour()
    {
        $sql = "SELECT DATE(date_emission) AS date, COUNT(*) AS count
            FROM devis_banamur
            WHERE masque = 0
            GROUP BY DATE(date_emission)
            ORDER BY DATE(date_emission) ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function masquerDevis($id)
    {
        $sql = "UPDATE devis_banamur SET masque = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function modifierDevis($devisId, $data, $lignes)
    {
        $sql = "UPDATE devis_banamur SET emis_par = :emis_par, destine_a = :destine_a, delai_livraison = :delai_livraison, date_emission = :date_emission, date_expiration = :date_expiration, termes_conditions = :termes_conditions, pied_de_page = :pied_de_page, total_ht = :total_ht, total_ttc = :total_ttc, logo = :logo, client_id = :client_id, offre_id = :offre_id, tva_facturable = :tva_facturable, publier_devis = :publier_devis, tva = :tva, correspondant = :correspondant WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $devisId;
        $stmt->execute($data);

        // Supprimer les anciennes lignes
        $deleteStmt = $this->pdo->prepare("DELETE FROM ligne_devis_banamur WHERE devis_id = :devis_id");
        $deleteStmt->execute(['devis_id' => $devisId]);

        // Ajouter les nouvelles lignes
        foreach ($lignes as $ligne) {
            $sqlLigne = "INSERT INTO ligne_devis_banamur (devis_id, designation, prix, quantite, unite_id, total, groupe)
             VALUES (:devis_id, :designation, :prix, :quantite, :unite_id, :total, :groupe)";
            $stmtLigne = $this->pdo->prepare($sqlLigne);
            $stmtLigne->execute([
                'devis_id'   => $devisId,
                'designation' => $ligne['designation'],
                'prix'       => $ligne['prix'],
                'quantite'   => $ligne['quantite'],
                'unite_id'   => $ligne['unite_id'],
                'total'      => $ligne['total'],
                'groupe'     => $ligne['groupe'],
            ]);
        }
    }
    public function getDevisById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM devis_banamur WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLignesDevis($devisId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_devis_banamur WHERE devis_id = :devis_id");
        $stmt->execute(['devis_id' => $devisId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
