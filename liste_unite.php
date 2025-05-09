<?php

require_once 'header/header_liste_unite.php';

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Unités de Mesure</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/custom_style_dashboard.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
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

    <div class="container mt-4">
        <h1 class="text-center mb-4">Liste des Unités de Mesure</h1>
        <!-- Bouton Ajouter -->
        <div class="mb-3 text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUniteModal">
                <i class="fas fa-plus"></i> Ajouter une unité
            </button>
        </div>
        <div class="card">
            <div class="card-header" style="background:#000;color:#fdd96c;">
                <i class="fas fa-ruler"></i> Unités disponibles
            </div>
            <div class="card-body" style="background:#fffbe6;">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center" id="unitesTable">
                        <thead style="background:#fdd96c;color:#000;">
                            <tr>
                                <th>Symbole</th>
                                <th>Libellé</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unites as $unite): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($unite['symbole']) ?></strong></td>
                                    <td><?= htmlspecialchars($unite['libelle']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout d'une unité -->
    <div class="modal fade" id="addUniteModal" tabindex="-1" aria-labelledby="addUniteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" id="formAddUnite" autocomplete="off">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUniteModalLabel">Ajouter une unité de mesure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="symbole" class="form-label">Symbole</label>
                        <input type="text" class="form-control" id="symbole" name="symbole" required>
                    </div>
                    <div class="mb-3">
                        <label for="libelle" class="form-label">Libellé</label>
                        <input type="text" class="form-control" id="libelle" name="libelle" required>
                    </div>
                    <div id="unite-error" class="text-danger"></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container text-center">
            <p>&copy; <?= gmdate('Y') ?> BANAMUR INDUSTRIES & TECH. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/2b8b2e5e0e.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#formAddUnite').on('submit', function(e) {
            e.preventDefault();
            $('#unite-error').text('');
            $.ajax({
                url: 'request/ajouter_unite.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Ajoute la nouvelle unité dans le tableau sans recharger
                        $('#unitesTable tbody').append(
                            `<tr>
                            <td><strong>${$('<div>').text(response.symbole).html()}</strong></td>
                            <td>${$('<div>').text(response.libelle).html()}</td>
                        </tr>`
                        );
                        $('#addUniteModal').modal('hide');
                        $('#formAddUnite')[0].reset();
                    } else {
                        $('#unite-error').text(response.error || "Erreur lors de l'ajout.");
                    }
                },
                error: function() {
                    $('#unite-error').text("Erreur serveur.");
                }
            });
        });
    </script>
</body>

</html>