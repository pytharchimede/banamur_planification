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
    <script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>
    <script>
        tinymce.init({
            selector: '#contenu',
            plugins: 'fullscreen table lists link image code',
            toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | link image | table | fullscreen | code',
            height: 500,
            menubar: false,
            branding: false
        });
    </script>
    <style>
        /* Style A4 pour la zone d’édition */
        .a4-page {
            background: #fff;
            margin: 0 auto;
            padding: 2cm 1.5cm;
            min-height: 29.7cm;
            max-width: 21cm;
            box-shadow: 0 0 10px #bbb;
            border-radius: 8px;
        }

        body {
            background: #f4f4f4;
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
                toolbar: [
                    'heading', '|', 'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList', 'blockQuote',
                    '|', 'insertTable', 'undo', 'redo', 'alignment', 'outdent', 'indent', 'imageUpload', 'codeBlock', 'fullscreen'
                ],
                simpleUpload: {
                    uploadUrl: 'request/ckeditor_upload.php',
                }
            })
            .catch(error => {
                console.error(error);
            });
    </script>
</body>

</html>