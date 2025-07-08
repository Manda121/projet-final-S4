<?php
use Controllers\PretController_nofy;

$db = getDB();
$controller = new PretController_nofy($db);

Flight::route('GET /pretValider', [$controller, 'getValiderPret']);
Flight::route('POST /simulerPret/', [$controller, 'simuler']);

