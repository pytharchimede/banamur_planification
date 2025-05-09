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

$pdf->AddFont('BookAntiqua', '', 'bookantiqua.php');
$pdf->AddFont('BookAntiqua', 'B', 'bookantiqua_bold.php');
$pdf->SetFont('BookAntiqua', '', 12);

// Générer le QR code
$qrCodeData = 'https://fidest.ci/devis/request/export_pdf.php?devisId=' . $devis['id'];
$qrCodeFile = '../qrCodeFile/qrcode.png';
QRcode::png($qrCodeData, $qrCodeFile, 'L', 4, 2);

// Ajouter le logo à gauche
if (isset($devis['logo']) && $devis['logo'] != '') {
    $pdf->Image('../logo/' . $devis['logo'], 10, 10, 40);
}

// Ajouter le QR code à droite du logo
$pdf->Image($qrCodeFile, 180, 10, 20);

// Positionnement individuel des informations de BANAMUR INDUSTRIES & TECH
$pdf->SetFont('Arial', 'B', 10);

// Positionnement individuel des informations de SIFCA
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFont('BookAntiqua', 'B', 10);
$pdf->SetXY(10, 50);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding(strtoupper($client['nom_client'])), 0, 1, 'L');

$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(10, 55);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($client['localisation_client']), 0, 1, 'L');

$pdf->SetXY(10, 60);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($client['commune_client']), 0, 1, 'L');

$pdf->SetXY(10, 65);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($client['bp_client']), 0, 1, 'L');

$pdf->SetXY(10, 70);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($client['pays_client']), 0, 1, 'L');

// Positionnement individuel des informations du devis
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFont('BookAntiqua', 'B', 10);
$pdf->SetXY(135, 50);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('N° d\'offre: ' . $offre['num_offre']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 8);
$pdf->AddFont('BookAntiqua', '', 8);
$pdf->SetXY(135, 55);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Date: ' . Utils::dateEnToutesLettres($offre['date_offre'])), 0, 1, 'L');

$pdf->SetXY(135, 60);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Référence: ' . $offre['reference_offre']), 0, 1, 'L');
/*
$pdf->SetXY(150, 65);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Votre numéro client: 1064'), 0, 1, 'L');
*/
$pdf->SetXY(135, 65);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Votre interlocuteur: ' . $offre['commercial_dedie']), 0, 1, 'L');

$pdf->Ln(10);

// Ajouter les informations concernant le devis juste en dessous des trois colonnes
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFont('BookAntiqua', 'B', 12);
$pdf->Cell(50, 10, Utils::toMbConvertEncoding('Devis N° ' . $devis['numero_devis']), 0, 0, 'L');


$pdf->SetFont('Arial', '', 8);
$pdf->SetFont('BookAntiqua', '', 8);

$pdf->Ln(10);

// Tableau des lignes du devis
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFont('BookAntiqua', 'B', 8);

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
                $pdf->SetFont('BookAntiqua', 'B', 10);
                // Fusionne toutes les colonnes sauf la dernière (10+65+20+25+30 = 150mm)
                $pdf->Cell(150, 10, Utils::toMbConvertEncoding('SOUS-TOTAL ' . strtoupper($currentGroup)), 1, 0, 'C');
                // Colonne "Prix total" (30mm) pour le montant, bordure complète
                $pdf->Cell(30, 10, number_format($groupTotal, 0, ',', ' ') . ' XOF', 1, 1, 'C');
                $pdf->Ln(2);
            }
            // Afficher le titre du groupe si présent
            if (!empty($ligne['groupe'])) {
                $pdf->SetFont('BookAntiqua', 'B', 10);
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
    $pdf->SetFont('BookAntiqua', '', 8);
    $pdf->Cell(10, 10, $pos++, 1, 0, 'C');
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->AddFont('BookAntiqua', 'B', 8);
    $pdf->Cell(65, 10, Utils::toMbConvertEncoding($ligne['designation']), 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->AddFont('BookAntiqua', '', 8);
    $pdf->Cell(20, 10, $ligne['quantite'], 1, 0, 'C'); // quantité centrée
    $pdf->Cell(25, 10, Utils::toMbConvertEncoding($unite), 1, 0, 'C'); // unité centrée
    $pdf->Cell(30, 10, number_format($ligne['prix'], 0, ',', ' ') . ' XOF', 1, 0, 'C'); // prix unitaire centré
    $pdf->Cell(30, 10, number_format($ligne['total'], 0, ',', ' ') . ' XOF', 1, 1, 'C'); // total centré

    // Additionner au sous-total du groupe
    if ($hasGroup) {
        $groupTotal += $ligne['total'];
        // Si c'est la dernière ligne, afficher le sous-total du groupe si besoin
        if ($index === array_key_last($lignes) && $currentGroup !== null) {
            $pdf->SetFont('BookAntiqua', 'B', 10);
            // Fusionne toutes les colonnes sauf la dernière (10+65+20+25+30 = 150mm)
            $pdf->Cell(150, 10, Utils::toMbConvertEncoding('SOUS-TOTAL ' . strtoupper($currentGroup)), 1, 0, 'C');
            // Colonne "Prix total" (30mm) pour le montant, bordure complète
            $pdf->Cell(30, 10, number_format($groupTotal, 0, ',', ' ') . ' XOF', 1, 1, 'C');
            $pdf->Ln(2);
        }
    }
}

// Ligne Montant HT
$pdf->SetFont('BookAntiqua', 'B', 10);
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
