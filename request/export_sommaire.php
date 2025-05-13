<?php
session_start();

include_once '../header/header_export_sommaire.php';
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
        $this->SetDrawColor(0, 0, 0);
        $this->Line(10, 272, 200, 272);

        // Position à 1.5 cm du bas
        $this->SetY(-22);

        // BookAntiqua 7
        $this->SetFont('Arial', '', 7);

        // Ligne 1
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("S.A.R.L au Capital de 100 000 000 FCFA - Siège Social: Abidjan, Koumassi Bd. du Gabon prolongé – 01 BP 1642 Abidjan 01"), 0, 1, 'C');
        // Ligne 2
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("RCCM N°: CI-ABJ-03-2022-B13-02828 – Tél. : +225 27 21 36 27 27 / 27 21 36 09 29 – Fax : 27 21 36 05 75"), 0, 1, 'C');
        // Ligne 3
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("E-mail: banacerf1@gmail.com - Compte Bancaire BDU N° : CI180 01010 020401144580 11"), 0, 1, 'C');
    }
}

// Créez un nouvel objet FPDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->AliasNbPages();

// $pdf->AliasNbPages();
$pdf->AddFont('BookAntiqua', '', 'bookantiqua.php'); // Pour le style normal
$pdf->AddFont('BookAntiqua', 'B', 'bookantiqua_bold.php'); // Pour le style gras, si disponible
$pdf->SetFont('BookAntiqua', '', 12);

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
        'font' => 'BookAntiqua'
    ],
    [
        'text' => 'BATIMENT-TRAVAUX PUBLICS',
        'size' => 13,
        'style' => '',
        'color' => [0, 0, 0],
        'font' => 'BookAntiqua'
    ],
    [
        'text' => 'RENOVATION ET TRAVAUX NEUF',
        'size' => 13,
        'style' => 'B',
        'color' => [255, 204, 0],
        'font' => 'BookAntiqua'
    ],
    [
        'text' => 'TUYAUTERIE-CHAUDRENERIE-CHARPENTE METALLIQUE',
        'size' => 14,
        'style' => '',
        'color' => [0, 0, 0],
        'font' => 'BookAntiqua'
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
$pdf->Ln($logoHeight + 0);

// // Positionnement pour le bloc client à droite sous l'entête
// $blocW = 63; // 1/3 de 190mm
// $blocX = 127; // 190 - 63 = 127, mais on laisse 10mm de marge à droite
// $blocY = $enteteY + $logoHeight + 5;

// Ajoute de l'espace avant le titre SOMMAIRE
// $pdf->Ln(5);

// Entête du sommaire
$pdf->SetFont('BookAntiqua', 'B', 28);
$pdf->Cell(0, 18, Utils::toMbConvertEncoding('SOMMAIRE'), 0, 1, 'C');
$pdf->Ln(10);

$interLineheight = 20;

// Liste des sections, centrées et espacées
$pdf->SetFont('BookAntiqua', '', 14);
$pdf->SetLeftMargin(30);
$pdf->Cell(0, $interLineheight, Utils::toMbConvertEncoding('1. Description des prestations........................................................................'), 0, 1, 'L');
$pdf->Cell(0, $interLineheight, Utils::toMbConvertEncoding('2. Délai de réalisation.....................................................................................'), 0, 1, 'L');
$pdf->Cell(0, $interLineheight, Utils::toMbConvertEncoding('3. Conditions Financières...............................................................................'), 0, 1, 'L');
$pdf->Cell(0, $interLineheight, Utils::toMbConvertEncoding('4. Décomposition des prix..............................................................................'), 0, 1, 'L');
$pdf->Cell(0, $interLineheight, Utils::toMbConvertEncoding('5. Garantie......................................................................................................'), 0, 1, 'L');
$pdf->SetLeftMargin(0);

ob_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="devis_' . $devis['id'] . '.pdf"');

$pdf->Output('I', 'devis_' . $devis['id'] . '.pdf');

unset($_SESSION['devisId']);

exit();
