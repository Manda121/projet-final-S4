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
        $id = $this->model->create($data);
        Flight::json(['message' => 'Type pret ajouté', 'id' => $id]);
    }

    public function update($id)
    {
        parse_str(file_get_contents("php://input"), $data);  // <-- ici
        // var_dump($data); exit; // décommenter pour debug

        if (!isset($data['libelle'])) {
            Flight::halt(400, "Le champ libelle est obligatoire");
        }

        $this->model->update($id, $data);

        Flight::json(['message' => 'Type de prêt modifié']);
    }


    // public function delete($id) {
    //     $this->model->delete($id);
    //     Flight::json(['message' => 'Étudiant supprimé']);
    // }
}
