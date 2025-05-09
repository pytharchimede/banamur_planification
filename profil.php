<?php
include 'header/header_profil.php';

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/custom_style_profil.css">
</head>

<body>
    <!-- Menu de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Mon Profil</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php include 'menu.php'; ?>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-circle"></i> Gérer mon Profil
            </div>
            <div class="card-body">
                <form action="profil.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4 text-center photo-preview">
                            <div class="photo-profile">
                                <!-- Affiche la photo actuelle ou Gravatar si pas de photo -->
                                <img id="previewImage" src="<?php echo $user['photo'] ?: 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($user['mail_pro']))) . '?d=mm&s=200'; ?>" alt="Photo de profil">
                            </div>
                            <div class="mt-3">
                                <input type="file" class="form-control-file" name="photo" accept="image/*" onchange="previewImage(event)">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Laissez vide pour ne pas modifier">
                            </div>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>&copy; 2024 Mon Application. Tous droits réservés.</p>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Aperçu de l'image sélectionnée
        function previewImage(event) {
            const preview = document.getElementById('previewImage');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.onload = () => URL.revokeObjectURL(preview.src); // Libérer la mémoire
        }
    </script>
</body>

</html>