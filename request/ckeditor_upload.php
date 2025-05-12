<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$targetDir = __DIR__ . '/../uploads/ckeditor/';
$publicDir = 'uploads/ckeditor/';

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (
    isset($_FILES['upload']) &&
    is_uploaded_file($_FILES['upload']['tmp_name']) &&
    !empty($_FILES['upload']['name'])
) {
    $ext = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode(["error" => ["message" => "Extension non autorisée"]]);
        exit;
    }
    $fileName = uniqid('img_') . '.' . $ext;
    $targetFile = $targetDir . $fileName;
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $targetFile)) {
        echo json_encode([
            "url" => $publicDir . $fileName
        ]);
        exit;
    }
}

http_response_code(400);
echo json_encode(["error" => ["message" => "Erreur upload image"]]);
