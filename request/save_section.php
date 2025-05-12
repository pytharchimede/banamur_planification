<?php
require_once '../model/Database.php';
require_once '../model/Devis.php';


$databaseObj = new Database();
$pdo = $databaseObj->getConnection();
$devisModel = new Devis($pdo);
$devisId = intval($_POST['devis_id'] ?? 0);
$section = $_POST['section'] ?? '';
$contenu = $_POST['contenu'] ?? '';
$devisModel->saveSectionContent($devisId, $section, $contenu);
header('Location: ../editer_section.php?devisId=' . $devisId . '&section=' . $section . '&saved=1');


exit;
