<?php
include 'auth_check.php';
include 'header/header_liste_offre.php';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Offres - BTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom_style_liste_offre.css">
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
        <h1 class="text-center">Liste des Offres</h1>

        <!-- Button to trigger modal -->
        <div class="text-center mb-4">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOfferModal">
                Ajouter une offre
            </button>
        </div>

        <!-- Cards displaying offers -->
        <div class="card-grid">
            <?php foreach ($offers as $index => $offer):
                $fichiers = $offreModel->getFichiersByOffre($offer['id_offre']);
                // Génère un ID unique pour l'accordéon de chaque offre
                $accordionId = "accordionFiles" . $offer['id_offre'];
            ?>
                <div class="card h-100 d-flex flex-column">
                    <div class="card-header">Numéro d'Offre: <?= htmlspecialchars($offer['num_offre']) ?></div>
                    <div class="card-body d-flex flex-column">
                        <p><strong>Date d'Offre:</strong> <?= htmlspecialchars($offer['date_offre']) ?></p>
                        <p><strong>Référence:</strong> <?= htmlspecialchars($offer['reference_offre']) ?></p>
                        <p><strong>Commercial dédié:</strong> <?= htmlspecialchars($offer['commercial_dedie']) ?></p>
                        <p><strong>Date de Création:</strong> <?= htmlspecialchars($offer['date_creat_offre']) ?></p>
                        <?php if (!empty($fichiers)): ?>
                            <div class="accordion mt-3" id="<?= $accordionId ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $accordionId ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $accordionId ?>" aria-expanded="false" aria-controls="collapse<?= $accordionId ?>" style="background:#fffbe6;color:#000;">
                                            <strong>Fichiers joints</strong>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $accordionId ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $accordionId ?>" data-bs-parent="#<?= $accordionId ?>">
                                        <div class="accordion-body" style="background:#fffbe6;">
                                            <ul style="list-style: none; padding-left: 0; margin-bottom:0;">
                                                <?php foreach ($fichiers as $fichier): ?>
                                                    <?php $webPath = str_replace('../', '', $fichier['file_path']); ?>
                                                    <li>
                                                        <a href="<?= htmlspecialchars($webPath) ?>" download="<?= htmlspecialchars($fichier['file_name']) ?>" target="_blank" style="color:#000;text-decoration:underline;">
                                                            <i class="fas fa-paperclip"></i> <?= htmlspecialchars($fichier['file_name']) ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <!-- Pour forcer la hauteur identique et aligner le bouton en bas si besoin -->
                        <div class="flex-grow-1"></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal for adding offer -->
    <div class="modal fade" id="addOfferModal" tabindex="-1" aria-labelledby="addOfferModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addOfferModalLabel">Ajouter une Offre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="request/ajouter_offre.php" enctype="multipart/form-data" method="POST">
                        <div class="mb-3">
                            <label for="num_offre" class="form-label">Numéro d'Offre</label>
                            <input type="text" class="form-control" id="num_offre" name="num_offre" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_offre" class="form-label">Date d'Offre</label>
                            <input type="date" class="form-control" id="date_offre" name="date_offre" required>
                        </div>
                        <div class="mb-3">
                            <label for="reference_offre" class="form-label">Référence</label>
                            <input type="text" class="form-control" id="reference_offre" name="reference_offre" required>
                        </div>
                        <div class="mb-3">
                            <label for="commercial_dedie" class="form-label">Commercial dédié</label>
                            <input type="text" class="form-control" id="commercial_dedie" name="commercial_dedie" required>
                        </div>
                        <div class="mb-3">
                            <label for="date_creat_offre" class="form-label">Date de Création</label>
                            <input type="date" class="form-control" id="date_creat_offre" name="date_creat_offre" required>
                        </div>
                        <div class="mb-3">
                            <label for="offre_files" class="form-label">Fichiers relatifs à l'offre</label>
                            <input type="file" class="form-control" id="offre_files" name="offre_files[]" multiple style="display:none;">
                            <div id="drop_zone" style="border:2px dashed #fabd02; background:#fffbe6; color:#000; padding:30px; text-align:center; cursor:pointer;">
                                Glissez-déposez vos fichiers ici ou cliquez pour sélectionner
                            </div>
                            <div id="file_list"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter l'Offre</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center py-3">
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
    <script src="js/script.js"></script>
    <!--Intégration de jquery/Ajax-->
    <script src="../logi/js/jquery_1.7.1_jquery.min.js"></script>
    <script src="js/function.js"></script>
    <script>
        const dropZone = document.getElementById('drop_zone');
        const fileInput = document.getElementById('offre_files');
        const fileList = document.getElementById('file_list');
        let filesBuffer = [];

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.style.background = '#fdd96c';
        });
        dropZone.addEventListener('dragleave', e => {
            e.preventDefault();
            dropZone.style.background = '#fffbe6';
        });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.style.background = '#fffbe6';
            addFiles(e.dataTransfer.files);
        });
        fileInput.addEventListener('change', () => addFiles(fileInput.files));

        function addFiles(fileListInput) {
            for (let file of fileListInput) {
                filesBuffer.push(file);
            }
            updateFileList();
            updateInputFiles();
        }

        function removeFile(index) {
            filesBuffer.splice(index, 1);
            updateFileList();
            updateInputFiles();
        }

        function updateFileList() {
            fileList.innerHTML = '';
            filesBuffer.forEach((file, idx) => {
                fileList.innerHTML += `<div style="display:flex;align-items:center;justify-content:space-between;">
                    <span>${file.name}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeFile(${idx})" style="margin-left:10px;">Supprimer</button>
                </div>`;
            });
        }

        // Met à jour l'input file pour l'envoi du formulaire
        function updateInputFiles() {
            const dataTransfer = new DataTransfer();
            filesBuffer.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }
    </script>
</body>

</html>