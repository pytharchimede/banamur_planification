<nav class="navbar navbar-expand-lg mb-4" style="background:#111;">
    <div class="container">
        <button class="navbar-toggler text-warning" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="filter: invert(80%) sepia(100%) saturate(500%) hue-rotate(10deg);"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?php echo ($page == "dashboard") ? "active" : ""; ?>" href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == "generer_devis") ? "active" : ""; ?>" href="generer_devis.php"><i class="fas fa-file-signature"></i> Générer un devis</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == "liste_devis") ? "active" : ""; ?>" href="liste_devis.php"><i class="fas fa-file-contract"></i> Devis</a></li>
                <!-- <li class="nav-item"><a class="nav-link <?php //echo ($page == "liste_facture") ? "active" : ""; 
                                                                ?>" href="liste_facture.php"><i class="fas fa-file-invoice"></i> Factures</a></li> -->
                <li class="nav-item"><a class="nav-link <?php echo ($page == "liste_client") ? "active" : ""; ?>" href="liste_client.php"><i class="fas fa-users"></i> Clients</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == "liste_offre") ? "active" : ""; ?>" href="liste_offre.php"><i class="fas fa-gift"></i> Offres</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == "liste_unite") ? "active" : ""; ?>" href="liste_unite.php"><i class="fas fa-balance-scale"></i> Unités</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == "liste_debourse") ? "active" : ""; ?>" href="liste_debourse.php"><i class="fas fa-coins"></i> Déboursés</a></li>
                <!-- <li class="nav-item"><a class="nav-link <?php //echo ($page == "liste_utilisateur") ? "active" : ""; 
                                                                ?>" href="liste_utilisateur.php"><i class="fas fa-user-cog"></i> Utilisateurs</a></li> -->
                <li class="nav-item"><a class="nav-link <?php echo ($page == "liste_chantier") ? "active" : ""; ?>" href="liste_chantier.php"><i class="fas fa-warehouse"></i> Chantiers</a></li>
                <li class="nav-item"><a class="nav-link <?php echo ($page == "profil") ? "active" : ""; ?>" href="profil.php"><i class="fas fa-user"></i> Profil</a></li>
                <li class="nav-item ms-lg-2">
                    <a href="deconnex.php" class="btn btn-danger px-3"><i class="fas fa-sign-out-alt"></i> </a>
                </li>
            </ul>
        </div>
    </div>
</nav>