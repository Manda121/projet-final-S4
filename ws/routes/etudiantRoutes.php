<?php
use Controllers\EtudiantController;

$db = getDB();
$controller = new EtudiantController($db);

Flight::route('GET /etudiants', [$controller, 'getAll']);
Flight::route('GET /etudiants/@id', [$controller, 'getById']);
Flight::route('POST /etudiants', [$controller, 'create']);
Flight::route('PUT /etudiants/@id', [$controller, 'update']);
Flight::route('DELETE /etudiants/@id', [$controller, 'delete']);
