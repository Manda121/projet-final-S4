<?php
namespace App;

use PDO;

class TypePretManda
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $id = $_SESSION["id_etablissement"];
        $stmt = $this->db->query("SELECT 
            tp.id_type_pret,
            tp.libelle,
            tp.montant_min,
            tp.montant_max,
            tp.delai_mois_max,
            GROUP_CONCAT(t.taux ORDER BY t.id_taux SEPARATOR ', ') AS taux
        FROM finance_s4_type_pret tp
        LEFT JOIN finance_s4_taux t ON tp.id_type_pret = t.id_type_pret
        WHERE tp.id_etablissement = $id
        GROUP BY tp.id_type_pret, tp.libelle, tp.montant_min, tp.montant_max, tp.delai_mois_max");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT 
            tp.*,
            GROUP_CONCAT(t.taux ORDER BY t.id_taux SEPARATOR ', ') AS taux
        FROM finance_s4_type_pret tp
        LEFT JOIN finance_s4_taux t ON tp.id_type_pret = t.id_type_pret
        WHERE tp.id_type_pret = ?
        GROUP BY tp.id_type_pret");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $this->db->beginTransaction();
        try {
            // Insert into finance_s4_type_pret (without taux)
            $stmt = $this->db->prepare("INSERT INTO finance_s4_type_pret (libelle, id_etablissement, montant_min, montant_max, delai_mois_max) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$data['libelle'], $_SESSION["id_etablissement"], $data['montantMinimum'], $data['montantMaximum'], $data['delaiMax']]);
            $id_type_pret = $this->db->lastInsertId();

            // Insert rates into finance_s4_taux
            $taux = json_decode($data['taux'], true);
            if (!empty($taux)) {
                $stmt = $this->db->prepare("INSERT INTO finance_s4_taux (id_type_pret, taux) VALUES (?, ?)");
                foreach ($taux as $rate) {
                    $stmt->execute([$id_type_pret, $rate]);
                }
            }

            $this->db->commit();
            return $id_type_pret;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $this->db->beginTransaction();
        try {
            // Update finance_s4_type_pret (without taux)
            $stmt = $this->db->prepare("UPDATE finance_s4_type_pret SET libelle = ?, montant_min = ?, montant_max = ?, delai_mois_max = ? WHERE id_type_pret = ?");
            $stmt->execute([$data['libelle'], $data['montantMinimum'], $data['montantMaximum'], $data['delaiMax'], $id]);

            // Delete existing rates
            $stmt = $this->db->prepare("DELETE FROM finance_s4_taux WHERE id_type_pret = ?");
            $stmt->execute([$id]);

            // Insert new rates
            $taux = json_decode($data['taux'], true);
            if (!empty($taux)) {
                $stmt = $this->db->prepare("INSERT INTO finance_s4_taux (id_type_pret, taux) VALUES (?, ?)");
                foreach ($taux as $rate) {
                    $stmt->execute([$id, $rate]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}