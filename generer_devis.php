<?php
include('header/header_generer_devis.php');

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rédaction de Devis - BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom_style_generer_devis.css">
</head>

<body>

    <!-- Menu -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img style="width:auto; height:50px;" src="https://fidest.ci/logo/new_logo_banamur.jpg" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <?php include 'menu.php'; ?>
            </div>
        </div>
    </nav>


    <div class="container mt-4">
        <h1 class="text-center">Rédiger un Devis</h1>


        <div class="row mb-3">
            <div class="col-md-6">
                <label for="clientSelect" class="form-label">Sélectionner le client</label>
                <select class="form-control" id="clientSelect" name="client_id">
                    <option value="" disabled selected>Choisissez un client</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?php echo $client['id_client']; ?>">
                            <?php echo $client['nom_client']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="offreSelect" class="form-label">Sélectionner l'offre</label>
                <select class="form-control" id="offreSelect" name="offre_id">
                    <option value="" disabled selected>Choisissez une offre</option>
                    <?php foreach ($offres as $offre): ?>
                        <option value="<?php echo $offre['id_offre']; ?>">
                            <?php echo $offre['num_offre'] . ' - ' . $offre['reference_offre']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label for="delaiLivraison" class="form-label">Délai de livraison</label>
                <input type="text" class="form-control" id="delaiLivraison" name="delaiLivraison" placeholder="Délai de livraison">
            </div>
            <div class="col-md-4">
                <label for="correspondant" class="form-label">Correspondant</label>
                <input type="text" class="form-control" id="correspondant" name="correspondant" placeholder="Correspondant">
            </div>
        </div>

        <div class="checkbox_zone">
            <div class="form-group">
                <label for="tvaFacturable">TVA Facturable</label>
                <label class="switch">
                    <input type="checkbox" id="tvaFacturable" name="tvaFacturable" value="1">
                    <span class="slider round"></span>
                </label>
            </div>

            <div class="form-group">
                <label for="publierDevis">Publier le devis</label>
                <label class="switch">
                    <input type="checkbox" id="publierDevis" name="publierDevis" value="1">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>

        <div class="d-flex align-items-start mb-4">

            <div id="logoUploadContainer" class="border rounded p-4" onclick="document.getElementById('logoUpload').click();">
                <img id="logoPreview" src="logo/default_logo.jpg" alt="Logo" class="img-fluid mb-2" style="max-height: 150px;">
                <input type="file" id="logoUpload" name="logoUpload" accept="image/*" style="display: none;">
                <p id="logoMessage" class="text-muted logo-message">Cliquer ou glisser votre logo</p>
            </div>

            <div class="spacer"></div> <!-- Spacer added here -->

            <div class="info-container ms-3">
                <div class="form-group">
                    <label for="numeroDevis" class="form-label">N° Devis</label>
                    <input type="text" class="form-control" id="numeroDevis" name="numeroDevis" placeholder="N° Devis" value="<?php echo $code_devis; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="dateEmission" class="form-label">Date d'émission</label>
                    <input type="date" class="form-control" id="dateEmission" name="dateEmission" value="<?php echo gmdate('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="dateExpiration" class="form-label">Date d'expiration</label>
                    <input type="date" class="form-control" id="dateExpiration" name="dateExpiration">
                </div>
            </div>
        </div>

        <form id="devisForm" method="POST" enctype="multipart/form-data">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="emisPar" class="form-label">Émetteur</label>
                    <textarea class="form-control" id="emisPar" name="emisPar" placeholder="Informations sur l'émetteur" rows="3"></textarea>
                </div>
                <div class="col-md-6">
                    <label for="destineA" class="form-label">Destinataire</label>
                    <textarea class="form-control" id="destineA" name="destineA" placeholder="Informations sur le destinataire" rows="3"></textarea>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="devisTable">
                    <thead>
                        <tr>
                            <th style="width: 50px;">N°</th>
                            <th>Désignation</th>
                            <th>Unité</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Prix Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Ajout des lignes du devis ici -->
                    </tbody>
                </table>
            </div>
            <!-- Boutons pour ajouter un groupe ou une ligne -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <button type="button" class="btn btn-info" id="addGroupBtn">
                    <i class="fas fa-layer-group"></i> Ajouter un groupe
                </button>
                <button type="button" class="btn btn-success" id="addRow">+ Ajouter une ligne</button>
            </div>
        </form>

        <!-- Additional Info Section -->
        <div class="row footer-info mt-4">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="termesConditions" class="form-label">Termes et conditions</label>
                    <textarea class="form-control" id="termesConditions" name="termesConditions" rows="5" placeholder="Termes et conditions"></textarea>
                </div>
                <div class="form-group mt-3">
                    <label for="piedDePage" class="form-label">Pied de page</label>
                    <textarea class="form-control" id="piedDePage" name="piedDePage" rows="5" placeholder="Pied de page"></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mt-3">
                    <label for="totalHT" class="form-label">Total HT</label>
                    <input type="text" class="form-control" id="totalHT" name="totalHT" readonly>
                </div>
                <div class="form-group mt-3 tvaZone">
                    <label for="tva" class="form-label">TVA 18%</label>
                    <input type="text" class="form-control" id="tva" name="tva" readonly>
                </div>
                <div class="form-group mt-3">
                    <label for="totalTTC" class="form-label">Total TTC</label>
                    <input type="text" class="form-control" id="totalTTC" name="totalTTC" readonly>
                </div>

                <div id="resultat"></div>
                <!-- Buttons -->
                <div class="btn-group d-flex flex-column mt-3">
                    <button type="button" class="btn btn-primary mt-2" id="saveBtn" style="margin-bottom:2px;">
                        <i class="fas fa-save"></i> Enregistrer le Devis
                    </button>
                    <button type="button" class="btn btn-secondary" id="exportPdfBtn">
                        <i class="fas fa-file-pdf"></i> Exporter PDF
                    </button>
                </div>
            </div>
        </div>


    </div>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="text-center">
                <p>&copy; <?php echo gmdate('Y'); ?> BANAMUR INDUSTRIES & TECH. Tous droits réservés.</p>
                <div class="social-icons">
                    <a href="#" class="fab fa-facebook-f"></a>
                    <a href="#" class="fab fa-twitter"></a>
                    <a href="#" class="fab fa-linkedin-in"></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let uniteOptions = `
        <option value="" disabled selected>Choisir...</option>
        <?php foreach ($unites as $unite): ?>
            <option value="<?= (int)$unite['id'] ?>">
                <?= htmlspecialchars($unite['libelle']) ?> (<?= htmlspecialchars($unite['symbole']) ?>)
            </option>
        <?php endforeach; ?>
        `;
    </script>
    <script src="js/script.js"></script>
    <!--Intégration de jquery/Ajax-->
    <script src="../logi/js/jquery_1.7.1_jquery.min.js"></script>
    <script src="js/function_devis.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        function initSelect2() {
            $('.unite-select').select2({
                dropdownParent: $('#devisTable').parent(),
                width: '100%',
                placeholder: "Choisir..."
            });
        }
        $(document).ready(function() {
            initSelect2();
        });
    </script>
</body>

</html>