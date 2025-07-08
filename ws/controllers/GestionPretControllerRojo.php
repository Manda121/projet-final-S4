<?php
namespace Controllers;

use App\GestionPretRojo;
use Flight;

class GestionPretControllerRojo {
    private $model;

    public function __construct($db) {
        $this->model = new GestionPretRojo($db); // Correction ici
    }

    public function getPretsEnAttente() {
        try {
            $prets = $this->model->getPretsEnAttente();
            Flight::json($prets);
        } catch (PDOException $e) {
            error_log("Erreur dans getPretsEnAttente: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des prêts en attente.'], 500);
        }
    }
    
    public function getPretDetails($id_pret) {
        try {
            $pret = $this->model->getPretDetails($id_pret);
            if ($pret) {
                Flight::json($pret);
            } else {
                Flight::json(['message' => 'Prêt non trouvé.'], 404);
            }
        } catch (PDOException $e) {
            error_log("Erreur dans getPretDetails: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des détails du prêt.'], 500);
        }
    }
    
    public function validerPret($id_pret) {
        $result = $this->model->validerPret($id_pret);
        Flight::json($result);
    }
    
    public function refuserPret($id_pret) {
        $result = $this->model->refuserPret($id_pret);
        Flight::json($result);
    }
}