<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

require_once("../model/Utils.php");
require_once("../model/Database.php");
require_once("../model/Devis.php");
require_once("../model/Client.php");
require_once("../model/Offre.php");
require_once("../model/UniteMesure.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// --- Récupération des données ---
$pdo = Database::getConnection();
$devisObj = new Devis($pdo);
$clientObj = new Client($pdo);
$offreObj = new Offre($pdo);

if (!isset($_SESSION['devisId']) && !isset($_GET['devisId'])) {
    die('ID de devis non défini.');
}
$devisId = $_GET['devisId'] ?? $_SESSION['devisId'];
$devis = $devisObj->getDevisById($devisId);
if (!$devis) die('Devis non trouvé.');
$lignes = $devisObj->getLignesDevis($devisId);
$client = $clientObj->getClientById($devis['client_id']);
if (!$client) die('Client non trouvé.');
$offre = $offreObj->getOffreById($devis['offre_id']);
if (!$offre) die('Offre non trouvée.');

$uniteModel = new UniteMesure($pdo);
$unitesArray = [];
foreach ($uniteModel->getAll() as $u) {
    $unitesArray[$u['id']] = $u;
}

// --- Création du fichier Excel ---
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Devis');

// --- Ajout du logo ---
$logoPath = '../logo/' . ($devis['logo'] ?: 'default_logo.jpg');
if (file_exists($logoPath)) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setPath($logoPath);
    $drawing->setHeight(60);
    $drawing->setCoordinates('A1');
    $drawing->setWorksheet($sheet);
}

// --- Ajout du QR Code (si tu as déjà généré un QR code PNG pour ce devis) ---
$qrcodePath = '../qrcodes/qrcode_' . $devis['id'] . '.png';
if (file_exists($qrcodePath)) {
    $drawingQR = new Drawing();
    $drawingQR->setName('QR Code');
    $drawingQR->setPath($qrcodePath);
    $drawingQR->setHeight(60);
    $drawingQR->setCoordinates('F1');
    $drawingQR->setWorksheet($sheet);
}

// --- Expéditeur et destinataire ---
$row = 5;
$sheet->setCellValue("A$row", "Expéditeur : BANAMUR BTP");
$sheet->getStyle("A$row")->getFont()->setBold(true);
$row++;
$sheet->setCellValue("A$row", "Destinataire : " . $client['nom_client']);
$sheet->getStyle("A$row")->getFont()->setBold(true);
$row += 2;

// --- Infos client et devis ---
$sheet->setCellValue("A$row", "Adresse : " . $client['localisation_client']);
$row++;
$sheet->setCellValue("A$row", "Commune : " . $client['commune_client']);
$row++;
$sheet->setCellValue("A$row", "BP : " . $client['bp_client']);
$row++;
$sheet->setCellValue("A$row", "Pays : " . $client['pays_client']);
$row += 2;
$sheet->setCellValue("A$row", "Devis N° " . $devis['numero_devis']);
$row++;
$sheet->setCellValue("A$row", "Date émission : " . $devis['date_emission']);
$row++;
$sheet->setCellValue("A$row", "Date expiration : " . $devis['date_expiration']);
$row++;
$sheet->setCellValue("A$row", "Offre : " . $offre['num_offre']);
$row++;
$sheet->setCellValue("A$row", "Référence : " . $offre['reference_offre']);
$row++;
$sheet->setCellValue("A$row", "Interlocuteur : " . $offre['commercial_dedie']);
$row += 2;

// --- En-tête du tableau ---
$headerRow = $row;
$headers = ['N°', 'Désignation', 'Quantité', 'Unité', 'Prix unitaire', 'Prix total'];
foreach ($headers as $col => $title) {
    $colLetter = Coordinate::stringFromColumnIndex($col + 1);
    $sheet->setCellValue($colLetter . $headerRow, $title);
}
$sheet->getStyle("A$headerRow:F$headerRow")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF000000');
$sheet->getStyle("A$headerRow:F$headerRow")->getFont()->getColor()->setARGB('FFFFFFFF');
$sheet->getStyle("A$headerRow:F$headerRow")->getFont()->setBold(true);
$sheet->getStyle("A$headerRow:F$headerRow")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$row++;

// --- Lignes du devis avec formules ---
$startDataRow = $row;
$pos = 1;
foreach ($lignes as $index => $ligne) {
    $unite = isset($unitesArray[$ligne['unite_id']]) ? $unitesArray[$ligne['unite_id']]['symbole'] : '';
    $sheet->setCellValue("A$row", $pos++);
    $sheet->setCellValue("B$row", $ligne['designation']);
    $sheet->setCellValue("C$row", $ligne['quantite']);
    $sheet->setCellValue("D$row", $unite);
    $sheet->setCellValue("E$row", $ligne['prix']);
    $sheet->setCellValue("F$row", "=C$row*E$row");
    $row++;
}

// --- Totaux (avec formules) ---
$sheet->setCellValue("E$row", "MONTANT HT");
$sheet->setCellValue("F$row", "=SUM(F$startDataRow:F" . ($row - 1) . ")");
$sheet->getStyle("E$row:F$row")->getFont()->setBold(true);
$row++;

if ($devis['tva_facturable'] == 1) {
    $sheet->setCellValue("E$row", "TVA 18%");
    $sheet->setCellValue("F$row", "=F" . ($row - 1) . "*0.18");
    $sheet->getStyle("E$row:F$row")->getFont()->setBold(true);
    $row++;
}

$sheet->setCellValue("E$row", "MONTANT TTC");
if ($devis['tva_facturable'] == 1) {
    $sheet->setCellValue("F$row", "=F" . ($row - 2) . "+F" . ($row - 1));
} else {
    $sheet->setCellValue("F$row", "=F" . ($row - 1));
}
$sheet->getStyle("E$row:F$row")->getFont()->setBold(true);

// --- Mise en forme rapide ---
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}
$sheet->getStyle("A$headerRow:F$row")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// --- Pied de page ---
$row += 2;
$sheet->setCellValue("A$row", "FOURNITURES INDUSTRIELLES, DEPANNAGE ET TRAVAUX PUBLIQUES - Au capital de 10 000 000 F CFA - Siège Social : Abidjan, Koumassi, Zone industrielle");
$row++;
$sheet->setCellValue("A$row", "01 BP 1642 Abidjan 01 - Téléphone : (+225) 27-21-36-27-27  -  Email : info@fidest.org - RCCM : CI-ABJ-2017-B-20163  -  N° CC : 010274200088");
$sheet->mergeCells("A" . ($row - 1) . ":F" . ($row - 1));
$sheet->mergeCells("A$row:F$row");
$sheet->getStyle("A" . ($row - 1) . ":A$row")->getFont()->setSize(8);
$sheet->getStyle("A" . ($row - 1) . ":A$row")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// --- Téléchargement ---
$filename = 'devis_' . $devis['id'] . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
