<?php
session_start();
require_once('../vendor/autoload.php');
include_once '../header/header_export_gantt.php';
require_once("../model/Utils.php");

function dureeFr($dateDebut, $dateFin)
{
    if (empty($dateDebut) || empty($dateFin)) return '-';
    $start = new DateTime($dateDebut);
    $end = new DateTime($dateFin);
    if ($end < $start) return '-';
    $interval = $start->diff($end);
    $txt = [];
    if ($interval->y) $txt[] = $interval->y . ' an' . ($interval->y > 1 ? 's' : '');
    if ($interval->m) $txt[] = $interval->m . ' mois';
    if ($interval->d) $txt[] = $interval->d . ' jour' . ($interval->d > 1 ? 's' : '');
    if (empty($txt)) $txt[] = '1 jour';
    return implode(' ', $txt);
}

// Classe PDF personnalisée (hérite de TCPDF)
class PlanningPDF extends TCPDF
{
    public function Header()
    {
        // Dimensions
        // $logoW = 30;
        // $logoH = 30;
        // $pageW = $this->getPageWidth();
        // $enteteW = 220; // Largeur totale du bloc entête (ajuste si besoin)
        // $blocTextW = 140; // Largeur du bloc texte (ajuste si besoin)
        // $blocH = $logoH;

        // // Calcul du point de départ pour centrer l'ensemble logo+texte
        // $startX = ($pageW - ($logoW + 8 + $blocTextW)) / 2;
        // $logoX = $startX;
        // $logoY = 15;
        // $textX = $logoX + $logoW + 8;
        // $textY = $logoY;

        // // Logo
        // $this->Image('../logo/' . ($GLOBALS['devis']['logo'] ?? 'default_logo.jpg'), $logoX, $logoY, $logoW, $logoH);

        // // Texte à droite du logo, centré verticalement
        // $this->SetXY($textX, $textY);
        // $this->SetFont('helvetica', 'B', 14);
        // $this->Cell($blocTextW, 7, 'BANAMUR INDUSTRIES ET TECHNOLOGIES', 0, 2, 'C');
        // $this->SetFont('helvetica', '', 11);
        // $this->Cell($blocTextW, 6, 'BATIMENT-TRAVAUX PUBLICS', 0, 2, 'C');
        // $this->SetFont('helvetica', 'B', 11);
        // $this->SetTextColor(255, 204, 0);
        // $this->Cell($blocTextW, 6, 'RENOVATION ET TRAVAUX NEUF', 0, 2, 'C');
        // $this->SetFont('helvetica', '', 10);
        // $this->SetTextColor(0, 0, 0);
        // $this->Cell($blocTextW, 6, 'TUYAUTERIE-CHAUDRONNERIE-CHARPENTE METALLIQUE', 0, 2, 'C');
        // $this->Ln(2);
    }

    public function Footer()
    {
        $this->SetDrawColor(0, 0, 0);
        $this->Line(10, 287, 200, 287);
        $this->SetY(-28);
        $this->SetFont('helvetica', '', 7);
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("S.A.R.L au Capital de 100 000 000 FCFA - Siège Social: Abidjan, Koumassi Bd. du Gabon prolongé – 01 BP 1642 Abidjan 01"), 0, 1, 'C');
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("RCCM N°: CI-ABJ-03-2022-B13-02828 – Tél. : +225 27 21 36 27 27 / 27 21 36 09 29 – Fax : 27 21 36 05 75"), 0, 1, 'C');
        $this->Cell(0, 3.5, Utils::toMbConvertEncoding("E-mail: banacerf1@gmail.com - Compte Bancaire BDU N° : CI180 01010 020401144580 11"), 0, 1, 'C');
        $this->Cell(0, 8, Utils::toMbConvertEncoding('Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages()), 0, 0, 'C');
    }
}

// Création du PDF
$pdf = new PlanningPDF('L', 'mm', 'A4', true, 'UTF-8', false); // L = paysage

// Récupération correcte des marges
$margins = $pdf->getMargins();
$pageWidth = $pdf->getPageWidth() - $margins['left'] - $margins['right'];
$pdf->SetX($margins['left']);

$colNum = 10;
$colTask = 70;
$colStart = 22;
$colEnd = 22;
$colDuration = 14;
$colGantt = $pageWidth - ($colNum + $colTask + $colStart + $colEnd + $colDuration);

// ----------- SECTION PLANNING (GANTT) -----------
$pdf->AddPage('L'); // Paysage
$pdf->SetFont('helvetica', 'B', 12); // Police réduite
$pdf->Ln(6);
$pdf->Cell(0, 10,  "Planning d'exécution (Gantt simplifié)", 0, 1, 'C');
$pdf->Ln(2);

// En-tête du tableau
$pdf->SetFont('helvetica', 'B', 7);
$pdf->SetFillColor(0, 0, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetX($margins['left']);
$pdf->Cell($colNum, 8, 'N°', 1, 0, 'C', true);
$pdf->Cell($colTask, 8, 'Tâche', 1, 0, 'C', true);
$pdf->Cell($colStart, 8, 'Début', 1, 0, 'C', true);
$pdf->Cell($colEnd, 8, 'Fin', 1, 0, 'C', true);
$pdf->Cell($colDuration, 8, 'Durée', 1, 0, 'C', true);
$pdf->Cell($colGantt, 8, 'Gantt', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 7);
$pdf->SetTextColor(0, 0, 0);

// Calcul des bornes du planning
$pos = 1;
$minDate = null;
$maxDate = null;
foreach ($lignes_debourse as $l) {
    if (!empty($l['date_debut']) && !empty($l['date_fin'])) {
        $dateDebut = strtotime($l['date_debut']);
        $dateFin = strtotime($l['date_fin']);
        if ($minDate === null || $dateDebut < $minDate) $minDate = $dateDebut;
        if ($maxDate === null || $dateFin > $maxDate) $maxDate = $dateFin;
    }
}

// Affichage de l'échelle de temps au-dessus du Gantt
$pdf->SetFont('helvetica', 'I', 6);
$pdf->SetTextColor(80, 80, 80);
$pdf->SetX($margins['left'] + $colNum + $colTask + $colStart + $colEnd + $colDuration);

$nbLabels = 6; // Nombre de repères à afficher (ajuste selon besoin)
for ($i = 0; $i <= $nbLabels; $i++) {
    $timestamp = $minDate + ($i * ($maxDate - $minDate) / $nbLabels);
    $label = date('d/m/Y', $timestamp); // Correction ici
    $xLabel = $pdf->GetX() + ($i * $colGantt / $nbLabels);
    $pdf->SetXY($xLabel, $pdf->GetY());
    $pdf->Cell(1, 3, '|', 0, 0, 'C');
    $pdf->SetXY($xLabel - 10, $pdf->GetY() + 2);
    $pdf->Cell(20, 3, $label, 0, 0, 'C');
    $pdf->SetXY($margins['left'] + $colNum + $colTask + $colStart + $colEnd + $colDuration, $pdf->GetY() - 2);
}
$pdf->Ln(8);
$pdf->SetTextColor(0, 0, 0);

// Affichage du planning
$pos = 1;
if ($minDate === null || $maxDate === null) {
    $pdf->Cell($pageWidth, 12, "Aucune donnée de planning disponible.", 1, 1, 'C');
} else {
    $totalDays = ($maxDate - $minDate) / 86400 + 1;
    foreach ($lignes_debourse as $l) {
        if (!empty($l['is_titre'])) {
            // Afficher le titre du déboursé sur toute la largeur du tableau
            $pdf->SetFont('helvetica', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);
            $pdf->SetX($margins['left']);
            $pdf->Cell($pageWidth, 8, $l['titre'], 1, 1, 'L', true);
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetFillColor(255, 255, 255);
            continue;
        }

        $designation = Utils::toMbConvertEncoding($l['designation']);
        $dateDebut = $l['date_debut'] ?? '-';
        $dateFin = $l['date_fin'] ?? '-';
        $duration = dureeFr($l['date_debut'], $l['date_fin']);

        // Calcul de la hauteur nécessaire pour la cellule tâche (MultiCell)
        $nbLines = $pdf->getNumLines($designation, $colTask);
        $rowHeight = max(8, $nbLines * 4.5);

        $pdf->SetX($margins['left']);
        // N° (numérotation par déboursé)
        $pdf->MultiCell($colNum, $rowHeight, $l['numero'], 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        // Tâche
        $pdf->MultiCell($colTask, $rowHeight, $designation, 1, 'L', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        // Début
        $pdf->MultiCell($colStart, $rowHeight, $dateDebut, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        // Fin
        $pdf->MultiCell($colEnd, $rowHeight, $dateFin, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        // Durée
        $pdf->MultiCell($colDuration, $rowHeight, $duration, 1, 'C', false, 0, '', '', true, 0, false, true, $rowHeight, 'M');
        // Gantt
        $xGantt = $pdf->GetX();
        $yGantt = $pdf->GetY();
        $pdf->MultiCell($colGantt, $rowHeight, '', 1, 'C', false, 1, '', '', true, 0, false, true, $rowHeight, 'M');

        // Dessiner la barre Gantt si dates valides
        if (!empty($l['date_debut']) && !empty($l['date_fin'])) {
            $start = strtotime($l['date_debut']);
            $end = strtotime($l['date_fin']);
            $offset = max(0, min($totalDays, ($start - $minDate) / 86400));
            $durationBar = max(1, min($totalDays - $offset, ($end - $start) / 86400 + 1));
            $barW = max(2, ($durationBar / $totalDays) * ($colGantt - 2));
            $barOffset = ($offset / $totalDays) * ($colGantt - 2);
            if ($barOffset + $barW > $colGantt - 2) $barW = $colGantt - 2 - $barOffset;
            $pdf->SetFillColor(0, 102, 204);
            $pdf->Rect($xGantt + 1 + $barOffset, $yGantt + ($rowHeight / 2) - 2, $barW, 4, 'F');
            $pdf->SetFillColor(255, 255, 255);
        }
    }
}

ob_clean();
$pdf->Output('planning_gantt_' . $devis['id'] . '.pdf', 'I');
exit();
