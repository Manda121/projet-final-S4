<?php
function getDB() {
    $host = '127.0.0.1';
    $dbname = 'tp_flight';
    $username = 'root';
    $password = 'mysql';

    try {
        return new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}
