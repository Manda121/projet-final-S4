<?php
namespace Controllers;

use App\RemboursementRojo;
use Flight;

class RemboursementControllerRojo {
    private $model;

    public function __construct($db) {
        $this->model = new RemboursementRojo($db);
    }

    public function simulateRemboursement($id_pret) {
        try {
            $pret = $this->model->getPretDetailsForSimulation($id_pret);
            if ($pret) {
                $echeancier = $this->model->generateEcheancier(
                    $pret['id_pret'],
                    $pret['montant'],
                    $pret['taux_annuel'],
                    $pret['date_pret'],
                    $pret['date_limite']
                );
                $remises = $this->model->getRemisesByPret($id_pret);
                $total_remis = $this->model->getTotalRemis($id_pret);
                Flight::json(['echeancier' => $echeancier, 'pret' => $pret, 'remises' => $remises, 'total_remis' => $total_remis]);
            } else {
                Flight::json(['message' => 'Prêt non trouvé ou non validé.'], 404);
            }
        } catch (PDOException $e) {
            error_log("Erreur dans simulateRemboursement: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la simulation du remboursement.'], 500);
        }
    }

    public function recordPayment() {
        $id_pret = Flight::request()->data->id_pret;
        $montant = Flight::request()->data->montant;
        $date_remise = Flight::request()->data->date_remise;

        if (!$id_pret || !$montant || !$date_remise) {
            Flight::json(['message' => 'Tous les champs sont requis (id_pret, montant, date_remise).'], 400);
            return;
        }

        try {
            $result = $this->model->recordRemise($id_pret, $montant, $date_remise);
            Flight::json(['message' => $result ? 'Paiement enregistré avec succès.' : 'Échec de l\'enregistrement du paiement.', 'success' => $result]);
        } catch (PDOException $e) {
            error_log("Erreur dans recordPayment: " . $e->getMessage());
            Flight::json(['message' => 'Échec de l\'enregistrement du paiement.'], 500);
        }
    }

    public function saveSimulation() {
        $id_pret = Flight::request()->data->id_pret;
        if (!$id_pret) {
            Flight::json(['message' => 'ID du prêt requis.'], 400);
            return;
        }

        try {
            $pret = $this->model->getPretDetailsForSimulation($id_pret);
            if ($pret) {
                $echeancier = $this->model->generateEcheancier(
                    $pret['id_pret'],
                    $pret['montant'],
                    $pret['taux_annuel'],
                    $pret['date_pret'],
                    $pret['date_limite']
                );
                $result = $this->model->saveSimulation(
                    $pret['id_pret'],
                    $pret['montant'],
                    $pret['taux_annuel'],
                    $pret['date_pret'],
                    $pret['date_limite'],
                    $echeancier
                );
                Flight::json($result);
            } else {
                Flight::json(['message' => 'Prêt non trouvé ou non validé.'], 404);
            }
        } catch (PDOException $e) {
            error_log("Erreur dans saveSimulation: " . $e->getMessage());
            Flight::json(['message' => 'Échec de l\'enregistrement de la simulation.'], 500);
        }
    }

    public function getSimulations() {
        try {
            $simulations = $this->model->getSimulations();
            Flight::json($simulations);
        } catch (PDOException $e) {
            error_log("Erreur dans getSimulations: " . $e->getMessage());
            Flight::json(['message' => 'Échec de la récupération des simulations.'], 500);
        }
    }
}
?>