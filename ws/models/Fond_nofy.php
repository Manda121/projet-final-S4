<?php
namespace App;

use PDO;

class Fond_nofy {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getFondAddById($id) {
    $stmt = $this->db->prepare("SELECT * FROM finance_s4_fond WHERE id_etablissement = ?");
    $stmt->execute([$id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    public function getSommeById($id) {
        $stmt = $this->db->prepare("SELECT SUM(montant) FROM finance_s4_fond WHERE id_etablissement  = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function AddFondByIdEtablissement($fond,$id) {
        $stmt = $this->db->prepare("INSERT INTO finance_s4_fond( id_etablissement, montant) VALUES (?, ?)");
        $stmt->execute([$id, $fond]);
        return $this->db->lastInsertId();
    }
}
