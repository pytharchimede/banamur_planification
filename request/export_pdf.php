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

        // $this->Image('../img/logo_veritas.jpg', 150, 10, 30);
    }

    // Méthode pour le pied de page
    function Footer()
    {
        // Dessiner une ligne grise
        $this->SetDrawColor(0, 0, 0);
        $this->Line(10, 272, 200, 272);

        // Position at 1.5 cm from bottom
        $this->SetY(-22);

        // Arial italic 8
        $this->SetFont('Arial', '', 7);

        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("FOURNITURES INDUSTRIELLES, DEPANNAGE ET TRAVAUX PUBLIQUES - Au capital de 10 000 000 F CFA - Siège Social : Abidjan, Koumassi, Zone industrielle"), 0, 1, 'C');
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("01 BP 1642 Abidjan 01 - Téléphone : (+225) +225 27-21-36-27-27  -  Email : info@fidest.org - RCCM : CI-ABJ-2017-B-20163  -  N° CC : 010274200088"), 0, 1, 'C');

        // Page number
        $this->Cell(0, 10, Utils::toMbConvertEncoding('Page ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}

// Créez un nouvel objet FPDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->AliasNbPages();

// $pdf->AddFont('BookAntiqua', '', 'bookantiqua.php');
// $pdf->AddFont('BookAntiqua', 'B', 'bookantiqua_bold.php');
// $pdf->SetFont('BookAntiqua', '', 12);

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
$pdf->Ln($logoHeight + 5);

// Générer le QR code
$qrCodeData = 'https://fidest.ci/devis/request/export_pdf.php?devisId=' . $devis['id'];
$qrCodeFile = '../qrCodeFile/qrcode.png';
QRcode::png($qrCodeData, $qrCodeFile, 'L', 4, 2);

// Positionner le QR code 
$pdf->Image($qrCodeFile, 16, 62, 15);

// Positionnement pour le bloc client à droite sous l'entête
$blocW = 63; // 1/3 de 190mm
$blocX = 137; // 190 - 63 = 127, mais on laisse 10mm de marge à droite
$blocY = $enteteY + $logoHeight + 5;

// Ligne 1 : Date en français
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fra');
$dateEmission = strftime('%d %B %Y', strtotime($devis['date_emission']));
$ligne1 = "Abidjan, le $dateEmission";

// Ligne 2 : Nom du client
$ligne2 = $client['nom_client'];

// Ligne 3 : Localisation
$ligne3 = $client['localisation_client'];

// Ligne 4 : BP
$ligne4 = $client['bp_client'];

// Affichage
$pdf->SetXY($blocX, $blocY);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell($blocW, 7, Utils::toMbConvertEncoding($ligne1), 0, 2, 'C');

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell($blocW, 9, Utils::toMbConvertEncoding($ligne2), 0, 2, 'C');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell($blocW, 7, Utils::toMbConvertEncoding($ligne3), 0, 2, 'C');

$pdf->Cell($blocW, 7, Utils::toMbConvertEncoding($ligne4), 0, 2, 'C');

// Revenir à la position normale pour la suite
$pdf->Ln(5);




// Afficher la référence de l'offre comme titre, centré, grand, gras et encadré, avec retour à la ligne si besoin
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);

$refText = Utils::toMbConvertEncoding($offre['reference_offre']);

// Calcul largeur réelle du texte (max 150mm pour éviter d'aller trop loin sur la page)
$maxWidth = 180;
$textWidth = $pdf->GetStringWidth($refText) + 12; // 6mm padding de chaque côté
if ($textWidth > $maxWidth) $textWidth = $maxWidth;

// Positionner au centre
$pageWidth = 210 - 20; // A4 - marges (10mm de chaque côté)
$startX = 10 + ($pageWidth - $textWidth) / 2;

// Afficher le cadre autour du texte, MultiCell pour retour à la ligne
$pdf->SetX($startX);
$pdf->MultiCell($textWidth, 7, $refText, 1, 'C', true);

// Ajouter un petit espace après le bloc
$pdf->Ln(4);

$pdf->SetFont('Arial', '', 8);
// $pdf->SetFont('BookAntiqua', '', 8);

$pdf->Ln(10);

// Tableau des lignes du devis
$pdf->SetFont('Arial', 'B', 8);
// $pdf->SetFont('BookAntiqua', 'B', 8);

$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(169, 169, 169);

$pdf->Cell(10, 10, Utils::toMbConvertEncoding('N°'), 1, 0, 'C', true);
$pdf->Cell(65, 10, Utils::toMbConvertEncoding('Désignation'), 1, 0, 'C', true);
$pdf->Cell(20, 10, Utils::toMbConvertEncoding('Quantité'), 1, 0, 'C', true);
$pdf->Cell(25, 10, Utils::toMbConvertEncoding('Unité'), 1, 0, 'C', true);
$pdf->Cell(30, 10, Utils::toMbConvertEncoding('Prix unitaire'), 1, 0, 'C', true);
$pdf->Cell(30, 10, Utils::toMbConvertEncoding('Prix total'), 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(0, 0, 0);

// Vérifier s'il y a au moins un groupe renseigné
$hasGroup = false;
foreach ($lignes as $l) {
    if (!empty($l['groupe'])) {
        $hasGroup = true;
        break;
    }
}

$currentGroup = null;
$groupTotal = 0;
$pos = 1;

foreach ($lignes as $index => $ligne) {
    // Mode groupé
    if ($hasGroup) {
        // Nouveau groupe
        if ($ligne['groupe'] !== $currentGroup) {
            // Afficher le sous-total du groupe précédent si besoin
            if ($currentGroup !== null) {
                // $pdf->SetFont('BookAntiqua', 'B', 10);
                // Fusionne toutes les colonnes sauf la dernière (10+65+20+25+30 = 150mm)
                $pdf->Cell(150, 10, Utils::toMbConvertEncoding('SOUS-TOTAL ' . strtoupper($currentGroup)), 1, 0, 'C');
                // Colonne "Prix total" (30mm) pour le montant, bordure complète
                $pdf->Cell(30, 10, number_format($groupTotal, 0, ',', ' ') . ' XOF', 1, 1, 'C');
                $pdf->Ln(2);
            }
            // Afficher le titre du groupe si présent
            if (!empty($ligne['groupe'])) {
                // $pdf->SetFont('BookAntiqua', 'B', 10);
                $pdf->SetFillColor(230, 230, 230);
                $pdf->Cell(180, 8, Utils::toMbConvertEncoding(strtoupper($ligne['groupe'])), 1, 1, 'L', true);
                $pdf->SetFillColor(255, 255, 255);
            }
            $currentGroup = $ligne['groupe'];
            $groupTotal = 0;
        }
    }

    // Affichage de la ligne de devis (identique dans les deux cas)
    $unite = isset($unitesArray[$ligne['unite_id']]) ? '' . $unitesArray[$ligne['unite_id']]['symbole'] . '' : '';
    $pdf->SetFont('Arial', '', 8);
    // $pdf->SetFont('BookAntiqua', '', 8);
    $pdf->Cell(10, 10, $pos++, 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    // $pdf->AddFont('BookAntiqua', 'B', 8);
    $pdf->Cell(65, 10, Utils::toMbConvertEncoding($ligne['designation']), 1);
    $pdf->SetFont('Arial', '', 8);
    // $pdf->AddFont('BookAntiqua', '', 8);
    $pdf->Cell(20, 10, $ligne['quantite'], 1, 0, 'C'); // quantité centrée
    $pdf->Cell(25, 10, Utils::toMbConvertEncoding($unite), 1, 0, 'C'); // unité centrée
    $pdf->Cell(30, 10, number_format($ligne['prix'], 0, ',', ' ') . ' XOF', 1, 0, 'C'); // prix unitaire centré
    $pdf->Cell(30, 10, number_format($ligne['total'], 0, ',', ' ') . ' XOF', 1, 1, 'C'); // total centré

    // Additionner au sous-total du groupe
    if ($hasGroup) {
        $groupTotal += $ligne['total'];
        // Si c'est la dernière ligne, afficher le sous-total du groupe si besoin
        if ($index === array_key_last($lignes) && $currentGroup !== null) {
            // $pdf->SetFont('BookAntiqua', 'B', 10);
            // Fusionne toutes les colonnes sauf la dernière (10+65+20+25+30 = 150mm)
            $pdf->Cell(150, 10, Utils::toMbConvertEncoding('SOUS-TOTAL ' . strtoupper($currentGroup)), 1, 0, 'C');
            // Colonne "Prix total" (30mm) pour le montant, bordure complète
            $pdf->Cell(30, 10, number_format($groupTotal, 0, ',', ' ') . ' XOF', 1, 1, 'C');
            $pdf->Ln(2);
        }
    }
}

// Ligne Montant HT
// $pdf->SetFont('BookAntiqua', 'B', 10);
$pdf->Cell(150, 10, Utils::toMbConvertEncoding('MONTANT HT'), 1, 0, 'C');
$pdf->Cell(30, 10, number_format($devis['total_ht'], 0, ',', ' ') . ' XOF', 1, 1, 'C');

// Ligne TVA si facturable
if ($devis['tva_facturable'] == 1) {
    $pdf->Cell(150, 10, Utils::toMbConvertEncoding('TVA 18%'), 1, 0, 'C');
    $pdf->Cell(30, 10, number_format($devis['tva'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
}

// Ligne Montant TTC
$pdf->Cell(150, 10, Utils::toMbConvertEncoding('MONTANT TTC'), 1, 0, 'C');
$pdf->Cell(30, 10, number_format($devis['total_ttc'], 0, ',', ' ') . ' XOF', 1, 1, 'C');

$pdf->Ln(20);

ob_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="devis_' . $devis['id'] . '.pdf"');

$pdf->Output('I', 'devis_' . $devis['id'] . '.pdf');

unset($_SESSION['devisId']);

exit();
