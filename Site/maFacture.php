<?php
require('fpdf/fpdf.php');

class PDF extends FPDF
{
// En-tête
function Header()
{
    // Police Arial gras 15
    $this->SetFont('Arial','B',15);
    // Décalage à droite
    $this->Cell(80);
    // Titre
    $this->Cell(30,10,'Facture',1,0,'C');
    // Saut de ligne
    $this->Ln(20);
}

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    $this->SetFont('Arial','I',8);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Instanciation de la classe dérivée
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
$pdf->Cell(0,10,'Reference numero : '.$_REQUEST['ide'],0,1);
$pdf->Cell(0,10,'Facture generee le : '.$_REQUEST['dateG'],0,1);
$pdf->Cell(0,10,'Nom : '.$_REQUEST['nom'],0,1);
$pdf->Cell(0,10,'Prenom : '.$_REQUEST['prenom'],0,1);
$pdf->Cell(0,10,'Recapitulatif des oeuvre(s) commandee(s) : ',0,1);
foreach ((array) $_REQUEST['oeuvre'] as $key => $value)
    $pdf->Cell(0,10,'          '.$value,0,1);
$pdf->Cell(0,10,'Total a payer : '.$_REQUEST['montant'].'euros',0,1);
$pdf->Output();
?>
