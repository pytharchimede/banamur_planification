<?php
include 'auth_check.php';
include 'header/header_gerer_debourse.php';

function getCategorieOptions($selected = '')
{
    $cats = ['matériel', 'matériaux', 'transport', 'main d\'œuvre', 'EPI'];
    $html = '';
    foreach ($cats as $cat) {
        $sel = ($cat == $selected) ? 'selected' : '';
        $html .= "<option value=\"$cat\" $sel>" . ucfirst($cat) . "</option>";
    }
    return $html;
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer le déboursé - Devis <?= htmlspecialchars($devis['numero_devis'] ?? '') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom_style_gerer_debourse.css">
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

    <div class="container py-4">
        <h1 class="text-center wow-title mb-4">
            <i class="fas fa-coins"></i> Gérer le déboursé du devis <span class="text-dark"><?= htmlspecialchars($devis['numero_devis'] ?? '') ?></span>
        </h1>
        <div class="mb-4 text-center">
            <span class="badge bg-primary fs-6">Client : <?= htmlspecialchars($devis['destine_a'] ?? '') ?></span>
            <span class="badge bg-success fs-6">Total TTC : <?= number_format($devis['total_ttc'] ?? 0, 0, ',', ' ') ?> FCFA</span>
        </div>

        <form id="form-debourse" method="post">
            <input type="hidden" name="devis_id" value="<?= $devisId ?>">
            <?php $index = 0; ?>
            <?php foreach ($lignes as $ligne):
                $index++;
                $debourse = $debourses[$ligne['id']] ?? [];
                $montant_ligne = isset($ligne['prix']) && is_numeric($ligne['prix']) ? $ligne['prix'] : 0;
                $quantite_ligne = isset($ligne['quantite']) && is_numeric($ligne['quantite']) ? $ligne['quantite'] : 0;
                $total_debourse_ligne = $debourse['montant_debourse'] ?? 0;
                $total_ligne = $montant_ligne * $quantite_ligne;
                $montant_ligne = $total_ligne > 0 ? $total_ligne : $montant_ligne;
                $depassement = ($total_debourse_ligne > $montant_ligne);
            ?>
                <div class="card card-ligne">
                    <div class="card-header bg-light">
                        <span class="badge bg-primary rounded-circle fs-5 me-2" style="width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;">
                            <?= $index ?>
                        </span>
                        <span class="fw-bold fs-5"><?= htmlspecialchars($ligne['designation']) ?></span>
                        <span class="float-end text-secondary">Montant devis : <?= number_format($total_ligne, 0, ',', ' ') ?> FCFA</span>
                    </div>
                    <div class="card-body">
                        <?php if ($depassement): ?>
                            <div class="alert alert-depassement mb-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                Attention : le montant déboursé dépasse le montant du devis !
                            </div>
                        <?php endif; ?>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Montant déboursé</label>
                                <input type="number" min="0" step="0.01" class="form-control" name="montant_debourse[<?= $ligne['id'] ?>]" value="<?= htmlspecialchars($debourse['montant_debourse'] ?? '') ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Responsable</label>
                                <select class="form-select" name="responsable_id[<?= $ligne['id'] ?>]">
                                    <option value="">-- Sélectionner --</option>
                                    <?php foreach ($utilisateurs as $user): ?>
                                        <option value="<?= $user['id'] ?>" <?= (!empty($debourse['responsable_id']) && $debourse['responsable_id'] == $user['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Dates (début/fin)</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" name="date_debut[<?= $ligne['id'] ?>]" value="<?= htmlspecialchars($debourse['date_debut'] ?? '') ?>">
                                    <span class="input-group-text">au</span>
                                    <input type="date" class="form-control" name="date_fin[<?= $ligne['id'] ?>]" value="<?= htmlspecialchars($debourse['date_fin'] ?? '') ?>">
                                </div>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-outline-primary btn-add-sous-ligne" data-bs-toggle="collapse" data-bs-target="#sousLignes<?= $ligne['id'] ?>">
                                    <i class="fas fa-list"></i> Détail déboursé
                                </button>
                            </div>
                        </div>
                        <!-- Sous-lignes de déboursé (fiche de décaissement) -->
                        <div class="collapse mt-4" id="sousLignes<?= $ligne['id'] ?>">
                            <h6 class="mb-3"><i class="fas fa-stream"></i> Sous-lignes de déboursé</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-debourse align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Désignation</th>
                                            <th>Montant</th>
                                            <th>Date début</th>
                                            <th>Date fin</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-sous-lignes-<?= $ligne['id'] ?>">
                                        <!-- Les sous-lignes existantes seront chargées ici via JS ou PHP -->
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-success btn-sm" onclick="ajouterSousLigne(<?= $ligne['id'] ?>)">
                                <i class="fas fa-plus"></i> Ajouter une sous-ligne
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="text-end my-4">
                <button type="submit" class="btn btn-lg btn-primary px-5">
                    <i class="fas fa-save"></i> Enregistrer les déboursés
                </button>
            </div>
        </form>
        <div id="alert-debourse"></div>
    </div>

    <footer class="footer text-white text-center py-3 bg-dark">
        <div class="container">
            <p>&copy; <?= gmdate('Y'); ?> BANAMUR INDUSTRIES & TECH. Tous droits réservés.</p>
            <div class="social-icons">
                <a href="#" class="fab fa-facebook-f"></a>
                <a href="#" class="fab fa-twitter"></a>
                <a href="#" class="fab fa-linkedin-in"></a>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ajout dynamique de sous-lignes (en JS, à adapter pour sauvegarde côté serveur)
        function ajouterSousLigne(ligneId) {
            let tbody = document.getElementById('tbody-sous-lignes-' + ligneId);
            let idx = tbody.rows.length;
            let row = tbody.insertRow();
            row.className = 'ligne-sous-debourse';
            row.innerHTML = `
            <td>
                <select name="categorie[${ligneId}][]" class="form-select" required>
                    <option value="">--Catégorie--</option>
                    <option value="matériel">Matériel</option>
                    <option value="matériaux">Matériaux</option>
                    <option value="transport">Transport</option>
                    <option value="main d'œuvre">Main d'œuvre</option>
                    <option value="EPI">EPI</option>
                </select>
            </td>
            <td><input type="text" name="designation[${ligneId}][]" class="form-control" required></td>
            <td><input type="number" name="montant_sous_ligne[${ligneId}][]" class="form-control" min="0" step="0.01" required></td>
            <td><input type="date" name="date_debut_sous_ligne[${ligneId}][]" class="form-control"></td>
            <td><input type="date" name="date_fin_sous_ligne[${ligneId}][]" class="form-control"></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        }

        $('#form-debourse').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = form.find('button[type=submit]');
            let data = form.serialize(); // Sérialise AVANT de désactiver
            // Désactive tous les champs
            form.find('input, select, textarea, button').prop('disabled', true);
            $('#alert-debourse').html('');
            $.ajax({
                url: 'request/save_debourse.php',
                type: 'POST',
                data: data, // Utilise la variable data
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        $('#alert-debourse').html('<div class="alert alert-success">' + resp.message + '</div>');
                        setTimeout(function() {
                            window.location.href = 'liste_devis.php';
                        }, 5000);
                    } else {
                        $('#alert-debourse').html('<div class="alert alert-danger">' + resp.message + '</div>');
                        // Réactive tous les champs si erreur
                        form.find('input, select, textarea, button').prop('disabled', false);
                    }
                },
                error: function() {
                    $('#alert-debourse').html('<div class="alert alert-danger">Erreur lors de la sauvegarde.</div>');
                    // Réactive tous les champs si erreur
                    form.find('input, select, textarea, button').prop('disabled', false);
                }
            });
        });
    </script>
</body>

</html>