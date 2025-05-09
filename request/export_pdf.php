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

        $this->Image('../img/logo_veritas.jpg', 150, 10, 30);
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

$pdf->SetFont('Arial', '', 10);
$pdf->SetFont('BookAntiqua', '', 10);
$pdf->Cell(0, 10, Utils::toMbConvertEncoding('   à l\'attention de ' . $devis['correspondant']), 0, 1, 'L');

$pdf->SetFont('Arial', '', 8);
$pdf->SetFont('BookAntiqua', '', 8);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Pour faire suite a votre demande, '), 0, 1, 'L');
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('nous vous prions de bien vouloir trouver ci-dessous notre meilleur proposition.'), 0, 1, 'L');
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Nous restons à votre  entière disposition pour toute information complémentaire.'), 0, 1, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->SetFont('BookAntiqua', '', 8);

$pdf->Ln(10);



// Tableau des lignes du devis
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetFont('BookAntiqua', 'B', 8);

$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(169, 169, 169);

$pdf->Cell(10, 10, Utils::toMbConvertEncoding('Pos.'), 1, 0, 'C', true);
$pdf->Cell(65, 10, Utils::toMbConvertEncoding('Description'), 1, 0, 'C', true);
$pdf->Cell(20, 10, Utils::toMbConvertEncoding('Quantité'), 1, 0, 'C', true);
$pdf->Cell(25, 10, Utils::toMbConvertEncoding('Unité'), 1, 0, 'C', true);
$pdf->Cell(30, 10, Utils::toMbConvertEncoding('Prix unitaire'), 1, 0, 'C', true);
$pdf->Cell(30, 10, Utils::toMbConvertEncoding('Prix total'), 1, 0, 'C', true);
$pdf->Ln();

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetDrawColor(0, 0, 0);

$pdf->SetFont('Arial', '', 8);
$pdf->SetFont('BookAntiqua', '', 8);
foreach ($lignes as $i => $ligne) {
    $unite = isset($unitesArray[$ligne['unite_id']]) ? $unitesArray[$ligne['unite_id']]['libelle'] . ' (' . $unitesArray[$ligne['unite_id']]['symbole'] . ')' : '';
    $pdf->Cell(10, 10, $i + 1, 1);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->AddFont('BookAntiqua', 'B', 8);
    $pdf->Cell(65, 10, Utils::toMbConvertEncoding($ligne['designation']), 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->AddFont('BookAntiqua', '', 8);
    $pdf->Cell(20, 10, $ligne['quantite'], 1);
    $pdf->Cell(25, 10, Utils::toMbConvertEncoding($unite), 1);
    $pdf->Cell(30, 10, number_format($ligne['prix'], 0, ',', ' ') . ' XOF', 1);
    $pdf->Cell(30, 10, number_format($ligne['total'], 0, ',', ' ') . ' XOF', 1);
    $pdf->Ln();
}

$pdf->Ln(5);
$pdf->SetDrawColor(0, 0, 0);
$pdf->Cell(190, 0, '', 'T');
$pdf->SetDrawColor(255, 255, 255);

$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 8);
$pdf->AddFont('BookAntiqua', 'B', 8);
$pdf->Cell(115, 10, '', 0);
$pdf->Cell(45, 10, Utils::toMbConvertEncoding('Montant HT'), 1);
$pdf->Cell(30, 10, number_format($devis['total_ht'], 0, ',', ' ') . ' XOF', 1);
$pdf->Ln();
if ($devis['tva_facturable'] == 1) {
    $pdf->Cell(115, 10, '', 0);
    $pdf->Cell(45, 10, Utils::toMbConvertEncoding('TVA 18%'), 1);
    $pdf->Cell(30, 10, number_format($devis['tva'], 0, ',', ' ') . ' XOF', 1);
    $pdf->Ln();
}
$pdf->Cell(115, 10, '', 0);
$pdf->Cell(45, 10, Utils::toMbConvertEncoding('Montant TTC'), 1);
$pdf->Cell(30, 10, number_format($devis['total_ttc'], 0, ',', ' ') . ' XOF', 1);

$pdf->Ln(20);

// Ajouter les conditions
$pdf->SetFont('Arial', 'BU', 8);
$pdf->SetFont('BookAntiqua', 'BU', 8);
$pdf->Cell(25, 5, Utils::toMbConvertEncoding('Validité de l\'offre:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('30 jours'), 0, 1, 'L');

$pdf->SetFont('Arial', 'BU', 8);
$pdf->SetFont('BookAntiqua', 'BU', 8);
$pdf->Cell(26, 5, Utils::toMbConvertEncoding('Délai de livraison:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding($devis['delai_livraison']), 0, 1, 'L');

$pdf->SetFont('Arial', 'BU', 8);
$pdf->SetFont('BookAntiqua', 'BU', 8);
$pdf->Cell(35, 5, Utils::toMbConvertEncoding('Conditions de règlement:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->SetFont('BookAntiqua', '', 8);
$pdf->Cell(0, 5, Utils::toMbConvertEncoding('Habituelle entre nous'), 0, 1, 'L');

$pdf->Ln(5);

// Ajouter les signatures
$pdf->SetFont('BookAntiqua', 'BU', 10);
$pdf->Cell(5);
$pdf->Cell(80, 10, Utils::toMbConvertEncoding('Directeur Technique (Nom et Signature)'), 0, 0, 'L');
$pdf->Cell(100, 10, Utils::toMbConvertEncoding('Directeur Général (Nom et Signature)'), 0, 1, 'R');

$pdf->Ln(1);

$signatureTechnique = '../signatures/' . $directeurTechnique['signature'];
$signatureGeneral = '../signatures/' . $directeurGeneral['signature'];

$pdf->Cell(5);

// Signature Directeur Technique
if ($devisObj->isValidTechnique($devisId)) {
    list($widthTechnique, $heightTechnique) = getimagesize($signatureTechnique);
    $aspectRatioTechnique = $widthTechnique / $heightTechnique;
    $maxWidthTechnique = 150;
    $maxHeightTechnique = 50;

    if ($aspectRatioTechnique > 1) {
        $newWidthTechnique = $maxWidthTechnique;
        $newHeightTechnique = $maxWidthTechnique / $aspectRatioTechnique;
    } else {
        $newHeightTechnique = $maxHeightTechnique;
        $newWidthTechnique = $maxHeightTechnique * $aspectRatioTechnique;
    }
    $pdf->Cell(80, 30, $pdf->Image($signatureTechnique, $pdf->GetX() + ($maxWidthTechnique - $newWidthTechnique) / 2 - 30, $pdf->GetY() + ($maxHeightTechnique - $newHeightTechnique) / 2, $newWidthTechnique, $newHeightTechnique), 1, 0, 'C');
} else {
    $pdf->Cell(80, 30, '', 1, 0, 'C');
}

$pdf->Cell(30);

// Signature Directeur Général
if ($devisObj->isValidGenerale($devisId)) {
    list($widthGeneral, $heightGeneral) = getimagesize($signatureGeneral);
    $aspectRatioGeneral = $widthGeneral / $heightGeneral;
    $maxWidthGeneral = 60;
    $maxHeightGeneral = 20;

    if ($aspectRatioGeneral > 1) {
        $newWidthGeneral = $maxWidthGeneral;
        $newHeightGeneral = $maxWidthGeneral / $aspectRatioGeneral;
    } else {
        $newHeightGeneral = $maxHeightGeneral;
        $newWidthGeneral = $maxHeightGeneral * $aspectRatioGeneral;
    }

    $pdf->SetY($pdf->GetY() + 10);
    $pdf->Cell(125);
    $pdf->Cell(80, 30, $pdf->Image($signatureGeneral, $pdf->GetX() + ($maxWidthGeneral - $newWidthGeneral) / 2, $pdf->GetY() + ($maxHeightGeneral - $newHeightGeneral) / 2, $newWidthGeneral, $newHeightGeneral), 1, 1, 'C');
} else {
    $pdf->Cell(80, 30, '', 1, 1, 'C');
}

$pdf->SetFont('BookAntiqua', '', 10);
$pdf->Cell(5);
if ($devisObj->isValidTechnique($devisId)) {
    $pdf->Cell(80, 10, Utils::toMbConvertEncoding($directeurTechnique['prenom'] . ' ' . $directeurTechnique['nom']), 0, 0, 'L');
} else {
    $pdf->Cell(80, 10, Utils::toMbConvertEncoding('En Attente de validation...'), 0, 0, 'L');
}

if ($devisObj->isValidGenerale($devisId)) {
    $pdf->Cell(100, 10, Utils::toMbConvertEncoding($directeurGeneral['prenom'] . ' ' . $directeurGeneral['nom']), 0, 1, 'R');
} else {
    $pdf->Cell(80, 10, Utils::toMbConvertEncoding('En Attente de validation...'), 0, 0, 'L');
}

$pdf->Cell(5);
$pdf->Cell(80, 30, '', 1, 0, 'C');
$pdf->Cell(10);
$pdf->Cell(80, 30, '', 1, 1, 'C');

ob_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="devis_' . $devis['id'] . '.pdf"');

$pdf->Output('I', 'devis_' . $devis['id'] . '.pdf');

unset($_SESSION['devisId']);

exit();
