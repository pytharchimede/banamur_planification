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
                'groupe'     => $ligne['groupe'], // <-- ajout ici
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

    public function updateDevis($devisId, $data, $lignes)
    {
        // Met à jour le devis principal
        $sql = "UPDATE devis_banamur SET 
                    numero_devis = :numero_devis,
                    delai_livraison = :delai_livraison,
                    date_emission = :date_emission,
                    date_expiration = :date_expiration,
                    emis_par = :emis_par,
                    destine_a = :destine_a,
                    termes_conditions = :termes_conditions,
                    pied_de_page = :pied_de_page,
                    total_ht = :total_ht,
                    total_ttc = :total_ttc,
                    logo = :logo,
                    client_id = :client_id,
                    offre_id = :offre_id,
                    tva_facturable = :tva_facturable,
                    publier_devis = :publier_devis,
                    tva = :tva,
                    correspondant = :correspondant
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $data['id'] = $devisId;
        $stmt->execute($data);

        // Supprime les anciennes lignes
        $deleteStmt = $this->pdo->prepare("DELETE FROM ligne_devis_banamur WHERE devis_id = :devis_id");
        $deleteStmt->execute(['devis_id' => $devisId]);

        // Ajoute les nouvelles lignes
        foreach ($lignes as $ligne) {
            $sqlLigne = "INSERT INTO ligne_devis_banamur (devis_id, designation, prix, quantite, unite_id, total, groupe)
                         VALUES (:devis_id, :designation, :prix, :quantite, :unite_id, :total, :groupe)";
            $stmtLigne = $this->pdo->prepare($sqlLigne);
            $stmtLigne->execute([
                'devis_id'    => $devisId,
                'designation' => $ligne['designation'],
                'prix'        => $ligne['prix_unitaire'],
                'quantite'    => $ligne['quantite'],
                'unite_id'    => $ligne['unite_id'],
                'total'       => $ligne['prix_total'],
                'groupe'      => $ligne['groupe'],
            ]);
        }
        return true;
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

    // Récupère les déboursés pour un devis, indexés par ligne_devis_id
    public function getDeboursesByDevis($devisId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM debourse_banamur WHERE devis_id = :devis_id");
        $stmt->execute(['devis_id' => $devisId]);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['ligne_devis_id']] = $row;
        }
        return $result;
    }

    // Récupère les sous-lignes de déboursé pour une ligne de devis
    public function getLignesDebourse($debourseId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_debourse_banamur WHERE debourse_id = :debourse_id");
        $stmt->execute(['debourse_id' => $debourseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveDebourseLigne($devisId, $ligneId, $montant, $responsable, $date_debut, $date_fin)
    {
        $sql = "REPLACE INTO debourse_banamur (devis_id, ligne_devis_id, montant_debourse, responsable_id, date_debut, date_fin)
                VALUES (:devis_id, :ligne_devis_id, :montant, :responsable, :date_debut, :date_fin)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'devis_id' => $devisId,
            'ligne_devis_id' => $ligneId,
            'montant' => $montant,
            'responsable' => $responsable,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
    }

    public function deleteSousLignesDebourse($ligneId)
    {
        $sql = "DELETE FROM ligne_debourse_banamur WHERE debourse_id = (SELECT id FROM debourse_banamur WHERE ligne_devis_id = :ligne_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['ligne_id' => $ligneId]);
    }

    public function addSousLigneDebourse($ligneId, $categorie, $designation, $montant, $date_debut, $date_fin)
    {
        // Récupérer l'id du déboursé principal
        $stmt = $this->pdo->prepare("SELECT id FROM debourse_banamur WHERE ligne_devis_id = :ligne_id");
        $stmt->execute(['ligne_id' => $ligneId]);
        $debourse = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$debourse) return;
        $debourse_id = $debourse['id'];

        $sql = "INSERT INTO ligne_debourse_banamur (debourse_id, categorie, designation, montant, date_debut, date_fin)
                VALUES (:debourse_id, :categorie, :designation, :montant, :date_debut, :date_fin)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'debourse_id' => $debourse_id,
            'categorie' => $categorie,
            'designation' => $designation,
            'montant' => $montant,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
    }

    /**
     * Ajoute une sous-ligne à un déboursé existant (par son id)
     */
    public function addSousLigneDebourseByDebourseId($debourseId, $categorie, $designation, $montant, $date_debut, $date_fin)
    {
        $sql = "INSERT INTO ligne_debourse_banamur (debourse_id, categorie, designation, montant, date_debut, date_fin)
                VALUES (:debourse_id, :categorie, :designation, :montant, :date_debut, :date_fin)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'debourse_id' => $debourseId,
            'categorie' => $categorie,
            'designation' => $designation,
            'montant' => $montant,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
    }

    public function debourseExistePourDevis($devisId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM debourse_banamur WHERE devis_id = :devis_id");
        $stmt->execute(['devis_id' => $devisId]);
        return $stmt->fetchColumn() > 0;
    }

    public function getSectionContent($devisId, $section)
    {
        $allowed = ['page_garde', 'description', 'delai', 'conditions', 'garantie'];
        if (!in_array($section, $allowed)) return '';
        $stmt = $this->pdo->prepare("SELECT `$section` FROM devis_banamur WHERE id = :id");
        $stmt->execute(['id' => $devisId]);
        return $stmt->fetchColumn();
    }

    public function saveSectionContent($devisId, $section, $contenu)
    {
        $allowed = ['page_garde', 'description', 'delai', 'conditions', 'garantie'];
        if (!in_array($section, $allowed)) return false;
        $stmt = $this->pdo->prepare("UPDATE devis_banamur SET `$section` = :contenu WHERE id = :id");
        return $stmt->execute(['contenu' => $contenu, 'id' => $devisId]);
    }

    // Met à jour une sous-ligne de déboursé
    public function updateLigneDebourse($id, $data)
    {
        $sql = "UPDATE ligne_debourse_banamur 
                SET designation = :designation, montant = :montant, date_debut = :date_debut, date_fin = :date_fin, categorie = :categorie
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'designation' => $data['designation'],
            'montant' => $data['montant'],
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'categorie' => $data['categorie'],
            'id' => $id
        ]);
    }

    // Récupère le déboursé parent d'une sous-ligne
    public function getDebourseByLigneDebourse($ligneDebourseId)
    {
        $sql = "SELECT d.* FROM debourse_banamur d
                JOIN ligne_debourse_banamur l ON l.debourse_id = d.id
                WHERE l.id = :ligne_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['ligne_id' => $ligneDebourseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Calcule le montant total et la période d'un déboursé
    public function getTotauxDebourse($debourseId)
    {
        $sql = "SELECT
                    SUM(montant) AS montant_debourse,
                    MIN(date_debut) AS date_debut,
                    MAX(date_fin) AS date_fin
                FROM ligne_debourse_banamur
                WHERE debourse_id = :debourse_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['debourse_id' => $debourseId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDebourseResume($debourseId)
    {
        $sql = "SELECT
                    MIN(date_debut) AS date_debut, 
                    MAX(date_fin) AS date_fin, 
                    SUM(montant) AS montant_debourse
                FROM ligne_debourse_banamur
                WHERE debourse_id = :debourse_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['debourse_id' => $debourseId]);
        $resume = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql2 = "UPDATE debourse_banamur 
                 SET date_debut = :date_debut, date_fin = :date_fin, montant_debourse = :montant_debourse
                 WHERE id = :debourse_id";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute([
            'date_debut' => $resume['date_debut'],
            'date_fin' => $resume['date_fin'],
            'montant_debourse' => $resume['montant_debourse'],
            'debourse_id' => $debourseId
        ]);
    }

    public function addDebourse($devisId, $date_debut, $date_fin)
    {
        $sql = "INSERT INTO debourse_banamur (devis_id, montant_debourse, date_debut, date_fin)
            VALUES (:devis_id, 0, :date_debut, :date_fin)";
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute([
            'devis_id' => $devisId,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ]);
        return $ok ? $this->pdo->lastInsertId() : false;
    }

    public function getDeboursesByDevisId($devisId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM debourse_banamur WHERE devis_id = :devis_id");
        $stmt->execute(['devis_id' => $devisId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLignesDebourseByDebourseId($debourseId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ligne_debourse_banamur WHERE debourse_id = :debourse_id");
        $stmt->execute(['debourse_id' => $debourseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalDebourseByDevisId($devisId)
    {
        $total = 0;
        $debourses = $this->getDeboursesByDevisId($devisId);
        foreach ($debourses as $debourse) {
            $sousLignes = $this->getLignesDebourseByDebourseId($debourse['id']);
            foreach ($sousLignes as $ligne) {
                $total += $ligne['montant'];
            }
        }
        return $total;
    }

    public function syncAllDebourseMontants($devisId)
    {
        $debourses = $this->getDeboursesByDevisId($devisId);
        foreach ($debourses as $debourse) {
            $this->updateDebourseResume($debourse['id']);
        }
    }
}
