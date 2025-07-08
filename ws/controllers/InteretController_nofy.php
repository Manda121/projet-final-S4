<?php
namespace Controllers;

use App\Interet_nofy;
use Flight;

class InteretController_nofy {
    private $model;

    public function __construct($db) {
        $this->model = new Interet_nofy($db);
    }

    public function filtreInteret($id) {
        $data = Flight::request()->data->getData();
        $reponse=$this->model->getTotalPretEtInteret($id, $data['debut'], $data['fin']);        
        Flight::json(['datas'=>$reponse]);
    }
    public function filtreMontant($id) {
        $data = Flight::request()->data->getData();
        $reponse=$this->model->getMontantDisponibleParMois($id, $data['date_debut'], $data['date_fin']);        
        Flight::json(['datas'=>$reponse]);
    }
    

    
}
