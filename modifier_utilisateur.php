<?php
include 'header/header_modifier_utilisateur.php';
$page = "liste_utilisateur";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/custom_style_modifier_utilisateur.css">
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
        <h1>Modifier un Utilisateur</h1>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-edit"></i> Formulaire de modification
            </div>
            <div class="card-body">
                <form action="modifier_utilisateur.php?id=<?php echo $userId; ?>" method="POST">
                    <div class="mb-3">
                        <label for="mail_pro" class="form-label">Email professionnel</label>
                        <input type="email" class="form-control" id="mail_pro" name="mail_pro" value="<?php echo $user['mail_pro']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Laissez vide pour ne pas modifier">
                    </div>
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $user['nom']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo $user['prenom']; ?>" required>
                    </div>

                    <!-- Interrupteurs pour les droits -->
                    <div class="mb-3">
                        <label class="form-label">Droits d'accès</label>
                        <div class="form-switch-container">
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="modifier_devis" name="modifier_devis" <?php echo $user['modifier_devis'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="modifier_devis">Modifier les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="visualiser_devis" name="visualiser_devis" <?php echo $user['visualiser_devis'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="visualiser_devis">Visualiser les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="soumettre_devis" name="soumettre_devis" <?php echo $user['soumettre_devis'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="soumettre_devis">Soumettre les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="masquer_devis" name="masquer_devis" <?php echo $user['masquer_devis'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="masquer_devis">Masquer les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="envoyer_devis" name="envoyer_devis" <?php echo $user['envoyer_devis'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="envoyer_devis">Envoyer les devis</label>
                            </div>
                            <div class="form-switch">
                                <input class="form-check-input" type="checkbox" id="valider_devis" name="valider_devis" <?php echo $user['valider_devis'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="valider_devis">Valider les devis</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        <p>&copy; 2024 Gestion des Utilisateurs. Tous droits réservés.</p>
    </div>

    <!-- Scripts JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>