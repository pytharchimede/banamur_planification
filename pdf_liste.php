<?php



session_start();


include '../../../connex.php';



include "../../../pdf/phpToPDF.php";



require '../../../pdf/mysql_table.php';



class PDF extends PDF_MySQL_Table

{



    function Header() {}

    // Pied de page

    function Footer()

    {

        // Position at 1.5 cm from bottom

        $this->SetY(-22);



        $this->Image('../../../img/logo_veritas.jpg', 10, 275, 30);

        // Arial italic 8

        $this->SetFont('Arial', '', 7);



        $this->Cell(0, 3.5, mb_convert_encoding("FOURNITURES INDUSTRIELLES, DEPANNAGE ET TRAVAUX PUBLIQUES", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->Cell(0, 3.5, mb_convert_encoding('Au capital de 10 000 000 F CFA - Siège Social : Abidjan, Koumassi, Zone industrielle ', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->Cell(0, 3.5, mb_convert_encoding("01 BP 1642 Abidjan 01 - Téléphone : (+225) 27-21-36-27-27  -  Email : info@fidest.org", 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

        $this->Cell(0, 3.5, mb_convert_encoding('RCCM : CI-ABJ-2017-B-20163  -  N° CC : 010274200088 ', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');




        $this->Image('../../../img/logo_connex.jpg', 172, 275, 30);



        // Page number

        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}



//Paramètres du fichier PDF 

$pdffilename = 'recap_besoin.pdf';

clearstatcache();

if (file_exists($pdffilename)) {

    //Si le fichier PDF existe, il faut le supprimer d'abord

    unlink($pdffilename);
}



//Création du fichier PDF

$pdf = new PDF('L', 'mm', 'A4');



$pdf->AliasNbPages();



//$pdf->AddPage('L');

$pdf->AddPage();





$pdf->SetTextColor(0);

$pdf->SetFont('Arial', '', 10);

$entreprise = mb_convert_encoding("Fidest Entreprises", 'ISO-8859-1', 'UTF-8');

$pdf->Image("../../../img/entete_fiche.jpg", 50, 10,  200, 'C');

$pdf->Ln(25);

//$pdf->Cell(0,2,utf8_decode("Application de Gestion des Formations "),0,1,'C');

//$pdf->Line(10, 35, 200, 35); 

$pdf->Ln(5);



$pdf->SetFont('Arial', '', 7);

$mot = "N°";

$num = mb_convert_encoding($mot, 'ISO-8859-1', 'UTF-8');

$date = gmdate('d/m/Y');





if (isset($_SESSION['recher_date_debut']) && $_SESSION['recher_date_debut'] != '') {

    $recher_date_debut = $_SESSION['recher_date_debut'];
} else {

    $recher_date_debut = '';
}



if (isset($_SESSION['recher_date_fin']) && $_SESSION['recher_date_fin'] != '') {

    $recher_date_fin = $_SESSION['recher_date_fin'];
} else {

    $recher_date_fin = '';
}



if (isset($_SESSION['recher_demandeur']) && $_SESSION['recher_demandeur'] != '') {

    $recher_demandeur = $_SESSION['recher_demandeur'];
} else {

    $recher_demandeur = '';
}



if (isset($_SESSION['recher_chantier']) && $_SESSION['recher_chantier'] != '') {

    $recher_chantier = $_SESSION['recher_chantier'];
} else {

    $recher_chantier = '';
}



if (isset($_SESSION['recher_affectation']) && $_SESSION['recher_affectation'] != '') {

    $recher_affectation = $_SESSION['recher_affectation'];
} else {

    $recher_affectation = '';
}







$req = "SELECT * FROM fiche WHERE id_fiche!='' AND etat_fiche=0 AND decaisse=0 AND sauvegarder=0 AND approuve=1 AND date_decaissement_minimum <= CURDATE() ";

if ($recher_date_debut != "") {
    $req .= " AND date_creat_fiche>='" . $recher_date_debut . "'  ";
}

if ($recher_date_fin != "") {
    $req .= " AND date_creat_fiche<='" . $recher_date_fin . "' ";
}

if ($recher_demandeur != "") {
    $req .= " AND beficiaire_fiche LIKE '%" . $recher_demandeur . "%' ";
}

if ($recher_chantier != "") {
    $req .= " AND chantier_id='" . $recher_chantier . "' ";
}

if ($recher_affectation != "") {
    $req .= " AND affectation_id='" . $recher_affectation . "' ";
}

$req .= " ORDER BY id_fiche DESC";



$reta = $con->prepare($req);

$reta->execute();

$nbre_serv = $reta->rowcount();





$req0_ = " SELECT * FROM fiche WHERE etat_fiche=0 AND sauvegarder=0 AND approuve=1 AND date_decaissement_minimum <= CURDATE()";



// if ($_SESSION['secur_hop'] != 'dgfidest' && $_SESSION['secur_hop'] != 'lol') {

//     $req0_ .= ' AND affectation_id!=29 AND affectation_id!=30 ';
// }





if ($recher_date_debut != "") {

    $req0_ .= " AND date_creat_fiche>='" . $recher_date_debut . "'  ";
}



if ($recher_date_fin != "") {

    $req0_ .= " AND date_creat_fiche<='" . $recher_date_fin . "' ";
}



if ($recher_demandeur != "") {

    $req0_ .= " AND beficiaire_fiche LIKE '%" . $recher_demandeur . "%' ";
}



if ($recher_chantier != "") {

    $req0_ .= " AND chantier_id='" . $recher_chantier . "' ";
}



if ($recher_affectation != "") {

    $req0_ .= " AND affectation_id='" . $recher_affectation . "' ";
}





$req0 = $con->prepare($req0_);



$req0->execute();

$info_fiche0 = $req0->fetch();



$date_oj = gmdate('d/m/Y');



$pdf->SetFont('Arial', '', 10);



$pdf->Cell(0, 4, '		                                                                                                                                                                 Date :' . $date_oj, 0, 1, 'L');



$pdf->Ln(6);



$aff = $con->prepare('SELECT * FROM affectation WHERE id_affectation=:A');

$aff->execute(array('A' => $recher_affectation));

$iaff = $aff->fetch();

$lib_affectation = $iaff['lib_affectation'];







$cha = $con->prepare('SELECT * FROM chantier WHERE id_chantier=:A');

$cha->execute(array('A' => $recher_chantier));

$icha = $cha->fetch();

$lib_chantier = $icha['lib_chantier'];






/*
$pay = mb_convert_encoding("Côte d'Ivoire", 'ISO-8859-1', 'UTF-8');

$pdf->SetFont('Arial', 'BU', 15);

$pdf->Cell(0, 3.3, mb_convert_encoding('DEMANDES A ANALYSER', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

$pdf->Ln(6);

$pdf->SetFont('Arial', '', 15);

$pdf->Cell(0, 3.3, mb_convert_encoding('Debut : ' . date("d/m/Y", strtotime($recher_date_debut)) . ' - Fin : ' . date("d/m/Y", strtotime($recher_date_fin)) . ' - ' . $lib_affectation . ' - ' . $lib_chantier . ' ', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 13);
*/

$pay = mb_convert_encoding("Côte d'Ivoire", 'ISO-8859-1', 'UTF-8');

$pdf->SetFont('Arial', 'BU', 15);
$pdf->Cell(0, 3.3, mb_convert_encoding('DEMANDES À ANALYSER', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(6);

// Vérification des dates et affichage
$today = date("d/m/Y");
if (empty($recher_date_debut) || empty($recher_date_fin)) {
    $dateText = "Point de décaissement généré le : $today";
    $message = "Aucun intervalle de date sélectionné";
} else {
    $dateText = 'Période : Début ' . date("d/m/Y", strtotime($recher_date_debut)) . ' - Fin ' . date("d/m/Y", strtotime($recher_date_fin));
    $message = ""; // Aucun message supplémentaire dans ce cas
}

// Affichage de la période ou date du jour
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, mb_convert_encoding($dateText, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');

// Affichage du message en italique, rouge et plus petit si aucun intervalle n'est sélectionné
if (!empty($message)) {
    $pdf->SetTextColor(255, 0, 0); // Couleur rouge
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 5, mb_convert_encoding($message, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->SetTextColor(0, 0, 0); // Réinitialiser la couleur
}

$pdf->Ln(3);

// Affichage des autres informations
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, mb_convert_encoding($lib_affectation . ' - ' . $lib_chantier, 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
$pdf->Ln(3);



$pdf->SetTextColor(0, 0, 0);

$pdf->SetFont('Arial', 'B', 7);



$pdf->Cell(20);

$pdf->Ln(8);



$req1_ = " SELECT * FROM fiche LEFT JOIN affectation ON affectation.id_affectation=fiche.affectation_id WHERE etat_fiche=0 AND sauvegarder=0 AND approuve=1 AND date_decaissement_minimum <= CURDATE()";



// if ($_SESSION['secur_hop'] != 'dgfidest' && $_SESSION['secur_hop'] != 'lol') {

//     $req1_ .= ' AND affectation_id!=29 AND affectation_id!=30 ';
// }





if ($recher_date_debut != "") {

    $req1_ .= " AND date_creat_fiche>='" . $recher_date_debut . "'  ";
}



if ($recher_date_fin != "") {

    $req1_ .= " AND date_creat_fiche<='" . $recher_date_fin . "' ";
}



if ($recher_demandeur != "") {

    $req1_ .= " AND beficiaire_fiche LIKE '%" . $recher_demandeur . "%' ";
}



if ($recher_chantier != "") {

    $req1_ .= " AND chantier_id='" . $recher_chantier . "' ";
}



if ($recher_affectation != "") {

    $req1_ .= " AND affectation_id='" . $recher_affectation . "' ";
}





$req1 = $con->prepare($req1_);



$req1->execute();

$info_fiche = $req1->fetch();



$pdf->SetTextColor(0, 0, 0);



$pdf->Ln(8);



$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell(0, 3.7, mb_convert_encoding('Nombre d\'éléments : ' . $nbre_serv . ' ', 'ISO-8859-1', 'UTF-8'), 0, 1, 'L');



$pdf->Cell(20);

$pdf->Ln(3);



$width_cell = array(8, 70, 70, 50, 29, 29, 29);

$pdf->SetFont('Arial', 'B', 8);



//Couleur d'arrère plan de l'en-tête//

$pdf->SetFillColor(193, 229, 252);



//EN-TETE /// 



//Colonne 1 //

$pdf->Cell($width_cell[0], 7, mb_convert_encoding('N°', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

$pdf->Cell($width_cell[1], 7, mb_convert_encoding('Affectation', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

$pdf->Cell($width_cell[2], 7, mb_convert_encoding('Designation', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

$pdf->Cell($width_cell[3], 7, mb_convert_encoding('Detail', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

$pdf->Cell($width_cell[4], 7, mb_convert_encoding('Bénéficiaire', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

$pdf->Cell($width_cell[5], 7, mb_convert_encoding('Téléphone', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);

$pdf->Cell($width_cell[6], 7, mb_convert_encoding('Montant', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);


//// FIN EN-TETE ///////

$pdf->SetFont('Arial', '', 7);

//Couleur arriere plan en-tête//

$pdf->SetFillColor(235, 236, 236);

//Pour donner des couleurs d'arrière plan alternatives// 

$fill = false;

$i = 0;

$k = 0;

if (isset($affectation)) {
    $affectation_i = $affectation;
} else {
    $affectation_i = '';
}

foreach ($reta as $row) {

    $i++;



    if ($row['affectation_id'] == 1) {

        $designation = $row['designation_fiche'];



        $ch = $con->prepare('SELECT * FROM chantier WHERE id_chantier=:A');

        $ch->execute(array('A' => $row['chantier_id']));

        $ich = $ch->fetch();

        $lib_chantier = $ich['lib_chantier'];



        $affectation = $row['lib_affectation'] . ' ' . $lib_chantier;
    } else {

        $designation = $row['designation_fiche'];

        $affectation = $row['lib_affectation'];
    }





    //Calcul montant restant

    $montant_fiche = $row['montant_fiche'];



    $tot_dec = 0;



    $mdec = $con->prepare('SELECT * FROM decaissement WHERE num_fiche_decaissement=:A');

    $mdec->execute(array('A' => $row['num_fiche']));

    while ($imdec = $mdec->fetch()) {

        $tot_dec = $tot_dec + $imdec['montant'];
    }



    $montant_restant = $montant_fiche - $tot_dec;







    $pdf->Cell($width_cell[0], 7, mb_convert_encoding(substr($row['num_fiche'], 0, 45), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);

    $pdf->Cell($width_cell[1], 7, mb_convert_encoding(substr($affectation, 0, 45), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);

    $pdf->Cell($width_cell[2], 7, mb_convert_encoding(substr($designation, 0, 45), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);

    if ($row['affectation_id'] == 1) {

        $pdf->Cell($width_cell[3], 7, mb_convert_encoding(substr($row['precision_fiche'], 0, 45), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);
    } else if ($row['affectation_id'] == 19) {

        $affec_bur = $con->prepare('SELECT * FROM serv_bureau_banamur WHERE id_serv_bureau_banamur=:A');
        $affec_bur->execute(array('A' => $row['serv_bureau_banamur_id']));
        $iaffec = $affec_bur->fetch();

        $pdf->Cell($width_cell[3], 7, mb_convert_encoding(substr($iaffec['lib_serv_bureau_banamur'], 0, 45), 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);
    }


    $pdf->Cell($width_cell[4], 7, mb_convert_encoding($row['beficiaire_fiche'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);

    $pdf->Cell($width_cell[5], 7, mb_convert_encoding($row['tel_beneficiaire_fiche'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);

    $pdf->Cell($width_cell[6], 7, mb_convert_encoding($montant_restant, 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', false);



    $k++;

    $affectation_k = $affectation;





    $fill = !$fill;
}



//Calcul Total

$tota = ' SELECT * FROM fiche WHERE etat_fiche=0 AND sauvegarder=0 AND approuve=1 AND date_decaissement_minimum <= CURDATE()';



// if ($_SESSION['secur_hop'] != 'dgfidest' && $_SESSION['secur_hop'] != 'lol') {

//     $tota .= ' AND affectation_id!=29 AND affectation_id!=30 ';
// }





if ($recher_date_debut != "") {

    $tota .= " AND date_creat_fiche>='" . $recher_date_debut . "'  ";
}



if ($recher_date_fin != "") {

    $tota .= " AND date_creat_fiche<='" . $recher_date_fin . "' ";
}



if ($recher_demandeur != "") {

    $tota .= " AND beficiaire_fiche LIKE '%" . $recher_demandeur . "%' ";
}



if ($recher_chantier != "") {

    $tota .= " AND chantier_id='" . $recher_chantier . "' ";
}



if ($recher_affectation != "") {

    $tota .= " AND affectation_id='" . $recher_affectation . "' ";
}





$tot = $con->prepare($tota);



$tot->execute();

$montant_total = 0;

while ($itot = $tot->fetch()) {



    //Calcul montant restant

    $montant_fiche = $itot['montant_fiche'];



    $tot_dec = 0;



    $mdec = $con->prepare('SELECT * FROM decaissement WHERE num_fiche_decaissement=:A');

    $mdec->execute(array('A' => $itot['num_fiche']));

    while ($imdec = $mdec->fetch()) {

        $tot_dec = $tot_dec + $imdec['montant'];
    }



    $montant_restant = $montant_fiche - $tot_dec;



    $montant_total = $montant_total + ($montant_restant);
}



//Affiche Total

$pdf->SetFillColor(255, 255, 255);

$pdf->Cell($width_cell[0], 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', false);

$pdf->Cell($width_cell[1], 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', false);

$pdf->Cell($width_cell[2], 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', false);

$pdf->Cell($width_cell[3], 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', false);

$pdf->Cell($width_cell[4], 7, mb_convert_encoding('', 'ISO-8859-1', 'UTF-8'), 0, 0, 'C', false);

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell($width_cell[5], 7, mb_convert_encoding('TOTAL', 'ISO-8859-1', 'UTF-8'), 1, 0, 'R', false);

$pdf->Cell($width_cell[6], 7, mb_convert_encoding(number_format($montant_total, 0, ',', ' ') . ' FCFA', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 7);

$fill = !$fill;



$pdf->Ln(8);



$pdf->SetFont('Arial', 'I', 10);

$pdf->Cell(0, 4, mb_convert_encoding('                                                                                                                                          Le Directeur Général (nom, date et visa) ', 'ISO-8859-1', 'UTF-8'), 0, 1, 'R');

$pdf->Ln(149);

$pdf->Line(10, 272, 200, 272);



//Sauvegarde du fichier PDF généré

$pdf->Output($pdffilename);



echo "<META HTTP-EQUIV='Refresh' CONTENT='2;URL=$pdffilename'>";
