<?php
// Inclure les fichiers nécessaires
require_once 'model/Database.php';
require_once 'model/User.php';

$pdo = Database::getConnection();
$userModel = new User($pdo);

// Récupérer la liste des utilisateurs
$stmt = $pdo->query("SELECT * FROM user_devis");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = "liste_utilisateur";
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Utilisateurs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/custom_style_liste_utilisateur.css">
</head>

<body>
    <!-- Menu de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Gestion des Utilisateurs</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php include 'menu.php'; ?>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container">
        <h1>Liste des Utilisateurs</h1>
        <a href="ajouter_utilisateur.php" class="btn btn-success my-3"> <i class="fa fa-plus"></i> Ajouter un nouvel utilisateur</a>

        <div class="row" style="height:60px;">&nbsp;</div>

        <!-- Card Deck -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($users as $user): ?>
                <div class="col">
                    <div class="card">
                        <div class="photo-profile">
                            <img src="<?php echo $user['photo'] ?: 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user['mail_pro']))) . '?d=mm&s=200'; ?>" alt="Photo de profil">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($user['mail_pro']); ?></p>
                            <p class="card-text">
                                <strong>Status:</strong> <?php echo $user['active'] ? 'Actif' : 'Désactivé'; ?>
                            </p>
                        </div>
                        <div class="card-footer text-center">
                            <div class="actions">
                                <a href="modifier_utilisateur.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Modifier</a>
                                <a href="supprimer_utilisateur.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Supprimer</a>
                                <?php if ($user['active']): ?>
                                    <a href="desactiver_utilisateur.php?id=<?php echo $user['id']; ?>" class="btn btn-secondary btn-sm"><i class="fa fa-ban"></i> Désactiver</a>
                                <?php else: ?>
                                    <a href="reactiver_utilisateur.php?id=<?php echo $user['id']; ?>" class="btn btn-success btn-sm"><i class="fa fa-check"></i> Réactiver</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>&copy; 2024 Gestion des Utilisateurs | Tous droits réservés</p>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>