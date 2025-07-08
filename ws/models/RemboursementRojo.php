<?php
namespace App;

use PDO;

class RemboursementRojo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getPretDetailsForSimulation($id_pret) {
        $stmt = $this->db->prepare("SELECT p.id_pret, CAST(p.montant AS DECIMAL(15,2)) AS montant, p.id_taux, CAST(p.taux_assurance AS DECIMAL(5,2)) AS taux_assurance, p.date_pret, p.date_limite, CAST(t.taux AS DECIMAL(5,2)) AS taux_annuel 
                                  FROM finance_s4_pret p 
                                  JOIN finance_s4_taux t ON p.id_taux = t.id_taux 
                                  WHERE p.id_pret = :id_pret AND p.etat = 'validee'");
        $stmt->execute([':id_pret' => $id_pret]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function generateEcheancier($id_pret, $montant, $taux_annuel, $date_pret, $date_limite) {
        $taux_mensuel = $taux_annuel / 100 / 12;
        $date1 = new \DateTime($date_pret); // Utilisation de \DateTime
        $date2 = new \DateTime($date_limite); // Utilisation de \DateTime
        $duree_mois = (int)($date1->diff($date2)->y * 12 + $date1->diff($date2)->m + ($date1->diff($date2)->d > 0 ? 1 : 0));
        $annuite = $montant * $taux_mensuel / (1 - pow(1 + $taux_mensuel, -$duree_mois));
        $echeancier = [];
        $capital_restant = $montant;
        $date_paiement = new \DateTime($date_pret); // Utilisation de \DateTime
        $date_paiement->modify('+1 month');

        for ($mois = 1; $mois <= $duree_mois; $mois++) {
            $interet = $capital_restant * $taux_mensuel;
            $capital_rembourse = $annuite - $interet;
            $echeancier[] = [
                'mois' => $mois,
                'capital_restant' => round($capital_restant, 2),
                'interet' => round($interet, 2),
                'capital_rembourse' => round($capital_rembourse, 2),
                'annuite' => round($annuite, 2),
                'date_paiement' => $date_paiement->format('Y-m-d')
            ];
            $capital_restant -= $capital_rembourse;
            $date_paiement->modify('+1 month');
        }
        return $echeancier;
    }

    public function getRemisesByPret($id_pret) {
        $stmt = $this->db->prepare("SELECT id_remise, montant, date_remise FROM finance_s4_remise WHERE id_pret = :id_pret ORDER BY date_remise ASC");
        $stmt->execute([':id_pret' => $id_pret]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recordRemise($id_pret, $montant, $date_remise) {
        $stmt = $this->db->prepare("INSERT INTO finance_s4_remise (id_pret, montant, date_remise) VALUES (:id_pret, :montant, :date_remise)");
        return $stmt->execute([
            ':id_pret' => $id_pret,
            ':montant' => $montant,
            ':date_remise' => $date_remise
        ]);
    }

    public function getTotalRemis($id_pret) {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(montant), 0) as total FROM finance_s4_remise WHERE id_pret = :id_pret");
        $stmt->execute([':id_pret' => $id_pret]);
        return $stmt->fetchColumn();
    }
}