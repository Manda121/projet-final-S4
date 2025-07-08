<?php
namespace Controllers;

use App\Pret_nofy;
use Flight;

class PretController_nofy {
    private $model;

    public function __construct($db) {
        $this->model = new Pret_nofy($db);
    }

    public function simuler() {
      $data = Flight::request()->data->getData();
        $reponse=$this->model->simulerPretValideParId($data['id_pret']);        
        Flight::json(['datas'=>$reponse]);
    }

    public function getValiderPret() {
      $data = Flight::request()->data->getData();
        $reponse=$this->model->getValiderPret();        
        Flight::json(['datas'=>$reponse]);
    }

    
}
