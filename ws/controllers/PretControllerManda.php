<?php
namespace Controllers;

use App\PretManda;
use Flight;

class PretControllerManda
{
    private $model;

    public function __construct($db)
    {
        $this->model = new PretManda($db);
    }

    public function getAll()
    {
        $filters = Flight::request()->query->getData();
        Flight::json($this->model->getAll($filters));
    }

    public function getById($id_pret)
    {
        $pret = $this->model->getById($id_pret);
        if (!$pret) {
            Flight::halt(404, "Prêt non trouvé");
        }
        Flight::json($pret);
    }
}