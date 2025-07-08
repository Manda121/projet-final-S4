<?php
require('fpdf186/fpdf.php');

if (!isset($_POST['pret'])) {
    die("Aucune donnee reçue.");
}

$pret = json_decode($_POST['pret'], true);
if (!$pret) {
    die("Erreur de decodage JSON.");
}

class ProfessionalLoanPDF extends FPDF
{
    var $pret;
    var $bankName;
    var $bankAddress = '';
    var $bankPhone = '';
    var $bankEmail = '';

    function __construct($pret)
    {
        parent::__construct();
        $this->pret = $pret;
        $this->bankName = $pret['etablissement_nom'];
    }
function Header()
{
    // Bank name and details (top left)
    $this->SetTextColor(0, 32, 91); // #00205b
    $this->SetFont('Helvetica', 'B', 16);
    $this->SetXY(10, 10); // Adjusted to left margin
    $this->Cell(100, 6, utf8_decode($this->bankName), 0, 1, 'L');
    
    $this->SetFont('Helvetica', '', 9);
    $this->SetXY(10, 16);
    $this->Cell(100, 4, utf8_decode($this->bankAddress), 0, 1, 'L');
    $this->SetXY(10, 20);
    $this->Cell(60, 4, $this->bankPhone, 0, 0, 'L');
    $this->Cell(40, 4, $this->bankEmail, 0, 1, 'L');
    
    // Date and reference box (top right)
    $this->SetDrawColor(200, 200, 200); // #c8c8c8
    $this->SetFillColor(248, 249, 250); // #f8f9fa
    $this->Rect(150, 10, 50, 16, 'DF'); // Moved to top right (210mm - 60mm = 150mm)
    $this->SetTextColor(0, 0, 0);
    $this->SetFont('Helvetica', 'B', 9);
    $this->SetXY(152, 12);
    $this->Cell(0, 4, "Date d'edition:", 0, 1, 'L');
    $this->SetXY(152, 16);
    $this->SetFont('Helvetica', '', 9);
    $this->Cell(0, 4, date('d/m/Y'), 0, 1, 'L');
    
    // Document title
    $this->Ln(10);
    $this->SetFont('Helvetica', 'B', 18);
    $this->SetTextColor(0, 32, 91);
    $this->Cell(0, 8, 'DETAIL DE PRET', 0, 1, 'C');
    $this->SetFont('Helvetica', '', 12);
    $this->SetTextColor(100, 100, 100);
    $this->Cell(0, 6, 'Reference: ' . $this->pret['id_pret'], 0, 1, 'C');
    
    $this->Ln(15);
}
    
    function Footer()
    {
        $this->SetY(-25);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(120, 120, 120);
        $this->Ln(2);
        $this->Cell(0, 4, '' . $this->bankName, 0, 1, 'L');
        $this->Cell(0, 4, 'ETU003280 ETU003299 ETU003233');
        $this->Cell(0, 4, 'Page ' . $this->PageNo(), 0, 0, 'R');
    }
    
    function ClientSection($data)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 32, 91);
        $this->Cell(0, 8, 'INFORMATIONS CLIENT', 0, 1, 'L');
        
        $this->SetDrawColor(0, 32, 91);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        
        // Client info in a box
        $this->SetDrawColor(200, 200, 200);
        $this->SetFillColor(248, 249, 250);
        $this->Rect(10, $this->GetY(), 190, 25, 'DF');
        
        $this->SetFont('Arial', 'B', 11);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY(15, $this->GetY() + 5);
        $this->Cell(0, 6, 'Nom complet: ' . $data['client_nom'] . ' ' . $data['client_prenom'], 0, 1, 'L');
        
        $this->SetXY(15, $this->GetY());
        $this->Cell(90, 6, 'Numero client: ' . (isset($data['id_client']) ? $data['id_client'] : 'N/A'), 0, 0, 'L');
        $this->Cell(0, 6, 'Date du contrat: ' . $data['date_pret'], 0, 1, 'L');
        
        $this->Ln(20);
    }
    
    function LoanDetailsSection($data)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 32, 91);
        $this->Cell(0, 8, 'CARACTERISTIQUES DU PRET', 0, 1, 'L');
        
        $this->SetDrawColor(0, 32, 91);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        
        // Loan details table
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0, 0, 0);
        $this->SetDrawColor(180, 180, 180);
        
        $details = [
            ['Libelle', 'Valeur'],
            ['Type de pret', $data['type_pret_libelle']],
            ['Montant du capital', number_format($data['montant'], 2, ',', ' ') . ' Ar'],
            ['Taux d\'interet annuel', $data['taux'] . ' %'],
            ['Taux d\'assurance', $data['taux_assurance'] . ' %'],
            ['Date d\'echeance', $data['date_limite']]
            // ['Statut actuel', $this->getStatusBadge($data['etat'])]
        ];
        
        foreach ($details as $index => $row) {
            if ($index === 0) {
                $this->SetFont('Arial', 'B', 11);
                $this->SetFillColor(0, 32, 91);
                $this->SetTextColor(255, 255, 255);
            } else {
                $this->SetFont('Arial', '', 10);
                $this->SetFillColor($index % 2 == 0 ? 248 : 255, $index % 2 == 0 ? 249 : 255, $index % 2 == 0 ? 250 : 255);
                $this->SetTextColor(0, 0, 0);
            }
            
            $this->Cell(80, 8, utf8_decode($row[0]), 1, 0, 'L', true);
            $this->Cell(110, 8, utf8_decode($row[1]), 1, 1, 'L', true);
        }
        
        $this->Ln(5);
        
        // Description box if exists
        if (!empty($data['description'])) {
            $this->SetFont('Arial', 'B', 11);
            $this->SetTextColor(0, 32, 91);
            $this->Cell(0, 6, 'Description:', 0, 1, 'L');
            
            $this->SetDrawColor(200, 200, 200);
            $this->SetFillColor(248, 249, 250);
            $this->Rect(10, $this->GetY(), 190, 15, 'DF');
            
            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(60, 60, 60);
            $this->SetXY(15, $this->GetY() + 3);
            $this->MultiCell(180, 5, utf8_decode($data['description']), 0, 'L');
            $this->Ln(10);
        }
        
        $this->Ln(5);
    }
    
    function RemboursementsSection($remises)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 32, 91);
        $this->Cell(0, 8, 'HISTORIQUE DES REMBOURSEMENTS', 0, 1, 'L');
        
        $this->SetDrawColor(0, 32, 91);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        
        if (empty($remises)) {
            $this->SetDrawColor(200, 200, 200);
            $this->SetFillColor(255, 245, 245);
            $this->Rect(10, $this->GetY(), 190, 20, 'DF');
            
            $this->SetFont('Arial', 'I', 11);
            $this->SetTextColor(180, 50, 50);
            $this->SetXY(15, $this->GetY() + 7);
            $this->Cell(0, 6, 'Aucun remboursement enregistre a ce jour', 0, 1, 'L');
            $this->Ln(15);
        } else {
            // Table header
            $this->SetFont('Arial', 'B', 11);
            $this->SetFillColor(0, 32, 91);
            $this->SetTextColor(255, 255, 255);
            $this->SetDrawColor(180, 180, 180);
            
            $this->Cell(30, 10, 'id.', 1, 0, 'C', true);
            $this->Cell(50, 10, 'Montant', 1, 0, 'C', true);
            $this->Cell(50, 10, 'Date', 1, 0, 'C', true);
            $this->Cell(60, 10, 'Statut', 1, 1, 'C', true);
            
            // Table body
            $this->SetFont('Arial', '', 10);
            $this->SetTextColor(0, 0, 0);
            $total = 0;
            
            foreach ($remises as $index => $remise) {
                $this->SetFillColor($index % 2 == 0 ? 248 : 255, $index % 2 == 0 ? 249 : 255, $index % 2 == 0 ? 250 : 255);
                
                $this->Cell(30, 8, $remise['id_remise'], 1, 0, 'C', true);
                $this->Cell(50, 8, number_format($remise['montant'], 2, ',', ' ') . ' Ar', 1, 0, 'R', true);
                $this->Cell(50, 8, date('d/m/Y', strtotime($remise['date_remise'])), 1, 0, 'C', true);
                $this->Cell(60, 8, 'ValidE', 1, 1, 'C', true);
                
                $total += $remise['montant'];
            }
            
            // Total row
            $this->SetFont('Arial', 'B', 11);
            $this->SetFillColor(0, 32, 91);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(80, 10, 'TOTAL REMBOURSE', 1, 0, 'C', true);
            $this->Cell(50, 10, number_format($total, 2, ',', ' ') . ' Ar', 1, 0, 'R', true);
            $this->Cell(60, 10, count($remises) . ' versement(s)', 1, 1, 'C', true);
        }
        
        $this->Ln(10);
    }
    
    function SummarySection($data, $remises)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 32, 91);
        $this->Cell(0, 8, 'RESUME FINANCIER', 0, 1, 'L');
        
        $this->SetDrawColor(0, 32, 91);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(5);
        
        $totalRembourse = 0;
        foreach ($remises as $remise) {
            $totalRembourse += $remise['montant'];
        }
        
        $soldeRestant = $data['montant'] - $totalRembourse;
        
        // Summary box
        $this->SetDrawColor(200, 200, 200);
        $this->SetFillColor(245, 250, 255);
        $this->Rect(10, $this->GetY(), 190, 25, 'DF');
        
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(0, 32, 91);
        $this->SetXY(15, $this->GetY() + 5);
        $this->Cell(85, 6, 'Capital initial:', 0, 0, 'L');
        $this->Cell(0, 6, number_format($data['montant'], 2, ',', ' ') . ' Ar', 0, 1, 'R');
        
        $this->SetXY(15, $this->GetY());
        $this->Cell(85, 6, 'Total rembourse:', 0, 0, 'L');
        $this->Cell(0, 6, number_format($totalRembourse, 2, ',', ' ') . ' Ar', 0, 1, 'R');
        
        $this->SetXY(15, $this->GetY());
        $this->SetTextColor($soldeRestant > 0 ? 180 : 34, $soldeRestant > 0 ? 50 : 139, $soldeRestant > 0 ? 50 : 34);
        $this->Cell(85, 6, 'Solde restant:', 0, 0, 'L');
        $this->Cell(0, 6, number_format($soldeRestant, 2, ',', ' ') . ' Ar', 0, 1, 'R');
        
        $this->Ln(20);
    }
    
    function getStatusBadge($status)
    {
        $badges = [
            'actif' => 'EN COURS',
            'termine' => 'TERMINÉ',
            'suspendu' => 'SUSPENDU',
            'annule' => 'ANNULÉ'
        ];
        
        return isset($badges[strtolower($status)]) ? $badges[strtolower($status)] : strtoupper($status);
    }
    
    // function DisclaimerSection()
    // {
    //     $this->SetFont('Arial', 'I', 9);
    //     $this->SetTextColor(100, 100, 100);
    //     $this->Cell(0, 5, 'MENTIONS LÉGALES', 0, 1, 'L');
    //     $this->MultiCell(0, 4, utf8_decode('Ce document constitue un relevé de compte prêt au ' . date('d/m/Y') . '. Les informations contenues dans ce document sont confidentielles et ne doivent pas être divulguées a des tiers. Pour toute question concernant votre prêt, veuillez contacter votre conseiller bancaire.'), 0, 'J');
    // }
}

// Create professional PDF
$pdf = new ProfessionalLoanPDF($pret);
$pdf->pret = $pret;
$pdf->AddPage();
$pdf->SetMargins(10, 10, 10);

// Add all sections
$pdf->ClientSection($pret);
$pdf->LoanDetailsSection($pret);
$pdf->RemboursementsSection($pret['remises']);
$pdf->SummarySection($pret, $pret['remises']);
// $pdf->DisclaimerSection();

// Output PDF
$pdf->Output('D', 'Releve_Pret_' . $pret['id_pret'] . '_' . date('Ymd') . '.pdf');
?>