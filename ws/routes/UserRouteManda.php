<?php
use Controllers\UserControllerManda;

$db = getDB();
$controller = new UserControllerManda($db);

Flight::route('GET /users', [$controller, 'getAll']);
Flight::route('GET /users/@id', [$controller, 'getById']);
Flight::route('POST /users', [$controller, 'create']);
Flight::route('POST /users/login', [$controller, 'login']);
Flight::route('PUT /users/@id', [$controller, 'update']);
Flight::route('DELETE /users/@id', [$controller, 'delete']);
Flight::route('GET /etablissements', [$controller, 'getEtablissements']);
Flight::route('GET /stats', [$controller, 'getStats']);