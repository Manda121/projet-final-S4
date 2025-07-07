<?php

namespace Controllers;

use App\UserManda;
use Flight;

class UserControllerManda
{
    private $model;

    public function __construct($db)
    {
        $this->model = new UserManda($db);
    }

    public function getAll()
    {
        Flight::json($this->model->getAll());
    }

    public function getById($id)
    {
        Flight::json($this->model->getById($id));
    }

    public function create()
    {
        $data = Flight::request()->data->getData();
        
        // Validation des champs obligatoires
        $requiredFields = ['nom', 'prenom', 'email', 'date_de_naissance', 'mot_de_passe'];

        $id = $this->model->create($data);
        Flight::json(['message' => 'Utilisateur créé', 'id' => $id], 201);
    }

    public function update($id)
    {
        $data = Flight::request()->data->getData();
        
        if (!isset($data['nom']) || !isset($data['prenom']) || !isset($data['email'])) {
            Flight::halt(400, "Les champs nom, prenom et email sont obligatoires");
        }

        $this->model->update($id, $data);
        Flight::json(['message' => 'Utilisateur mis à jour']);
    }

    public function login()
    {
        $data = Flight::request()->data->getData();
        
        if (!isset($data['email']) || !isset($data['mot_de_passe'])) {
            Flight::halt(400, "Email et mot de passe sont requis");
        }

        $result = $this->model->verifyCredentials($data['email'], $data['mot_de_passe']);
        
        if (!$result['success']) {
            Flight::halt(401, $result['message']);
        }

        // Retourner les infos utilisateur sans le mot de passe
        unset($result['user']['mot_de_passe']);
        Flight::json($result);
    }

    public function delete($id)
    {
        $this->model->delete($id);
        Flight::json(['message' => 'Utilisateur supprimé']);
    }
}