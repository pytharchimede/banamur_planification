<?php
session_start();

include_once '../header/header_export_pdf.php';
require_once("../model/Utils.php");

// Créez une classe dérivée de FPDF
class PDF extends FPDF
{
    // Méthode pour l'en-tête
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, '', 0, 1, 'C');
        $this->Ln(10);
    }

    // Méthode pour le pied de page
    function Footer()
    {
        // Dessiner une ligne noire
        // $this->SetDrawColor(0, 0, 0);
        // $this->Line(10, 272, 200, 272);

        // Position à 1.5 cm du bas
        $this->SetY(-22);

        // Arial 7
        $this->SetFont('Arial', '', 7);

        // Ligne 1
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("S.A.R.L au Capital de 100 000 000 FCFA - Siège Social: Abidjan, Koumassi Bd. du Gabon prolongé – 01 BP 1642 Abidjan 01"), 0, 1, 'C');
        // Ligne 2
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("RCCM N°: CI-ABJ-03-2022-B13-02828 – Tél. : +225 27 21 36 27 27 / 27 21 36 09 29 – Fax : 27 21 36 05 75"), 0, 1, 'C');
        // Ligne 3
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("E-mail: banacerf1@gmail.com - Compte Bancaire BDU N° : CI180 01010 020401144580 11"), 0, 1, 'C');

        // Numéro de page
        // $this->Cell(0, 10, Utils::toMbConvertEncoding('Page ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}

// Créez un nouvel objet FPDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
// $pdf->AliasNbPages();

// Dimensions
$logoPath = '../logo/' . ($devis['logo'] ?? 'default_logo.jpg');
$logoWidth = 40;
$logoHeight = 40;
$enteteX = 10;
$enteteY = 10;
$enteteW = 190;

// Afficher le logo à gauche
$pdf->Image($logoPath, $enteteX, $enteteY, $logoWidth, $logoHeight);

// Zone de texte à droite du logo
$textX = $enteteX + $logoWidth + 5;
$textW = $enteteW - $logoWidth - 5;
$textY = $enteteY;

// Préparer les 4 lignes (inchangé)
$lines = [
    [
        'text' => 'BANAMUR INDUSTRIES ET TECHNOLOGIES',
        'size' => 16,
        'style' => 'B',
        'color' => [0, 0, 0],
        'font' => 'Arial'
    ],
    [
        'text' => 'BATIMENT-TRAVAUX PUBLICS',
        'size' => 13,
        'style' => '',
        'color' => [0, 0, 0],
        'font' => 'Arial'
    ],
    [
        'text' => 'RENOVATION ET TRAVAUX NEUF',
        'size' => 13,
        'style' => 'B',
        'color' => [255, 204, 0],
        'font' => 'Arial'
    ],
    [
        'text' => 'TUYAUTERIE-CHAUDRENERIE-CHARPENTE METALLIQUE',
        'size' => 14,
        'style' => '',
        'color' => [0, 0, 0],
        'font' => 'Arial'
    ],
];

// Calculer la hauteur d'une ligne pour occuper exactement la hauteur du logo
$lineH = $logoHeight / count($lines);

// Afficher chaque ligne centrée dans la zone texte, alignée verticalement avec le logo
for ($i = 0; $i < count($lines); $i++) {
    $pdf->SetXY($textX, $textY + $i * $lineH);
    $pdf->SetFont($lines[$i]['font'], $lines[$i]['style'], $lines[$i]['size']);
    $pdf->SetTextColor($lines[$i]['color'][0], $lines[$i]['color'][1], $lines[$i]['color'][2]);
    $pdf->Cell($textW, $lineH, Utils::toMbConvertEncoding($lines[$i]['text']), 0, 0, 'C');
}

// Remettre la couleur noire pour la suite
$pdf->SetTextColor(0, 0, 0);
// Remonter le bloc (ajuste la valeur selon ton rendu souhaité)
$pdf->Ln($logoHeight - 5);

// Afficher la mention (en dehors du cadre, centré, gras, grand)
$mention = Utils::toMbConvertEncoding('OFFRE COMMERCIALE');
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 14, $mention, 0, 1, 'C');
$pdf->Ln(3);

// Bloc référence offre avec padding augmenté
$refText = Utils::toMbConvertEncoding(strtoupper($offre['reference_offre']));
$pdf->SetFont('Arial', 'B', 18);
$maxWidth = 180;
$paddingX = 10; // padding gauche/droite en mm
$paddingY = 6;  // padding haut/bas en mm

$textWidth = $maxWidth;
$pageWidth = 210 - 20; // marges 10mm
$startX = 10 + ($pageWidth - $textWidth) / 2;

// Positionner au début du bloc
$pdf->SetX($startX);
$y1 = $pdf->GetY();

// Padding haut
$pdf->Ln($paddingY);

// Afficher la référence dans le cadre, multiligne, centrée avec padding horizontal
$pdf->SetX($startX + $paddingX);
$pdf->MultiCell($textWidth - 2 * $paddingX, 10, $refText, 0, 'C', false);

// Padding bas
$pdf->Ln($paddingY);

// Position après le bloc
$y2 = $pdf->GetY();

// Dessiner le cadre autour de la référence uniquement (inclut le padding)
$pdf->Rect($startX, $y1, $textWidth, $y2 - $y1);

// Aller à la ligne après le bloc
$pdf->SetY($y2);
$pdf->Ln(8);

// Présentée à la
$pdf->SetFont('Arial', '', 13);
$pdf->Ln(6);
$pdf->Cell(0, 8, Utils::toMbConvertEncoding('Présentée à'), 0, 1, 'C');

// Sauter 4 lignes
$pdf->Ln(8);

// Logo du client centré (au milieu de la page)
$clientLogoPath = '../uploads/logos_clients/' . ($client['logo_client'] ?? 'default_logo.jpg');
error_log($clientLogoPath);
if (!file_exists($clientLogoPath)) {
    $clientLogoPath = null;
}
$clientLogoWidth = 50;
$pageWidth = 210;
$logoX = ($pageWidth - $clientLogoWidth) / 2;
$currentY = $pdf->GetY();
if ($clientLogoPath) {
    $pdf->Image($clientLogoPath, $logoX, $currentY, $clientLogoWidth);
    // Sauter la hauteur du logo + un peu d'espace
    $pdf->Ln($clientLogoWidth + 5);
} else {
    // Sauter un espace équivalent si pas de logo
    $pdf->Ln($clientLogoWidth + 5);
}

// Correspondant sur le devis
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($devis['correspondant']) . ' - ' . Utils::toMbConvertEncoding($client['nom_client']), 0, 1, 'C');
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($client['localisation_client']), 0, 1, 'C');
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($client['bp_client']), 0, 1, 'C');


// Sauter 3 lignes
$pdf->Ln(5);

// Dénommé le « Client »
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 8, Utils::toMbConvertEncoding('Dénommé le « Client »'), 0, 1, 'C');

// Sauter 3 lignes
$pdf->Ln(8);

// Référence de l’offre alignée à gauche sur toute la largeur (hors cadre)
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding("Référence de l’offre :"), 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding(strtoupper($offre['num_offre'])), 0, 1, 'L');

// Espace pour la signature du Directeur Technique
$pdf->Ln(5); // espace avant la zone de signature

ob_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="devis_' . $devis['id'] . '.pdf"');

$pdf->Output('I', 'devis_' . $devis['id'] . '.pdf');

unset($_SESSION['devisId']);

exit();
