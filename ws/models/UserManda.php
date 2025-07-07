<?php

namespace App;

use PDO;

class UserManda
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM finance_s4_user");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM finance_s4_user WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM finance_s4_user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO finance_s4_user (nom, prenom, email, date_de_naissance, mot_de_passe, role_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['date_de_naissance'],
            password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
            $data['role_user'] ?? 'client' // Par défaut 'client' si non spécifié
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE finance_s4_user SET nom = ?, prenom = ?, email = ?, date_de_naissance = ? WHERE id_user = ?";

        $params = [
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['date_de_naissance'],
            $id
        ];

        // Optionnel: mise à jour du mot de passe si fourni
        if (!empty($data['mot_de_passe'])) {
            $sql = "UPDATE finance_s4_user SET nom = ?, prenom = ?, email = ?, date_de_naissance = ?, mot_de_passe = ? WHERE id_user = ?";

            $params = [
                $data['nom'],
                $data['prenom'],
                $data['email'],
                $data['date_de_naissance'],
                password_hash($data['mot_de_passe'], PASSWORD_DEFAULT),
                $id
            ];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM finance_s4_user WHERE id_user = ?");
        return $stmt->execute([$id]);
    }

    public function verifyCredentials($email, $password)
    {
        $user = $this->getByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'error' => 'email',
                'message' => 'Email inconnu'
            ];
        }

        if ($password !== $user['mot_de_passe']) {
            return [
                'success' => false,
                'error' => 'password',
                'message' => 'Mot de passe incorrect'
            ];
        }

        return [
            'success' => true,
            'user' => $user,
            'message' => 'Authentification réussie'
        ];
    }
}
