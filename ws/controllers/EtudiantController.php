<?php
namespace Controllers;

use App\Etudiant;
use Flight;

class EtudiantController {
    private $model;

    public function __construct($db) {
        $this->model = new Etudiant($db);
    }

    public function getAll() {
        Flight::json($this->model->getAll());
    }

    public function getById($id) {
        Flight::json($this->model->getById($id));
    }

    public function create() {
        $data = Flight::request()->data->getData();
        $id = $this->model->create($data);
        Flight::json(['message' => 'Étudiant ajouté', 'id' => $id]);
    }

    public function update($id) {
        $data = Flight::request()->data->getData();
        $this->model->update($id, $data);
        Flight::json(['message' => 'Étudiant modifié']);
    }

    public function delete($id) {
        $this->model->delete($id);
        Flight::json(['message' => 'Étudiant supprimé']);
    }
}
