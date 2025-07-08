<?php
use Controllers\PretControllerRojo;
use Controllers\GestionPretControllerRojo;
use Controllers\UserControllerRojo;
use Controllers\RemboursementControllerRojo;



$db = getDB();

$remboursement_controller = new RemboursementControllerRojo($db);

$user_controller = new UserControllerRojo($db);
$controller = new PretControllerRojo($db);
$gestion_controller = new GestionPretControllerRojo($db);

Flight::route('GET /remboursement-simulate/@id_pret', [$remboursement_controller, 'simulateRemboursement']);
Flight::route('POST /remboursement-payment', [$remboursement_controller, 'recordPayment']);
Flight::route('POST /users', [$user_controller, 'addUser']);
Flight::route('GET /users', [$user_controller, 'getUsers']);
Flight::route('GET /users', [$controller, 'getAll']);
Flight::route('GET /users/@id', [$controller, 'getById']);
Flight::route('POST /users', [$controller, 'create']);
Flight::route('PUT /users/@id', [$controller, 'update']);
Flight::route('DELETE /users/@id', [$controller, 'delete']);
Flight::route('GET /ajout-pret-complet', function() {
    Flight::render('ajout_pret_complet.php');
});
Flight::route('POST /prets', [$controller, 'createPret']);
Flight::route('GET /prets', [$controller, 'getAllPrets']);
Flight::route('GET /types-pret-by-user', [$controller, 'getTypesPretByUser']);
Flight::route('GET /clients', [$controller, 'getClients']);
Flight::route('GET /taux-by-type-pret', [$controller, 'getTauxByTypePret']);

Flight::route('GET /prets-en-attente', [$gestion_controller, 'getPretsEnAttente']);
Flight::route('GET /pret-details', [$gestion_controller, 'getPretDetails']);
Flight::route('PUT /prets/@id_pret/valider', [$gestion_controller, 'validerPret']);
Flight::route('PUT /prets/@id_pret/refuser', [$gestion_controller, 'refuserPret']);
Flight::route('GET /prets-valides', [$controller, 'getPretsValides']);