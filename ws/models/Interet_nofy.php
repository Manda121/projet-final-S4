<?php
namespace App;

use PDO;

class Interet_nofy {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }
  public function getTotalPretEtInteret($id_etablissement, $date_debut, $date_fin) {
   $stmt = $this->db->prepare("
    SELECT 
        DATE_FORMAT(months.month_date, '%Y-%m') AS annee_mois,
        COALESCE(SUM(p.montant * tx.taux / 1200), 0) AS interet_mensuel,
        COALESCE(SUM(p.montant * p.taux_assurance / 1200), 0) AS assurance_mensuelle,
        COALESCE(SUM(p.montant * (tx.taux + p.taux_assurance) / 1200), 0) AS charges_totales,
        COUNT(p.id_pret) AS nombre_pret
    FROM (
        SELECT 
            DATE_ADD(
                DATE_FORMAT(:date_debut, '%Y-%m-01'), 
                INTERVAL n MONTH
            ) AS month_date,
            LAST_DAY(DATE_ADD(DATE_FORMAT(:date_debut, '%Y-%m-01'), INTERVAL n MONTH)) AS month_end
        FROM (
            SELECT 0 AS n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
            UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9
            UNION SELECT 10 UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14
            UNION SELECT 15 UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19
            UNION SELECT 20 UNION SELECT 21 UNION SELECT 22 UNION SELECT 23
        ) numbers
        WHERE DATE_ADD(DATE_FORMAT(:date_debut, '%Y-%m-01'), INTERVAL n MONTH) <= :date_fin
    ) months
    LEFT JOIN finance_s4_pret p ON 
        p.id_taux IN (
            SELECT t.id_taux 
            FROM finance_s4_taux t
            JOIN finance_s4_type_pret tp ON t.id_type_pret = tp.id_type_pret
            WHERE tp.id_etablissement = :id_etablissement
        )
        AND p.date_pret <= months.month_end
        AND (p.date_limite >= months.month_date OR p.date_limite IS NULL)
        AND p.etat = 'validee'
    LEFT JOIN finance_s4_taux tx ON tx.id_taux = p.id_taux
    LEFT JOIN finance_s4_type_pret tp ON tp.id_type_pret = tx.id_type_pret
    GROUP BY annee_mois, months.month_date
    ORDER BY months.month_date
");
    
    $stmt->execute([
        ':id_etablissement' => $id_etablissement,
        ':date_debut' => $date_debut,
        ':date_fin' => $date_fin
    ]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}   
 
