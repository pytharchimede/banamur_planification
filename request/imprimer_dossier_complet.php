<?php
session_start();
require_once('../vendor/autoload.php');
include_once '../header/header_export_pdf.php';
require_once("../model/Utils.php");

// Récupération des données nécessaires
$devisId = $_GET['devisId'] ?? $_SESSION['devisId'] ?? null;
// ... Récupère $devis, $client, $offre selon ta logique ...

class CustomPDF extends TCPDF
{
    public $logoPath;
    public $enteteTexts = [];

    public function Header()
    {
        // Logo à gauche
        $logoW = 35;
        $logoH = 35;
        $this->Image($this->logoPath, 15, 13, $logoW, $logoH);

        // Textes à droite du logo, alignés verticalement
        $x = 15 + $logoW + 8;
        $y = 15;
        $this->SetXY($x, $y);

        $this->SetFont('helvetica', 'B', 14);
        $this->Cell(0, 7, $this->enteteTexts[0], 0, 2, 'C');
        $this->SetFont('helvetica', '', 11);
        $this->Cell(0, 6, $this->enteteTexts[1], 0, 2, 'C');
        $this->SetFont('helvetica', 'B', 11);
        $this->SetTextColor(255, 204, 0);
        $this->Cell(0, 6, $this->enteteTexts[2], 0, 2, 'C');
        $this->SetFont('helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 6, $this->enteteTexts[3], 0, 2, 'C');
        $this->Ln(2);

        // $this->SetDrawColor(0, 0, 0);
        // $this->Line($x, $this->GetY(), $this->getPageWidth() - 15, $this->GetY());
    }

    public function Footer()
    {
        // Ligne noire
        $this->SetDrawColor(0, 0, 0);
        // $this->Line(10, 287, 200, 287); // 287 ~ 1.5cm du bas pour A4 portrait

        // Position à ~3cm du bas pour afficher toutes les lignes
        $this->SetY(-18);

        // Police
        $this->SetFont('helvetica', '', 7);
        $this->SetTextColor(0, 0, 0);
        // Ligne 1
        $this->Cell(0, 3.5, "S.A.R.L au Capital de 100 000 000 FCFA - Siège Social: Abidjan, Koumassi Bd. du Gabon prolongé – 01 BP 1642 Abidjan 01", 0, 1, 'C');
        // Ligne 2
        $this->Cell(0, 3.5, "RCCM N°: CI-ABJ-03-2022-B13-02828 – Tél. : +225 27 21 36 27 27 / 27 21 36 09 29 – Fax : 27 21 36 05 75", 0, 1, 'C');
        // Ligne 3
        $this->Cell(0, 3.5, "E-mail: banacerf1@gmail.com - Compte Bancaire BDU N° : CI180 01010 020401144580 11", 0, 1, 'C');

        // Numéro de page
        // $this->Cell(0, 8, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

// Création du PDF
$pdf = new CustomPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->logoPath = '../logo/' . ($devis['logo'] ?? 'default_logo.jpg');
$pdf->enteteTexts = [
    'BANAMUR INDUSTRIES ET TECHNOLOGIES',
    'BATIMENT-TRAVAUX PUBLICS',
    'RENOVATION ET TRAVAUX NEUF',
    'TUYAUTERIE-CHAUDRONNERIE-CHARPENTE METALLIQUE'
];
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('BANAMUR INDUSTRIES & TECH');
$pdf->SetTitle('Dossier Complet Devis');
$pdf->SetMargins(15, 65, 15); // top margin augmenté pour l'entête
$pdf->SetAutoPageBreak(true, 25); // 25 mm de marge basse pour laisser la place au footer

// Style CSS global
$style = '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }
    table, th, td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
';

// ----------- PAGE DE GARDE -----------
$pdf->AddPage();
$pdf->Ln(0); // espace sous l'entête

// Titre de l'offre encadré
$pdf->SetFont('helvetica', 'B', 24);
$pdf->Cell(0, 14, 'OFFRE COMMERCIALE', 0, 1, 'C');
$pdf->Ln(3);

// Référence offre dans un cadre auto-adapté
$pdf->SetFont('helvetica', 'B', 18);
$ref = strtoupper($offre['reference_offre']);
$refW = $pdf->GetStringWidth($ref) + 16;
$pdf->SetX(($pdf->GetPageWidth() - $refW) / 2);
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.7);
$pdf->Cell($refW, 14, $ref, 1, 1, 'C');
$pdf->SetLineWidth(0.2);
$pdf->Ln(8);

// Présentée à
$pdf->SetFont('helvetica', '', 13);
$pdf->Cell(0, 8, 'Présentée à', 0, 1, 'C');
$pdf->Ln(8);

// Logo client ou nom
$clientLogoPath = '../uploads/logos_clients/' . ($client['logo_client'] ?? 'default_logo.jpg');
if ($clientLogoPath && file_exists($clientLogoPath)) {
    $pdf->Image($clientLogoPath, ($pdf->GetPageWidth() - 50) / 2, $pdf->GetY(), 50);
    $pdf->Ln(55);
} else {
    $pdf->SetFont('helvetica', 'B', 38);
    $pdf->Cell(0, 30, $client['nom_client'], 0, 1, 'C');
    $pdf->Ln(5);
}
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 5, 'Contact : ' . $devis['correspondant'] . ' - ' . $client['nom_client'], 0, 1, 'C');
$pdf->Cell(0, 5, 'Adresse : ' . $client['localisation_client'], 0, 1, 'C');
$pdf->Cell(0, 5, $client['bp_client'], 0, 1, 'C');
$pdf->Ln(35);
$pdf->Cell(0, 8, 'Dénommé le « Client »', 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 5, "Référence de l’offre:", 0, 1, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 5, strtoupper($offre['num_offre']), 0, 1, 'L');
$pdf->Ln(10);

// ----------- SOMMAIRE -----------
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 28);
$pdf->Cell(0, 18, 'SOMMAIRE', 0, 1, 'C');
$pdf->Ln(20);
$pdf->SetFont('helvetica', '', 14);
$pdf->SetLeftMargin(30);
$lineHeightSommaire = 20;
$pdf->Cell(0, $lineHeightSommaire, '1. Description des prestations........................................................................', 0, 1, 'L');
$pdf->Cell(0, $lineHeightSommaire, '2. Délai de réalisation.....................................................................................', 0, 1, 'L');
$pdf->Cell(0, $lineHeightSommaire, '3. Conditions Financières...............................................................................', 0, 1, 'L');
$pdf->Cell(0, $lineHeightSommaire, '4. Décomposition des prix..............................................................................', 0, 1, 'L');
$pdf->Cell(0, $lineHeightSommaire, '5. Garantie......................................................................................................', 0, 1, 'L');
$pdf->SetLeftMargin(15);
$pdf->Ln(10);

// ----------- SECTION HTML (Description, Délai, Conditions) -----------
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 0, 0);

// Construction du HTML avec titres personnalisés
$html = $style;

// 1. Description des prestations (numéro non souligné, texte souligné)
$html .= '
<span style="font-size:18px;">
  <span>1.&nbsp;</span>
  <span style="text-decoration: underline;">Description des prestations</span>
</span>
<br><div style="margin-bottom: 18px;">' . $devis['description'] . '</div>';

// 2. Délai de réalisation
$html .= '
<span style="font-size:18px;">
  <span>2.&nbsp;</span>
  <span style="text-decoration: underline;">Délai de réalisation</span>
</span>
<br><div style="margin-bottom: 18px;">' . $devis['delai'] . '</div>';

// 3. Conditions Financières
$html .= '
<span style="font-size:18px;">
  <span>3.&nbsp;</span>
  <span style="text-decoration: underline;">Conditions Financières</span>
</span>
<br><div style="margin-bottom: 18px;">' . $devis['conditions'] . '</div>';

// Forcer les bordures noires sur tous les tableaux HTML
$html = preg_replace(
    '/<table([^>]*)>/i',
    '<table$1 border="1" cellpadding="4" style="border-collapse:collapse; border:1px solid #000;">',
    $html
);

$pdf->SetDrawColor(0, 0, 0); // Bordures noires pour les tableaux
$pdf->writeHTML($html, true, false, true, false, '');

// ----------- SECTION DÉCOMPOSITION DES PRIX -----------
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 12, '4. Décomposition des prix', 0, 1, 'L');
$pdf->Ln(4);

// En-tête du tableau
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(10, 8, 'N°', 1, 0, 'C', true);
$pdf->Cell(65, 8, 'Désignation', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Qté', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'U', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'PU', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'PT', 1, 1, 'C', true);

// Corps du tableau
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(255, 255, 255);

$pos = 1;
foreach ($lignes as $ligne) {
    $unite = isset($unitesArray[$ligne['unite_id']]) ? $unitesArray[$ligne['unite_id']]['symbole'] : '';
    $pdf->Cell(10, 7, $pos++, 1, 0, 'C');
    $pdf->Cell(65, 7, Utils::toMbConvertEncoding($ligne['designation']), 1);
    $pdf->Cell(20, 7, $ligne['quantite'], 1, 0, 'C');
    $pdf->Cell(25, 7, Utils::toMbConvertEncoding($unite), 1, 0, 'C');
    $pdf->Cell(30, 7, number_format($ligne['prix'], 0, ',', ' ') . ' XOF', 1, 0, 'C');
    $pdf->Cell(40, 7, number_format($ligne['total'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
}

// Total HT
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(150, 8, 'MONTANT HT', 1, 0, 'C');
$pdf->Cell(40, 8, number_format($devis['total_ht'], 0, ',', ' ') . ' XOF', 1, 1, 'C');

// TVA
if ($devis['tva_facturable'] == 1) {
    $pdf->Cell(150, 8, 'TVA 18%', 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($devis['tva'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
} else {
    $pdf->SetFont('helvetica', 'I', 10);
    $pdf->SetTextColor(120, 120, 120);
    $pdf->Cell(150, 8, 'TVA 18% (non facturée)', 1, 0, 'C');
    $pdf->Cell(40, 8, '(' . number_format(0.18 * $devis['total_ht'], 0, ',', ' ') . ' XOF)', 1, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetTextColor(0, 0, 0);
}

// Total TTC ou Net à payer
if ($devis['tva_facturable'] == 1) {
    $pdf->Cell(150, 8, 'MONTANT TTC', 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($devis['total_ttc'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
} else {
    $pdf->Cell(150, 8, 'MONTANT NET À PAYER', 1, 0, 'C');
    $pdf->Cell(40, 8, number_format($devis['total_ht'], 0, ',', ' ') . ' XOF', 1, 1, 'C');
}

$pdf->Ln(5);

// Montant en lettres
$pdf->SetFont('helvetica', 'U', 9);
$pdf->Cell(0, 6, "Arrêtée le présent devis à la somme de :", 0, 1, 'L');
$pdf->SetFont('helvetica', 'B', 10);
$montantLettre = Utils::montantEnLettre($devis['total_ttc']);
$pdf->MultiCell(0, 6, strtoupper(Utils::toMbConvertEncoding($montantLettre)), 0, 'L');

// ----------- SECTION HTML (Garantie) -----------
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetDrawColor(0, 0, 0); // <-- Ajoute cette ligne ici

$html = $style . '
<h2 style="font-family: Helvetica; color: #222;">5. Garantie</h2>
<div style="margin-bottom: 18px;">' . $devis['garantie'] . '</div>
';
$pdf->writeHTML($html, true, false, true, false, '');

// ----------- EXPORT PDF -----------
ob_clean();
$pdf->Output('devis_' . $devis['id'] . '.pdf', 'I');
