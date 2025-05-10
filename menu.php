<ul class="navbar-nav ms-auto">
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "dashboard") ? "active" : ""; ?>" href="index.php">Accueil</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "generer_devis") ? "active" : ""; ?>" href="generer_devis.php">Générer un devis</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_devis") ? "active" : ""; ?>" href="liste_devis.php">Devis</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_facture") ? "active" : ""; ?>" href="liste_facture.php">Factures</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_client") ? "active" : ""; ?>" href="liste_client.php">Clients</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_offre") ? "active" : ""; ?>" href="liste_offre.php">Offres</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_unite") ? "active" : ""; ?>" href="liste_unite.php">Unités</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_debourse") ? "active" : ""; ?>" href="liste_debourse.php">Déboursés</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "liste_utilisateur") ? "active" : ""; ?>" href="liste_utilisateur.php">Utilisateurs</a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?php echo ($page == "profil") ? "active" : ""; ?>" href="profil.php">Profil</a>
    </li>
    <li class="nav-item">
        <a href="deconnex.php" class="btn btn-danger">Déconnexion</a>
    </li>
</ul>