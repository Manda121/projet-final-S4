<?php
namespace Controllers;

use App\UserRojo;
use Flight;

class UserControllerRojo {
    private $model;

    public function __construct($db) {
        $this->model = new UserRojo($db);
    }

    public function addUser() {
        $nom = Flight::request()->data->nom;
        $prenom = Flight::request()->data->prenom;
        $email = Flight::request()->data->email;
        $date_de_naissance = Flight::request()->data->date_de_naissance;
        $mot_de_passe = Flight::request()->data->mot_de_passe;

        if (!$nom || !$prenom || !$email || !$date_de_naissance || !$mot_de_passe) {
            Flight::json(['message' => 'Tous les champs sont requis.'], 400);
            return;
        }

        try {
            $result = $this->model->createUser($nom, $prenom, $email, $date_de_naissance, $mot_de_passe);
            if ($result) {
                Flight::json(['message' => 'Utilisateur ajouté avec succès.', 'success' => true]);
            } else {
                Flight::json(['message' => 'Échec de l\'ajout de l\'utilisateur.', 'success' => false], 500);
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Code d'erreur MySQL pour doublon (email unique)
                Flight::json(['message' => 'Cet email est déjà utilisé.', 'success' => false], 400);
            } else {
                error_log("Erreur dans addUser: " . $e->getMessage());
                Flight::json(['message' => 'Erreur serveur.', 'success' => false], 500);
            }
        }
    }

    public function getUsers() {
        try {
            $users = $this->model->getAllUsers();
            // Ne pas renvoyer les mots de passe pour des raisons de sécurité
            foreach ($users as &$user) {
                unset($user['mot_de_passe']);
            }
            Flight::json($users);
        } catch (PDOException $e) {
            error_log("Erreur dans getUsers: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des utilisateurs.'], 500);
        }
    }
}