<?php
session_start();
require_once('../vendor/autoload.php');
include_once '../header/header_export_gantt.php';
require_once("../model/Utils.php");
require_once("../model/Database.php");
require_once("../model/Devis.php");

$devis_id = $_GET['devisId'] ?? null;
if (!$devis_id) {
    die("ID devis manquant");
}
$pdo = Database::getConnection();
$devisModel = new Devis($pdo);
$devis = $devisModel->getDevisById($devis_id);
$debourses = $devisModel->getDeboursesByDevisId($devis_id);

// Classe PDF personnalisée
class DeboursePDF extends TCPDF
{
    public function Header()
    {
        // Personnalise si besoin
    }
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}

$pdf = new DeboursePDF('L', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage('L');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, "Déboursés du devis {$devis['numero_devis']}", 0, 1, 'C');
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, "Client : {$devis['destine_a']}", 0, 1, 'L');
$totalLignesDebourse = $devisModel->getTotalDebourseByDevisId($devis_id);
$pdf->Cell(0, 8, "Total lTTC : " . number_format($totalLignesDebourse, 0, ',', ' ') . " FCFA", 0, 1, 'L');
$pdf->Ln(4);

$totalGeneral = 0;
foreach ($debourses as $debourse) {
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(0, 8, "Déboursé : " . ($debourse['designation'] ?? 'N/A'), 1, 1, 'L', true);

    $sousLignes = $devisModel->getLignesDebourseByDebourseId($debourse['id']);

    // Tableau
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(30, 7, "Catégorie", 1, 0, 'C', true);
    $pdf->Cell(147, 7, "Désignation", 1, 0, 'C', true);
    $pdf->Cell(40, 7, "Montant", 1, 0, 'C', true);
    $pdf->Cell(30, 7, "Date début", 1, 0, 'C', true);
    $pdf->Cell(30, 7, "Date fin", 1, 1, 'C', true);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);

    $totalDebourse = 0;
    foreach ($sousLignes as $ligne) {
        $pdf->Cell(30, 7, $ligne['categorie'], 1);
        $pdf->Cell(147, 7, Utils::toMbConvertEncoding($ligne['designation']), 1);
        $pdf->Cell(40, 7, number_format($ligne['montant'], 0, ',', ' ') . ' FCFA', 1, 0, 'R');
        $pdf->Cell(30, 7, Utils::dateJourCourtFr($ligne['date_debut']), 1, 0, 'C');
        $pdf->Cell(30, 7, Utils::dateJourCourtFr($ligne['date_fin']), 1, 1, 'C');
        $totalDebourse += $ligne['montant'];
        $totalGeneral += $ligne['montant']; // <-- Additionne chaque sous-ligne au total général
    }
    // Affiche le total du déboursé
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->Cell(187, 7, "Total déboursé", 1, 0, 'R', true);
    $pdf->Cell(30, 7, number_format($totalDebourse, 0, ',', ' ') . ' FCFA', 1, 0, 'R', true);
    $pdf->Cell(60, 7, '', 1, 1, 'C', true);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->Ln(2);
}

// Affiche le total général à la fin
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(0, 123, 0);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(177, 10, "TOTAL GENERAL", 1, 0, 'C', true);
$pdf->Cell(40, 10, number_format($totalGeneral, 0, ',', ' ') . ' FCFA', 1, 0, 'C', true);
$pdf->Cell(60, 10, '', 1, 1, 'C', true);
$pdf->SetTextColor(0, 0, 0);

ob_clean();
$pdf->Output('debourses_devis_' . $devis['id'] . '.pdf', 'I');
exit();
