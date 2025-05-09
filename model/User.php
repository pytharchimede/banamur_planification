<?php
// model/User.php
class User
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function ajouterUtilisateur($data)
    {
        $sql = "INSERT INTO user_devis (mail_pro, password, nom, prenom, modifier_devis, visualiser_devis, soumettre_devis, masquer_devis, envoyer_devis, valider_devis, active)
            VALUES (:mail_pro, :password, :nom, :prenom, :modifier_devis, :visualiser_devis, :soumettre_devis, :masquer_devis, :envoyer_devis, :valider_devis, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function findUserByEmail($mail_pro)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_devis_banamur WHERE mail_pro = :mail_pro AND active = 1 ");
        $stmt->execute(['mail_pro' => $mail_pro]);
        return $stmt->fetch();
    }

    public function verifyPassword($mail_pro, $password)
    {
        $user = $this->findUserByEmail($mail_pro);

        // Hachage du mot de passe en SHA-512 pour comparer avec la base de données
        $hashedPassword = hash('sha512', $password);

        // Comparaison du mot de passe haché avec celui stocké en base de données
        if ($user && $hashedPassword === $user['password']) {
            return $user;
        }
        return false;
    }


    // Vérifier les droits spécifiques de l'utilisateur
    public function hasPermission($user, $permission)
    {
        return isset($user[$permission]) && $user[$permission] == 1;
    }

    public function findDirecteurTechnique()
    {
        $stmt = $this->pdo->prepare("
        SELECT u.* 
        FROM user_devis_banamur u
        INNER JOIN role_devis_banamur r ON u.role_id = r.id_role_devis
        WHERE r.lib_role_devis = :libelle AND u.active = 1
    ");
        $stmt->execute(['libelle' => 'directeur technique']);
        return $stmt->fetch();
    }

    public function findDirecteurGeneral()
    {
        $stmt = $this->pdo->prepare("
        SELECT u.* 
        FROM user_devis_banamur u
        INNER JOIN role_devis_banamur r ON u.role_id = r.id_role_devis
        WHERE r.lib_role_devis = :libelle AND u.active = 1
    ");
        $stmt->execute(['libelle' => 'directeur general']);
        return $stmt->fetch();
    }

    public function desactiverUtilisateur($id)
    {
        $sql = "UPDATE user_devis SET active = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getUserById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_devis WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function modifierUtilisateur($id, $data)
    {
        $sql = "UPDATE user_devis SET mail_pro = :mail_pro, password = :password, nom = :nom, prenom = :prenom, modifier_devis = :modifier_devis, visualiser_devis = :visualiser_devis, soumettre_devis = :soumettre_devis, masquer_devis = :masquer_devis, envoyer_devis = :envoyer_devis, valider_devis = :valider_devis WHERE id = :id";
        $data['id'] = $id;
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($data);
    }

    public function reactiverUtilisateur($id)
    {
        $sql = "UPDATE user_devis SET active = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function supprimerUtilisateur($id)
    {
        $sql = "DELETE FROM user_devis WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
