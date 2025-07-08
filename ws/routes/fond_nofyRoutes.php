<?php
use Controllers\FondController_nofy;

$db = getDB();
$controller = new FondController_nofy($db);

Flight::route('GET /fondAll/@id', [$controller, 'getAll']);
Flight::route('POST /fond', [$controller, 'Addfond']);
