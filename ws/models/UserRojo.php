<?php
namespace App;

use PDO;

class UserRojo {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function createUser($nom, $prenom, $email, $date_de_naissance, $mot_de_passe) {
        $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO finance_s4_user (nom, prenom, email, date_de_naissance, mot_de_passe, role_user) VALUES (:nom, :prenom, :email, :date_de_naissance, :mot_de_passe, 'client')");
        return $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':date_de_naissance' => $date_de_naissance,
            ':mot_de_passe' => $hashed_password
        ]);
    }

    public function getAllUsers() {
        $stmt = $this->db->query("SELECT id_user, nom, prenom, email, date_de_naissance, role_user, mot_de_passe FROM finance_s4_user WHERE role_user = 'client'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}