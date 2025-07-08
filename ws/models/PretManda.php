<?php

namespace App;

use PDO;

class PretManda
{
    private $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll($filters = [])
    {
        $id_etablissement = $_SESSION["id_etablissement"];
        $query = "SELECT 
            p.id_pret,
            c.nom AS client_nom,
            c.prenom AS client_prenom,
            tp.libelle AS type_pret_libelle,
            p.montant,
            p.date_pret,
            p.etat,
            p.date_limite,
            t.taux
        FROM finance_s4_pret p
        JOIN finance_s4_user c ON p.id_user = c.id_user
        JOIN finance_s4_taux t ON p.id_taux = t.id_taux
        JOIN finance_s4_type_pret tp ON t.id_type_pret = tp.id_type_pret
        WHERE tp.id_etablissement = :id_etablissement";

        $params = [':id_etablissement' => $id_etablissement];

        if (!empty($filters['client'])) {
            $query .= " AND (c.nom LIKE :client OR c.prenom LIKE :client)";
            $params[':client'] = '%' . $filters['client'] . '%';
        }
        if (!empty($filters['date_debut'])) {
            $query .= " AND p.date_pret >= :date_debut";
            $params[':date_debut'] = $filters['date_debut'];
        }
        if (!empty($filters['date_fin'])) {
            $query .= " AND p.date_pret <= :date_fin";
            $params[':date_fin'] = $filters['date_fin'];
        }
        if (!empty($filters['montant_min'])) {
            $query .= " AND p.montant >= :montant_min";
            $params[':montant_min'] = $filters['montant_min'];
        }
        if (!empty($filters['montant_max'])) {
            $query .= " AND p.montant <= :montant_max";
            $params[':montant_max'] = $filters['montant_max'];
        }
        if (!empty($filters['type_pret'])) {
            $query .= " AND t.id_type_pret = :type_pret";
            $params[':type_pret'] = $filters['type_pret'];
        }
        if (!empty($filters['etat'])) {
            $query .= " AND p.etat = :etat";
            $params[':etat'] = $filters['etat'];
        }

        $query .= " ORDER BY p.date_pret DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id_pret)
    {
        $id_etablissement = $_SESSION["id_etablissement"];
        // Fetch loan details
        $query = "SELECT 
    p.id_pret,
    c.nom AS client_nom,
    c.prenom AS client_prenom,
    c.id_user AS id_client,
    tp.libelle AS type_pret_libelle,
    p.montant,
    p.taux_assurance,
    p.date_pret,
    p.date_limite,
    p.etat,
    p.description,
    t.taux,
    e.nom AS etablissement_nom
FROM finance_s4_pret p
JOIN finance_s4_user c ON p.id_user = c.id_user
JOIN finance_s4_taux t ON p.id_taux = t.id_taux
JOIN finance_s4_type_pret tp ON t.id_type_pret = tp.id_type_pret
JOIN finance_s4_etablissement e ON tp.id_etablissement = e.id_etablissement
WHERE p.id_pret = :id_pret AND tp.id_etablissement = :id_etablissement";

        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_pret' => $id_pret, ':id_etablissement' => $id_etablissement]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pret) {
            return null;
        }

        // Fetch associated remises
        $query = "SELECT id_remise, montant, date_remise 
                  FROM finance_s4_remise 
                  WHERE id_pret = :id_pret 
                  ORDER BY date_remise DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id_pret' => $id_pret]);
        $remises = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add remises to the result
        $pret['remises'] = $remises;

        return $pret;
    }
}
