<?php
use Controllers\PretControllerManda;

$db = getDB();
$controller = new PretControllerManda($db);

Flight::route('GET /prets', [$controller, 'getAll']);
Flight::route('GET /prets/@id_pret', [$controller, 'getById']);
// Flight::route('POST /types_prets', [$controller, 'create']);
// Flight::route('PUT /types_prets/@id', [$controller, 'update']);
Flight::route('DELETE /types_prets/@id', [$controller, 'delete']);
