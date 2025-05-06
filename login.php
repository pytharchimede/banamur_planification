<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Logiciel de Devis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style_login.css">
    <link rel="stylesheet" href="css/custom_style_login.css">
</head>

<body>
    <div class="login-container">
        <!-- Logo -->
        <img src="https://fidest.ci/logo/new_logo_banamur.jpg" alt="Logo">

        <!-- Title -->
        <h2>Connexion à votre Espace</h2>

        <!-- Réponse du server -->
        <div class="message" style="display: none;"></div>
        <div class="spinner" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i>
        </div>

        <!-- Login Form -->
        <form id="login-form" action="javascript:void(0);">
            <input type="text" name="mail_pro" placeholder="Adresse e-mail professionnelle" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Se Connecter</button>
        </form>

        <!-- Forgot Password Link -->
        <a href="reset_password.php" class="forgot-password">Mot de passe oublié ?</a>

        <!-- Footer -->
        <div class="login-footer">© 2024 Logiciel de Devis - BANAMUR INDUSTRIES & TECH</div>
    </div>
    <script src="js/login.js"></script>
</body>

</html>