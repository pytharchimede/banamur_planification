<?php
include 'auth_check.php';
include 'header/header_liste_devis.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Devis - BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom_style_liste_devis.css">
</head>

<body>
    <!-- Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="https://fidest.ci/logo/new_logo_banamur.jpg" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php include 'menu.php'; ?>
            </div>
        </div>
    </nav>

    <div class="container">

        <h1 class="text-center mb-4">Liste des Devis (<?php echo $nb_devis; ?>)</h1>

        <!-- Formulaire de recherche -->
        <form method="GET" action="liste_devis.php" class="row g-3 mb-4">
            <div class="col-md-3">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" id="date_debut" name="date_debut" class="form-control" value="<?= htmlspecialchars($_GET['date_debut'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" id="date_fin" name="date_fin" class="form-control" value="<?= htmlspecialchars($_GET['date_fin'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="emis_par" class="form-label">Émis par</label>
                <input type="text" id="emis_par" name="emis_par" class="form-control" placeholder="Nom de l'émetteur" value="<?= htmlspecialchars($_GET['emis_par'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label for="destine_a" class="form-label">Destiné à</label>
                <input type="text" id="destine_a" name="destine_a" class="form-control" placeholder="Nom du destinataire" value="<?= htmlspecialchars($_GET['destine_a'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary mt-4">Rechercher</button>
            </div>
        </form>

        <!-- Display total amount -->
        <div class="mt-4">
            <h4 class="text-end">Montant Total TTC: <span class="text-success"><?php echo number_format($total_ttc, 0, ',', ' '); ?> FCFA</span></h4>
        </div>

        <!-- Button to export filtered quotes in PDF -->
        <div class="text-end mt-3">
            <a target="_blank" href="request/export_resultat.php?<?php echo http_build_query($_GET); ?>" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </a>
            <a target="_blank" href="generer_devis.php" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Ajouter un devis
            </a>
        </div>

        <!-- Grid displaying quotes -->
        <div class="card-grid mt-4">
            <?php foreach ($devis as $de) : ?>
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-invoice"></i> <?= htmlspecialchars($de['numero_devis']) ?>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <p><strong>Délai Livraison:</strong> <?= htmlspecialchars($de['delai_livraison']) ?></p>
                            <p><strong>Date Émission:</strong> <?= htmlspecialchars($de['date_emission']) ?></p>
                            <p><strong>Date Expiration:</strong> <?= htmlspecialchars($de['date_expiration']) ?></p>
                            <p><strong>Émis Par:</strong> <?= htmlspecialchars($de['emis_par']) ?></p>
                            <p><strong>Destiné À:</strong> <?= htmlspecialchars($de['destine_a']) ?></p>
                            <p><strong>Total HT:</strong> <?= htmlspecialchars($de['total_ht']) ?> FCFA</p>
                            <p><strong>Total TTC:</strong> <?= htmlspecialchars($de['total_ttc']) ?> FCFA</p>
                            <p><strong>Date de Création:</strong> <?= htmlspecialchars($de['created_at']) ?></p>
                        </div>
                    </div>
                    <div class="card-footer d-flex flex-wrap gap-2 justify-content-between align-items-center">
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-primary btn-sm" target="_blank" title="Visualiser le devis détaillé"
                                href="request/export_pdf.php?devisId=<?= $de['id'] ?>">
                                <i class="fas fa-file-alt"></i> Détaillé
                            </a>
                            <a class="btn btn-outline-info btn-sm" target="_blank" title="Visualiser le devis groupé"
                                href="request/export_pdf_groupe.php?devisId=<?= $de['id'] ?>">
                                <i class="fas fa-layer-group"></i> Groupé
                            </a>
                        </div>
                        <div class="d-flex gap-2">
                            <a class="btn btn-outline-secondary btn-sm" title="Masquer ce devis"
                                href="request/masquer_devis.php?devisId=<?= $de['id'] ?>">
                                <i class="fas fa-eye-slash"></i>
                            </a>
                            <a class="btn btn-outline-warning btn-sm" title="Modifier ce devis"
                                href="modifier_devis.php?devisId=<?= $de['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                    <div class="footer-validation">
                        <?php if (!$de['validation_technique']) : ?>
                            <a class="btn-validate commerciale" href="request/valider_commerciale.php?devisId=<?= $de['id'] ?>"><i class="fas fa-check-circle"></i> Valider Commerciale</a>
                        <?php elseif (!$de['validation_generale']) : ?>
                            <a class="btn-validate generale" href="request/valider_generale.php?devisId=<?= $de['id'] ?>"><i class="fas fa-check-circle"></i> Valider Générale</a>
                        <?php else : ?>
                            <span class="validated"><i class="fas fa-check-double"></i> Déjà Validé</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-white text-center py-3">
        <div class="container">
            <p>&copy; <?php echo gmdate('Y'); ?> BANAMUR INDUSTRIES & TECH. Tous droits réservés.</p>
            <div class="social-icons">
                <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin-in"></a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>