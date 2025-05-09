<?php

session_start();
require_once("../model/Database.php");
require_once("../model/Devis.php");
require_once("../model/Client.php");
require_once("../model/Offre.php");
require_once("../model/Utils.php");
require_once('../fpdf186/fpdf.php');
require_once("../phpqrcode/qrlib.php");

// Classe PDF personnalisée
class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, Utils::toMbConvertEncoding('Liste des Devis'), 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, Utils::toMbConvertEncoding('Page ' . $this->PageNo() . '/{nb}'), 0, 0, 'C');
    }
}

$pdo = Database::getConnection();
$devisModel = new Devis($pdo);
$clientModel = new Client($pdo);
$offreModel = new Offre($pdo);

// Récupération des devis via la classe
$devisList = $devisModel->getAllDevis();

// Création du PDF en mode paysage
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();

// En-têtes du tableau
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(30, 10, Utils::toMbConvertEncoding('N° Devis'), 1, 0, 'C', true);
$pdf->Cell(60, 10, Utils::toMbConvertEncoding('Client'), 1, 0, 'C', true);
$pdf->Cell(70, 10, Utils::toMbConvertEncoding('Offre'), 1, 0, 'C', true);
$pdf->Cell(40, 10, Utils::toMbConvertEncoding('Date'), 1, 0, 'C', true);
$pdf->Cell(40, 10, Utils::toMbConvertEncoding('Montant Total'), 1, 0, 'C', true);
$pdf->Cell(30, 10, Utils::toMbConvertEncoding('Statut'), 1, 1, 'C', true);

// Données des devis
$pdf->SetFont('Arial', '', 10);
foreach ($devisList as $devis) {
    // Récupération des informations du client et de l'offre via les classes
    $client = $clientModel->getClientById($devis['client_id']);
    $offre = $offreModel->getOffreById($devis['offre_id']);

    $pdf->Cell(30, 10, Utils::toMbConvertEncoding($devis['numero_devis']), 1);
    $pdf->Cell(60, 10, Utils::toMbConvertEncoding($client['nom_client'] ?? ''), 1);
    $pdf->Cell(70, 10, Utils::toMbConvertEncoding($offre['description_offre'] ?? ''), 1);
    $pdf->Cell(40, 10, Utils::toMbConvertEncoding(date('d/m/Y', strtotime($devis['date_creation']))), 1);
    $pdf->Cell(40, 10, Utils::toMbConvertEncoding(number_format($devis['montant_total'], 2, ',', ' ') . ' F CFA'), 1);
    $pdf->Cell(30, 10, Utils::toMbConvertEncoding($devis['statut']), 1, 1);
}

$pdf->Output('I', 'liste_devis.pdf');
