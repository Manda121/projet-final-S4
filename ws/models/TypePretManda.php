<?php
namespace App;

use PDO;

class TypePretManda {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAll() {
        $id = $_SESSION["id_etablissement"];
        $stmt = $this->db->query("SELECT * FROM finance_s4_type_pret where id_etablissement = $id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM finance_s4_type_pret WHERE id_type_pret = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->db->prepare("INSERT INTO finance_s4_type_pret (libelle, id_etablissement, taux, montant_min, montant_max, delai_mois_max) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$data['libelle'], $_SESSION["id_etablissement"], $data['taux'], $data['montantMinimum'], $data['montantMaximum'], $data['delaiMax']]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("UPDATE finance_s4_type_pret SET libelle = ?, taux = ?, montant_min = ?, montant_max = ?, delai_mois_max = ? WHERE id_type_pret = ?");
        return $stmt->execute([$data['libelle'], $data['taux'], $data['montantMinimum'], $data['montantMaximum'], $data['delaiMax'], $id]);
    }

    // public function delete($id) {
    //     $stmt = $this->db->prepare("DELETE FROM etudiant WHERE id = ?");
    //     return $stmt->execute([$id]);
    // }
}
