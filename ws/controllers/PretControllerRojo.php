<?php
namespace Controllers;

use App\PretRojo;
use Flight;

class PretControllerRojo {
    private $model;

    public function __construct($db) {
        $this->model = new PretRojo($db);
    }

    public function getAll() {
        Flight::json($this->model->getAll());
    }

    public function getClients() {
        Flight::json($this->model->getClients());
    }

    public function getById($id) {
        Flight::json($this->model->getById($id));
    }

    public function create() {
        $data = Flight::request()->data->getData();
        $id = $this->model->create($data);
        Flight::json(['message' => 'Utilisateur ajouté', 'id' => $id]);
    }

    public function update($id) {
        $data = Flight::request()->data->getData();
        $this->model->update($id, $data);
        Flight::json(['message' => 'Utilisateur modifié']);
    }

    public function delete($id) {
        $this->model->delete($id);
        Flight::json(['message' => 'Utilisateur supprimé']);
    }

    public function createPret() {
        $data = Flight::request()->data->getData();
        $result = $this->model->createPret($data);
        if (isset($result['error'])) {
            Flight::json(['message' => $result['error']], 400);
        } else {
            Flight::json(['message' => 'Prêt ajouté', 'id' => $result['id']]);
        }
    }

    public function getAllPrets() {
        try {
            $prets = $this->model->getAllPrets();
            Flight::json($prets);
        } catch (PDOException $e) {
            error_log("Erreur dans getAllPrets: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des prêts.'], 500);
        }
    }

    public function getTypesPretByUser() {
        try {
            $id_user = Flight::request()->query['id_user'] ?? null;
            if (!$id_user) {
                throw new PDOException("L'ID de l'utilisateur est requis.");
            }
            $types = $this->model->getTypesPretByUser($id_user);
            Flight::json($types);
        } catch (PDOException $e) {
            error_log("Erreur dans getTypesPretByUser: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des types de prêts: ' . $e->getMessage()], 400);
        }
    }

    public function getTauxByTypePret() {
        try {
            $id_type_pret = Flight::request()->query['id_type_pret'] ?? null;
            if (!$id_type_pret) {
                throw new PDOException("L'ID du type de prêt est requis.");
            }
            $taux = $this->model->getTauxByTypePret($id_type_pret);
            Flight::json($taux);
        } catch (PDOException $e) {
            error_log("Erreur dans getTauxByTypePret: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des taux: ' . $e->getMessage()], 400);
        }
    }

    public function getPretsValides() {
        try {
            $prets = $this->model->getPretsValides();
            Flight::json($prets);
        } catch (PDOException $e) {
            error_log("Erreur dans getPretsValides: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des prêts validés.'], 500);
        }
    }
}