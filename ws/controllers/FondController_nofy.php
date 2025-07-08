<?php
namespace Controllers;

use App\Fond_nofy;
use Flight;

class FondController_nofy {
    private $model;

    public function __construct($db) {
        $this->model = new Fond_nofy($db);
    }

    public function getAll($id) {
        Flight::json($this->model->getFondAddById($id));
    }

    public function Addfond() {
        $data = Flight::request()->data->getData();
        $id = $this->model->AddFondByIdEtablissement($data['montant'],$data['id']);
        Flight::json(['message' => 'done', 'id' => $id]);
    }

    
}
