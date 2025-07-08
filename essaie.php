<?php
require('fpdf186/fpdf.php'); // adapte le chemin si nécessaire

// Création d'une classe personnalisée si besoin
class PDF extends FPDF {
    // En-tête personnalisé (optionnel)
    function Header() {
        $this->SetFont('Arial','B',12);
        $this->Cell(0,10,'Mon titre PDF',0,1,'C');
    }

    // Pied de page personnalisé (optionnel)
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();                // Ajoute une page
$pdf->SetFont('Arial','B',16); // Police
$pdf->Cell(40,10,'Bonjour FPDF !');

// Export : plusieurs options possibles

// 1. Afficher dans le navigateur
$pdf->Output('I', 'mon_fichier.pdf');

// 2. Télécharger directement
// $pdf->Output('D', 'mon_fichier.pdf');

// 3. Sauvegarder sur le serveur
// $pdf->Output('F', 'chemin/vers/mon_fichier.pdf');

// 4. Retourner le contenu (ex: pour envoyer dans un email)
// $contenu = $pdf->Output('S');
?>
