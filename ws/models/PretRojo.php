<?php
namespace App;

use PDO;
use DateTime;

class PretRojo {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT id_user, nom, prenom, email FROM finance_s4_user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClients() {
        $stmt = $this->db->query("SELECT id_user, nom, prenom, email FROM finance_s4_user WHERE role_user = 'client'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM finance_s4_user WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO finance_s4_user (nom, prenom, email, date_de_naissance, mot_de_passe, role_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $data['date_de_naissance'], $data['mot_de_passe'], $data['role_user']]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE finance_s4_user SET nom = ?, prenom = ?, email = ?, date_de_naissance = ?, mot_de_passe = ?, role_user = ? WHERE id_user = ?");
        return $stmt->execute([$data['nom'], $data['prenom'], $data['email'], $data['date_de_naissance'], $data['mot_de_passe'], $data['role_user'], $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM finance_s4_user WHERE id_user = ?");
        return $stmt->execute([$id]);
    }

    public function createPret($data) {
        $id_user = $data['id_user'] ?? null;
        $date_pret = $data['date_pret'] ?? null;
        $description = $data['description'] ?? null;
        $montant = $data['montant'] ?? null;
        $date_limite = $data['date_limite'] ?? null;
        $taux_assurance = $data['taux_assurance'] ?? null;
        $id_taux = $data['id_taux'] ?? null;
        $etat = $data['etat'] ?? 'en attente';
    
        if (!$id_user || !$date_pret || !$description || !$montant || !$date_limite || !$taux_assurance || !$id_taux) {
            return ['error' => 'Tous les champs sont requis sauf l\'état.'];
        }
    
        // Récupérer l'id_etablissement à partir de id_taux -> id_type_pret
        $stmt = $this->db->prepare("SELECT tp.id_etablissement FROM finance_s4_taux t JOIN finance_s4_type_pret tp ON t.id_type_pret = tp.id_type_pret WHERE t.id_taux = ?");
        $stmt->execute([$id_taux]);
        $etablissement = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$etablissement) {
            return ['error' => 'Taux invalide ou non associé à un établissement.'];
        }
        $id_etablissement = $etablissement['id_etablissement'];
    
        // Vérifier si le fond de l'établissement est suffisant
        $stmt = $this->db->prepare("SELECT montant FROM finance_s4_fond WHERE id_etablissement = ?");
        $stmt->execute([$id_etablissement]);
        $fond = $stmt->fetchColumn();
        if ($fond === false) {
            return ['error' => 'Aucun fond défini pour cet établissement.'];
        }
        if ($montant > $fond) {
            return ['error' => 'Le fond de l\'établissement est insuffisant pour ce prêt.'];
        }
    
        // // Vérifier si l'utilisateur a déjà un prêt en cours
        // $stmt = $this->db->prepare("SELECT COUNT(*) FROM finance_s4_pret WHERE id_user = ? AND etat NOT IN ('refusee')");
        // $stmt->execute([$id_user]);
        // $pret_count = $stmt->fetchColumn();
        // if ($pret_count > 0) {
        //     return ['error' => 'L\'utilisateur a déjà un prêt en cours.'];
        // }
    
        // Vérifier si la date limite est dans le futur
        $current_date = new DateTime();
        $limit_date = new DateTime($date_limite);
        if ($limit_date <= $current_date) {
            return ['error' => 'La date limite doit être dans le futur.'];
        }
    
        // Insérer le prêt
        $stmt = $this->db->prepare("INSERT INTO finance_s4_pret (id_user, id_taux, taux_assurance, date_pret, description, montant, date_limite, etat) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_user, $id_taux, $taux_assurance, $date_pret, $description, $montant, $date_limite, $etat]);
        return ['success' => true, 'id' => $this->db->lastInsertId()];
    }

    public function getAllPrets(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM finance_s4_pret");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getAllPrets: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTypesPretByUser($id_user): array {
        try {
            $stmt = $this->db->prepare("
                SELECT tp.* 
                FROM finance_s4_type_pret tp
                JOIN finance_s4_etablissement_user eu ON tp.id_etablissement = eu.id_etablissement
                WHERE eu.id_user = ?
            ");
            $stmt->execute([$id_user]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getTypesPretByUser: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTauxByTypePret($id_type_pret): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM finance_s4_taux WHERE id_type_pret = ?");
            $stmt->execute([$id_type_pret]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in getTauxByTypePret: " . $e->getMessage());
            throw $e;
        }
    }

    public function getPretsValides() {
        $stmt = $this->db->query("SELECT id_pret, montant, date_pret, date_limite FROM finance_s4_pret WHERE etat = 'validee'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}