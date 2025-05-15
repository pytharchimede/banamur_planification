<?php
include 'auth_check.php';

include 'header/header_voir_debourse.php';

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Voir Déboursé - Devis <?= htmlspecialchars($devis['numero_devis'] ?? '') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom_style_voir_debourse.css">
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
            <i class="fas fa-eye"></i> Déboursé du devis <span class="text-dark"><?= htmlspecialchars($devis['numero_devis'] ?? '') ?></span>
        </h1>
        <div class="mb-4 text-center">
            <span class="badge bg-primary fs-6">Client : <?= htmlspecialchars($devis['destine_a'] ?? '') ?></span>
            <span class="badge bg-success fs-6">Total TTC : <?= number_format($devis['total_ttc'] ?? 0, 0, ',', ' ') ?> FCFA</span>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card stat-card text-center p-3">
                    <div class="fs-2 text-success"><i class="fas fa-coins"></i></div>
                    <div class="fw-bold">Total déboursé</div>
                    <div class="fs-5"><?= number_format($totalDebourse, 0, ',', ' ') ?> FCFA</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card text-center p-3">
                    <div class="fs-2 text-info"><i class="fas fa-list-ol"></i></div>
                    <div class="fw-bold">Nombre de sous-lignes</div>
                    <div class="fs-5"><?= $nbSousLignes ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card p-3">
                    <div class="fw-bold mb-2"><i class="fas fa-chart-pie"></i> Répartition par catégorie</div>
                    <?php foreach ($categoriesStats as $cat => $montant): ?>
                        <span class="badge bg-secondary badge-categorie mb-1"><?= ucfirst($cat) ?> : <?= number_format($montant, 0, ',', ' ') ?> FCFA</span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex justify-content-end gap-2 mb-3">
            <a href="request/export_debourse_pdf.php?devisId=<?= $devisId ?>" target="_blank" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exporter déboursé (PDF)
            </a>
            <a href="request/export_debourse_excel.php?devisId=<?= $devisId ?>" target="_blank" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Exporter déboursé (Excel)
            </a>
            <a href="request/export_planning_gantt.php?devisId=<?= $devisId ?>" target="_blank" class="btn btn-primary">
                <i class="fas fa-project-diagram"></i> Exporter planning (Gantt)
            </a>
            <a href="liste_devis.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>

        <?php $index = 0; ?>

        <!-- Tableau des déboursés -->
        <?php foreach ($lignes as $ligne):
            $index++;
            $debourse = $debourses[$ligne['id']] ?? null;
            if (!$debourse) continue;
            $sousLignes = $devisModel->getLignesDebourse($debourse['id']);
            $prix_devis = $ligne['prix'] ?? 0;
            $quantite_devis = $ligne['quantite'] ?? 0;
            $montant_devis = $prix_devis * $quantite_devis;
        ?>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <span class="badge bg-primary rounded-circle fs-5 me-2" style="width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;">
                        <?= $index ?>
                    </span>
                    <span class="fw-bold fs-5"><?= htmlspecialchars($ligne['designation']) ?></span>
                    <span class="float-end text-secondary">Montant devis : <?= number_format($montant_devis ?? 0, 0, ',', ' ') ?> FCFA</span>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Montant déboursé :</strong>
                            <span class="editable" data-type="montant" data-id="<?= $debourse['id'] ?>">
                                <?= number_format($debourse['montant_debourse'] ?? 0, 0, ',', ' ') ?> FCFA
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Responsable :</strong>
                            <span class="editable" data-type="responsable" data-id="<?= $debourse['id'] ?>">
                                <?php
                                $resp = array_filter($utilisateurs, fn($u) => $u['id'] == $debourse['responsable_id']);
                                echo htmlspecialchars($resp ? reset($resp)['nom'] : 'Non défini');
                                ?>
                            </span>
                        </div>
                        <div class="col-md-4">
                            <strong>Période :</strong>
                            <span class="periode-debourse"><?= htmlspecialchars($debourse['date_debut'] ?? '') ?> au <?= htmlspecialchars($debourse['date_fin'] ?? '') ?></span>
                        </div>
                    </div>
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
                            <tbody>
                                <?php foreach ($sousLignes as $sous): ?>
                                    <tr>
                                        <td><?= ucfirst($sous['categorie']) ?></td>
                                        <td class="editable" data-type="designation" data-id="<?= $sous['id'] ?>">
                                            <?= htmlspecialchars($sous['designation']) ?>
                                        </td>
                                        <td class="editable" data-type="montant_sous" data-id="<?= $sous['id'] ?>">
                                            <?= number_format($sous['montant'], 0, ',', ' ') ?> FCFA
                                        </td>
                                        <td class="editable" data-type="date_debut" data-id="<?= $sous['id'] ?>">
                                            <?= htmlspecialchars($sous['date_debut']) ?>
                                        </td>
                                        <td class="editable" data-type="date_fin" data-id="<?= $sous['id'] ?>">
                                            <?= htmlspecialchars($sous['date_fin']) ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-outline-warning btn-sm btn-edit-sous-ligne" data-id="<?= $sous['id'] ?>" data-bs-toggle="modal" data-bs-target="#modalEditSousLigne">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modal édition sous-ligne déboursé -->
    <div class="modal fade" id="modalEditSousLigne" tabindex="-1" aria-labelledby="modalEditSousLigneLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formEditSousLigne" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditSousLigneLabel">Modifier la sous-ligne</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="editSousLigneId">
                    <div class="mb-3">
                        <label for="editDesignation" class="form-label">Désignation</label>
                        <input type="text" class="form-control" name="designation" id="editDesignation" required>
                    </div>
                    <div class="mb-3">
                        <label for="editMontant" class="form-label">Montant</label>
                        <input type="number" class="form-control" name="montant" id="editMontant" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDateDebut" class="form-label">Date début</label>
                        <input type="date" class="form-control" name="date_debut" id="editDateDebut" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDateFin" class="form-label">Date fin</label>
                        <input type="date" class="form-control" name="date_fin" id="editDateFin" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
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
        // Edition rapide (inline) pour montant, designation, responsable, date_debut, date_fin
        $('.editable').on('click', function() {
            let span = $(this);
            if (span.find('input,select').length) return;
            let type = span.data('type');
            let id = span.data('id');
            let val = span.text().trim().replace(' FCFA', '');
            let input;
            if (type === 'responsable') {
                input = $('<select class="edit-input"></select>');
                <?php foreach ($utilisateurs as $u): ?>
                    input.append('<option value="<?= $u['id'] ?>"><?= addslashes($u['nom']) ?></option>');
                <?php endforeach; ?>
            } else if (type === 'date_debut' || type === 'date_fin') {
                input = $('<input type="date" class="edit-input" value="' + val + '">');
            } else {
                input = $('<input type="text" class="edit-input" value="' + val + '">');
            }
            span.html(input);
            input.focus();
            input.on('blur keydown', function(e) {
                if (e.type === 'blur' || (e.type === 'keydown' && e.key === 'Enter')) {
                    let newVal = input.val();
                    $.post('request/update_debourse_inline.php', {
                        id: id,
                        type: type,
                        value: newVal
                    }, function(resp) {
                        if (resp.success) {
                            if (type === 'montant' || type === 'montant_sous') {
                                span.html(Number(newVal).toLocaleString('fr-FR') + ' FCFA');
                            } else if (type === 'responsable') {
                                span.html(input.find('option:selected').text());
                            } else {
                                span.html(newVal);
                            }
                            // Actualiser montant total et période si besoin
                            if (resp.montant_debourse !== undefined) {
                                span.closest('.card-body').find('[data-type="montant"]').html(Number(resp.montant_debourse).toLocaleString('fr-FR') + ' FCFA');
                            }
                            if (resp.date_debut && resp.date_fin) {
                                span.closest('.card-body').find('.periode-debourse').html(resp.date_debut + ' au ' + resp.date_fin);
                            }
                        } else {
                            span.html(val);
                            alert(resp.message || 'Erreur lors de la mise à jour.');
                        }
                    }, 'json');
                }
            });
        });

        // Bouton édition (peut ouvrir un modal pour édition avancée)
        $('.btn-edit-sous-ligne').on('click', function() {
            var tr = $(this).closest('tr');
            var id = $(this).data('id');
            $('#editSousLigneId').val(id);
            $('#editDesignation').val(tr.find('[data-type="designation"]').text().trim());
            $('#editMontant').val(tr.find('[data-type="montant_sous"]').text().replace(/\s+FCFA/, '').replace(/\s/g, ''));
            $('#editDateDebut').val(tr.find('[data-type="date_debut"]').text().trim());
            $('#editDateFin').val(tr.find('[data-type="date_fin"]').text().trim());
            var modal = new bootstrap.Modal(document.getElementById('modalEditSousLigne'));
            modal.show();
        });

        // Soumission du formulaire modal
        $('#formEditSousLigne').on('submit', function(e) {
            e.preventDefault();
            $.post('request/update_debourse_inline.php', $(this).serialize(), function(resp) {
                if (resp.success) {
                    location.reload(); // Ou mieux : mettre à jour la ligne sans recharger
                } else {
                    alert(resp.message || 'Erreur lors de la mise à jour.');
                }
            }, 'json');
        });
    </script>
</body>

</html>