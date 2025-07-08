<?php
namespace App;

use PDO;
use DateTime;

class GestionPretRojo {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getPretsEnAttente(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM finance_s4_pret WHERE etat = 'en attente' ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getPretsEnAttente: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function getPretDetails($id_pret): array {
        try {
            $stmt = $this->db->prepare("SELECT p.*, t.taux FROM finance_s4_pret p JOIN finance_s4_taux t ON p.id_taux = t.id_taux WHERE p.id_pret = ?");
            $stmt->execute([$id_pret]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Database error in getPretDetails: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function validerPret($id_pret): array {
        try {
            $stmt = $this->db->prepare("UPDATE finance_s4_pret SET etat = 'validee' WHERE id_pret = ? AND etat = 'en attente'");
            $stmt->execute([$id_pret]);
            return ['message' => $stmt->rowCount() ? 'Prêt validé avec succès.' : 'Échec : Prêt non trouvé ou déjà traité.'];
        } catch (PDOException $e) {
            error_log("Database error in validerPret: " . $e->getMessage());
            return ['message' => 'Échec de la validation du prêt: ' . $e->getMessage()];
        }
    }
    
    public function refuserPret($id_pret): array {
        try {
            $stmt = $this->db->prepare("UPDATE finance_s4_pret SET etat = 'refusee' WHERE id_pret = ? AND etat = 'en attente'");
            $stmt->execute([$id_pret]);
            return ['message' => $stmt->rowCount() ? 'Prêt refusé avec succès.' : 'Échec : Prêt non trouvé ou déjà traité.'];
        } catch (PDOException $e) {
            error_log("Database error in refuserPret: " . $e->getMessage());
            return ['message' => 'Échec du refus du prêt: ' . $e->getMessage()];
        }
    }
}