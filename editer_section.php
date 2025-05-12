<?php
include 'auth_check.php';
require_once 'model/Database.php';
require_once 'model/Devis.php';
$devisId = intval($_GET['devisId'] ?? 0);
$section = $_GET['section'] ?? '';

if ($devisId <= 0 || empty($section)) {
    header('Location: liste_devis.php');
    exit;
}

$databaseObj = new Database();
$pdo = $databaseObj->getConnection();
$devisModel = new Devis($pdo);

// Récupère le contenu existant
$contenu = $devisModel->getSectionContent($devisId, $section);

$titres = [
    'page_garde' => 'Page de garde',
    'description' => 'Description des prestations',
    'delai' => 'Délai de réalisation',
    'conditions' => 'Conditions financières',
    'garantie' => 'Garantie'
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Éditer <?= $titres[$section] ?? '' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@ckeditor/ckeditor5-build-classic@36.0.1/build/ckeditor.js"></script>
    <style>
        body {
            background: #f4f4f4;
        }

        .a4-page {
            background: #fff;
            margin: 0 auto;
            padding: 2cm 1.5cm;
            min-height: 29.7cm;
            max-width: 21cm;
            box-shadow: 0 0 10px #bbb;
            border-radius: 8px;
        }

        .ck-content img {
            max-width: 100%;
            display: block;
            margin: 10px auto;
            position: relative;
        }

        .resizer {
            width: 10px;
            height: 10px;
            background: #007bff;
            position: absolute;
            right: 0;
            bottom: 0;
            cursor: se-resize;
            z-index: 10;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <h2 class="mb-4 text-center"><?= $titres[$section] ?? '' ?> - Devis #<?= $devisId ?></h2>
        <form method="post" action="request/save_section.php">
            <input type="hidden" name="devis_id" value="<?= $devisId ?>">
            <input type="hidden" name="section" value="<?= htmlspecialchars($section) ?>">
            <div class="a4-page mb-3">
                <textarea id="contenu" name="contenu"><?= htmlspecialchars($contenu ?? '') ?></textarea>
            </div>
            <div class="mt-3 d-flex justify-content-center gap-2">
                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Sauvegarder</button>
                <a href="request/export_section_pdf.php?devisId=<?= $devisId ?>&section=<?= $section ?>" target="_blank" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Exporter PDF</a>
                <a href="request/export_section_excel.php?devisId=<?= $devisId ?>&section=<?= $section ?>" target="_blank" class="btn btn-success"><i class="fas fa-file-excel"></i> Exporter Excel</a>
                <a href="liste_devis.php" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>

    <script>
        ClassicEditor
            .create(document.querySelector('#contenu'), {
                extraPlugins: [MyCustomUploadAdapterPlugin],
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'link',
                    'bulletedList', 'numberedList', 'blockQuote',
                    '|', 'insertTable', 'undo', 'redo',
                    'outdent', 'indent',
                    '|', 'imageUpload', 'alignment'
                ],
                image: {
                    toolbar: [
                        'imageStyle:alignLeft',
                        'imageStyle:alignCenter',
                        'imageStyle:alignRight',
                        '|', 'imageTextAlternative'
                    ],
                    styles: ['alignLeft', 'alignCenter', 'alignRight']
                }
            })
            .then(editor => {
                editor.model.document.on('change:data', () => {
                    const editable = editor.ui.getEditableElement();
                    const images = editable.querySelectorAll('img');

                    images.forEach(img => {
                        if (!img.parentElement.querySelector('.resizer')) {
                            const resizer = document.createElement('div');
                            resizer.className = 'resizer';

                            img.style.position = 'relative';
                            img.parentElement.style.position = 'relative';
                            img.parentElement.appendChild(resizer);

                            let startX, startY, startWidth, startHeight;

                            resizer.addEventListener('mousedown', function(e) {
                                e.preventDefault();
                                startX = e.clientX;
                                startY = e.clientY;
                                startWidth = parseInt(document.defaultView.getComputedStyle(img).width, 10);
                                startHeight = parseInt(document.defaultView.getComputedStyle(img).height, 10);

                                document.documentElement.addEventListener('mousemove', doDrag, false);
                                document.documentElement.addEventListener('mouseup', stopDrag, false);
                            });

                            function doDrag(e) {
                                const newWidth = startWidth + e.clientX - startX;
                                const newHeight = startHeight + e.clientY - startY;
                                img.style.width = newWidth + 'px';
                                img.style.height = newHeight + 'px';
                                img.setAttribute('width', newWidth);
                                img.setAttribute('height', newHeight);
                            }

                            function stopDrag() {
                                document.documentElement.removeEventListener('mousemove', doDrag, false);
                                document.documentElement.removeEventListener('mouseup', stopDrag, false);
                            }
                        }
                    });
                });
            })
            .catch(error => {
                console.error(error);
            });

        // Plugin upload personnalisé
        function MyCustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new MyUploadAdapter(loader);
            };
        }

        class MyUploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file.then(file => {
                    const data = new FormData();
                    data.append('upload', file);

                    return fetch('request/ckeditor_upload.php', {
                            method: 'POST',
                            body: data
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (result && result.url) {
                                return {
                                    default: result.url
                                };
                            }
                            throw new Error(result.error?.message || 'Erreur lors de l\'upload');
                        });
                });
            }

            abort() {
                // gestion annulation
            }
        }
    </script>
</body>

</html>