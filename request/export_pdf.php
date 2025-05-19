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
        $this->SetDrawColor(0, 0, 0);
        $this->Line(10, 272, 200, 272);

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
        $this->Cell(0, 10, Utils::toMbConvertEncoding('Page ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }

    // Méthode pour calculer le nombre de lignes qu'occupera un MultiCell
    function NbLines($w, $txt)
    {
        // Calcule le nombre de lignes qu'occupera un MultiCell de largeur $w pour $txt
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}

// Créez un nouvel objet FPDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->AliasNbPages();

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
$blocX = 127; // 190 - 63 = 127, mais on laisse 10mm de marge à droite
$blocY = $enteteY + $logoHeight + 5;

// Ligne 1 : Date en français (remplace strftime)
$fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Africa/Abidjan', IntlDateFormatter::GREGORIAN, 'dd MMMM yyyy');
$dateEmission = $fmt->format(new DateTime($devis['date_emission']));
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

$refText = Utils::toMbConvertEncoding(strtoupper($offre['reference_offre']));

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
$pdf->Ln(8);

// Tableau des lignes du devis
$pdf->SetFont('Arial', 'B', 12);


$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetDrawColor(169, 169, 169);

$tableHeight = 7;

$pdf->Cell(10, $tableHeight, Utils::toMbConvertEncoding('N°'), 1, 0, 'C', true);
$pdf->Cell(65, $tableHeight, Utils::toMbConvertEncoding('Désignation'), 1, 0, 'C', true);
$pdf->Cell(20, $tableHeight, Utils::toMbConvertEncoding('Qté'), 1, 0, 'C', true);
$pdf->Cell(25, $tableHeight, Utils::toMbConvertEncoding('U'), 1, 0, 'C', true);
$pdf->Cell(30, $tableHeight, Utils::toMbConvertEncoding('PU'), 1, 0, 'C', true);
$pdf->Cell(40, $tableHeight, Utils::toMbConvertEncoding('PT'), 1, 0, 'C', true);
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
                $pdf->Cell(40, 10, number_format($groupTotal, 0, ',', ' ') . ' XOF', 1, 1, 'C');
                $pdf->Ln(2);
            }
            // Afficher le titre du groupe si présent
            if (!empty($ligne['groupe'])) {
                // $pdf->SetFont('BookAntiqua', 'B', 10);
                $pdf->SetFillColor(230, 230, 230);
                // Colonne N° sans bordure droite, même couleur de fond
                $pdf->Cell(10, 8, '', 'LTB', 0, '', true); // L=Left, T=Top, B=Bottom (pas de R=Right)
                // Colonne groupe, bordure complète, même couleur de fond
                $pdf->Cell(180, 8, Utils::toMbConvertEncoding(strtoupper($ligne['groupe'])), 'R', 1, 'L', true);
                $pdf->SetFillColor(255, 255, 255);
            }
            $currentGroup = $ligne['groupe'];
            $groupTotal = 0;
        }
    }

    // Limite de caractères pour la désignation
    $maxChars = 40;

    // Largeurs des colonnes
    $w = [10, 65, 20, 25, 30, 40];
    $lineHeight = 7;

    // Préparer la désignation
    $designationFull = Utils::toMbConvertEncoding($ligne['designation']);
    if (mb_strlen($designationFull) > $maxChars) {
        $designation = mb_substr($designationFull, 0, $maxChars - 3) . '...';
    } else {
        $designation = $designationFull;
    }

    // Calculer la hauteur (1 ligne)
    $cellHeight = $lineHeight;

    // Sauvegarder la position X/Y de départ
    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // N°
    $pdf->SetXY($x, $y);
    $pdf->Cell($w[0], $cellHeight, $pos++, 1, 0, 'C');

    // Désignation (une seule ligne)
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->SetXY($x + $w[0], $y);
    $pdf->Cell($w[1], $cellHeight, $designation, 1, 0, 'L');
    $pdf->SetFont('Arial', '', 8);

    // Qté
    $pdf->SetXY($x + $w[0] + $w[1], $y);
    $pdf->Cell($w[2], $cellHeight, $ligne['quantite'], 1, 0, 'C');
    // U
    $pdf->SetXY($x + $w[0] + $w[1] + $w[2], $y);
    $pdf->Cell($w[3], $cellHeight, Utils::toMbConvertEncoding($unite), 1, 0, 'C');
    // PU
    $pdf->SetXY($x + $w[0] + $w[1] + $w[2] + $w[3], $y);
    $pdf->Cell($w[4], $cellHeight, number_format($ligne['prix'], 0, ',', ' ') . ' XOF', 1, 0, 'C');
    // PT
    $pdf->SetXY($x + $w[0] + $w[1] + $w[2] + $w[3] + $w[4], $y);
    $pdf->Cell($w[5], $cellHeight, number_format($ligne['total'], 0, ',', ' ') . ' XOF', 1, 0, 'C');

    // Se placer tout à gauche, à la nouvelle ligne
    $pdf->SetXY($x, $y + $cellHeight);

    // Additionner au sous-total du groupe
    if ($hasGroup) {
        $groupTotal += $ligne['total'];
        // Si c'est la dernière ligne, afficher le sous-total du groupe si besoin
        if ($index === array_key_last($lignes) && $currentGroup !== null) {
            $pdf->SetFont('Arial', 'B', 10);

            // Fusionne toutes les colonnes sauf la dernière (10+65+20+25+30 = 150mm)
            $pdf->Cell(150, 8, Utils::toMbConvertEncoding('SOUS-TOTAL ' . strtoupper($currentGroup)), 1, 0, 'C');
            // Colonne "Prix total" (30mm) pour le montant, bordure complète
            $pdf->Cell(40, 8, number_format($groupTotal, 0, ',', ' ') . ' XOF', 1, 1, 'C');
            $pdf->Ln(2);
        }
    }
}

// Ligne Montant HT
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(150, 8, Utils::toMbConvertEncoding('MONTANT HT'), 1, 0, 'C');
$pdf->Cell(40, 8, number_format($devis['total_ht'], 0, ',', ' ') . ' XOF', 1, 1, 'C');

// Ligne TVA
if ($devis['tva_facturable'] == 1) {
    $pdf->Cell(150, 8, Utils::toMbConvertEncoding('TVA 18%'), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($devis['tva'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
} else {
    // Libellé explicite
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->Cell(150, 8, Utils::toMbConvertEncoding('TVA 18% (non facturée)'), 1, 0, 'C');
    // Montant barré (simulateur: affiche en gris, italique, entre parenthèses)
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->SetTextColor(180, 180, 180);
    $pdf->Cell(40, 8, '(' . number_format(0.18 * $devis['total_ht'], 0, ',', ' ') . ' XOF)', 1, 1, 'C');
    // Remettre police normale et couleur noire
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0, 0, 0);
}

// Ligne Montant TTC ou NAP
if ($devis['tva_facturable'] == 1) {
    $pdf->Cell(150, 8, Utils::toMbConvertEncoding('MONTANT TTC'), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($devis['total_ttc'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
    $montantAvantRemise = $devis['total_ttc'];
} else {
    $pdf->Cell(150, 8, Utils::toMbConvertEncoding('MONTANT NET À PAYER'), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($devis['total_ht'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
    $montantAvantRemise = $devis['total_ht'];
}

// Si remise, afficher la ligne de remise et le net à payer
if (!empty($devis['remise']) && $devis['remise'] != 0) {
    $remisePourcent = floatval($devis['remise']);
    $montantRemise = $montantAvantRemise * $remisePourcent / 100;
    $montantNetAPayer = $montantAvantRemise - $montantRemise;

    // Ligne Remise
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 102, 204);
    $pdf->Cell(150, 8, Utils::toMbConvertEncoding('REMISE (' . number_format($remisePourcent, 2, ',', ' ') . ' %)'), 1, 0, 'C');
    $pdf->Cell(40, 8, '- ' . number_format($montantRemise, 0, ',', ' ') . ' XOF', 1, 1, 'C');

    // Ligne Net à Payer
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(150, 8, Utils::toMbConvertEncoding('NET À PAYER'), 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($montantNetAPayer, 0, ',', ' ') . ' XOF', 1, 1, 'C');
} else {
    $montantNetAPayer = $montantAvantRemise;
}

$pdf->Ln(5);

// Ligne 1 : Arrêtée la présente facture à la somme de :
$pdf->SetFont('Arial', 'U', 8);
$pdf->Cell(0, 6, Utils::toMbConvertEncoding("Arrêtée le présent devis à la somme de :"), 0, 1, 'L');

// Ligne 2 : Montant en lettres (net à payer)
$montantLettre = Utils::montantEnLettre($montantNetAPayer);
$pdf->SetFont('Arial', 'B', 10);
$pdf->MultiCell(0, 6, Utils::toMbConvertEncoding(strtoupper($montantLettre)), 0, 'L');

// Espace pour la signature du Directeur Technique
$pdf->Ln(5); // espace avant la zone de signature

// Position horizontale à droite (ajuste si besoin)
$signatureX = 130;
$pdf->SetXY($signatureX, $pdf->GetY());

// "DIRECTEUR TECHNIQUE" en majuscule, souligné
$pdf->SetFont('Arial', 'U', 10);
$pdf->Cell(70, 7, Utils::toMbConvertEncoding('DIRECTEUR TECHNIQUE'), 0, 2, 'C');

// Nom du directeur technique (remplace par le vrai nom si besoin)
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(70, 8, Utils::toMbConvertEncoding('NOM DU DIRECTEUR'), 0, 2, 'C');

// Espace pour cachet et signature
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(70, 20, Utils::toMbConvertEncoding('(Cachet et signature)'), 0, 2, 'C');

ob_clean();

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="devis_' . $devis['id'] . '.pdf"');

$pdf->Output('I', 'devis_' . $devis['id'] . '.pdf');

unset($_SESSION['devisId']);

exit();
