<?php
// Inclure les fichiers nécessaires
require_once 'header/header_add_user.php';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="css/custom_style_add_user.css" rel="stylesheet">
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
        <h1>Ajouter un Utilisateur</h1>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-plus"></i> Formulaire d'ajout
            </div>
            <div class="card-body">
                <form action="ajouter_utilisateur.php" method="POST">
                    <div class="mb-3">
                        <label for="mail_pro" class="form-label">Email professionnel</label>
                        <input type="email" class="form-control" id="mail_pro" name="mail_pro" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>

                    <!-- Interrupteurs pour les droits -->
                    <div class="mb-3">
                        <label class="form-label">Droits d'accès</label>
                        <div class="form-switch-container">
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="modifier_devis" name="modifier_devis">
                                <label class="form-check-label" for="modifier_devis">Modifier les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="visualiser_devis" name="visualiser_devis">
                                <label class="form-check-label" for="visualiser_devis">Visualiser les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="soumettre_devis" name="soumettre_devis">
                                <label class="form-check-label" for="soumettre_devis">Soumettre les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="masquer_devis" name="masquer_devis">
                                <label class="form-check-label" for="masquer_devis">Masquer les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="envoyer_devis" name="envoyer_devis">
                                <label class="form-check-label" for="envoyer_devis">Envoyer les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="valider_devis" name="valider_devis">
                                <label class="form-check-label" for="valider_devis">Valider les devis</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Ajouter l'utilisateur
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="row" style="height:100px;"></div>

    <!-- Pied de page -->
    <div class="footer">
        <p>&copy; 2024 Gestion des Utilisateurs. Tous droits réservés.</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>