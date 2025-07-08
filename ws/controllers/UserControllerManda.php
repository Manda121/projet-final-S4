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
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                Flight::json(['success' => false, 'message' => "Le champ $field est requis"], 400);
                return;
            }
        }

        $result = $this->model->create($data);

        if ($result['success']) {
            Flight::json(['success' => true, 'message' => $result['message'], 'id' => $result['id']], 201);
        } else {
            Flight::json(['success' => false, 'message' => $result['message']], 400);
        }
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
        $_SESSION['id_user'] = $result['user']['id_user'];
        $_SESSION['id_etablissement'] = $result['user']['id_etablissement'] ?? null;
        Flight::json($result);
    }

    public function delete($id)
    {
        $this->model->delete($id);
        Flight::json(['message' => 'Utilisateur supprimé']);
    }

    public function getEtablissements()
    {
        $result = $this->model->getEtablissements();
        if (isset($result['success']) && !$result['success']) {
            Flight::json($result, 500);
        } else {
            Flight::json($result);
        }
    }
}
