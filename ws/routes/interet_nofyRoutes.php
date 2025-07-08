<?php
use Controllers\InteretController_nofy;

$db = getDB();
$controller = new InteretController_nofy($db);

Flight::route('POST /interet/@id', [$controller, 'filtreInteret']);

