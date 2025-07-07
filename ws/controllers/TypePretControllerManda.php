<?php
namespace Controllers;

use App\TypePretManda;
use Flight;

class TypePretControllerManda
{
    private $model;

    public function __construct($db)
    {
        $this->model = new TypePretManda($db);
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
        if (!isset($data['libelle']) || !isset($data['taux']) || !isset($data['montantMinimum']) || !isset($data['montantMaximum']) || !isset($data['delaiMax'])) {
            Flight::halt(400, "Tous les champs sont obligatoires");
        }
        $id = $this->model->create($data);
        Flight::json(['message' => 'Type pret ajouté', 'id' => $id]);
    }

    public function update($id)
    {
        parse_str(file_get_contents("php://input"), $data);
        if (!isset($data['libelle']) || !isset($data['taux']) || !isset($data['montantMinimum']) || !isset($data['montantMaximum']) || !isset($data['delaiMax'])) {
            Flight::halt(400, "Tous les champs sont obligatoires");
        }
        $this->model->update($id, $data);
        Flight::json(['message' => 'Type de prêt modifié']);
    }
}