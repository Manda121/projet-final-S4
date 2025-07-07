<?php
use Controllers\TypePretControllerManda;

$db = getDB();
$controller = new TypePretControllerManda($db);

Flight::route('GET /types_prets', [$controller, 'getAll']);
Flight::route('GET /types_prets/@id', [$controller, 'getById']);
Flight::route('POST /types_prets', [$controller, 'create']);
Flight::route('PUT /types_prets/@id', [$controller, 'update']);
Flight::route('DELETE /types_prets/@id', [$controller, 'delete']);
