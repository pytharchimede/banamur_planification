<?php
header('Content-Type: application/json');
$targetDir = "../uploads/ckeditor/";
if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

if (!empty($_FILES['upload']['name'])) {
    $fileName = uniqid('img_') . '.' . pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $targetFile)) {
        // Retourne l'URL relative pour le navigateur
        echo json_encode([
            "url" => "uploads/ckeditor/" . $fileName
        ]);
        exit;
    }
}
http_response_code(400);
echo json_encode(["error" => ["message" => "Erreur upload image"]]);
