<?php
session_start();
$pageTitle = 'Liste Pret';
$activeMenu = 'liste_prets_manda';
ob_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="remboursement.css">
    <title>Liste des Prêts</title>
    <style>
        .form-inline {
    display: flex;
    flex-wrap: wrap; /* Permet de passer à la ligne si trop long */
    gap: 15px;
    align-items: flex-end;
}

.form-group {
    display: flex;
    flex-direction: column;
    margin-right: 10px;
}

.form-group label {
    margin-bottom: 5px;
    font-weight: bold;
}

.btn {
    margin-top: 22px;
    height: 35px;
}

    </style>
   
</head>

<body>
    <div class="filter-form">
    <div class="form-container">
    <h2>Filtrer les Prêts</h2>
    <form id="client-form" class="form-inline"> <!-- Ajoute form-inline -->
        <div class="form-group">
            <label for="client">Client:</label>
            <input type="text" id="client" name="client" placeholder="Nom ou prénom du client">
        </div>

        <div class="form-group">
            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut">
        </div>

        <div class="form-group">
            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin">
        </div>

        <div class="form-group">    
            <label for="montant_min">Montant minimum:</label>
            <input type="number" id="montant_min" name="montant_min" step="0.01">
        </div>

        <div class="form-group">
            <label for="montant_max">Montant maximum:</label>
            <input type="number" id="montant_max" name="montant_max" step="0.01">
        </div>

        <div class="form-group">
            <label for="type_pret">Type de prêt:</label>
            <select id="type_pret" name="type_pret"></select>
        </div>

        <div class="form-group">
            <label for="etat">État:</label>
            <select id="etat" name="etat">
                <option value="">Tous</option>
                <option value="en attente">En attente</option>
                <option value="validee">Validée</option>
                <option value="refusee">Refusée</option>
            </select>
        </div>

        <div class="form-group">
            <button type="button" class="btn" onclick="filtrerPrets()">Filtrer</button>
        </div>
            <button type="button" class="btn" onclick="resetFilters()">Réinitialiser</button>
    </form>
</div>

    </div>

    <table id="table-prets">
        <thead>
            <tr>
                <th>ID Prêt</th>
                <th>Client</th>
                <th>Type de Prêt</th>
                <th>Montant</th>
                <th>Taux (%)</th>
                <th>Date Prêt</th>
                <th>Date Limite</th>
                <th>action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        const apiBase = "/projet-final-S4/ws";

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(data);
        }

        function chargerTypesPret() {
            ajax("GET", "/types_prets", null, (data) => {
                const select = document.getElementById("type_pret");
                select.innerHTML = '<option value="">Tous</option>';
                data.forEach(type => {
                    const option = document.createElement("option");
                    option.value = type.id_type_pret;
                    option.textContent = type.libelle;
                    select.appendChild(option);
                });
            });
        }

        function filtrerPrets() {
            const client = document.getElementById("client").value;
            const date_debut = document.getElementById("date_debut").value;
            const date_fin = document.getElementById("date_fin").value;
            const montant_min = document.getElementById("montant_min").value;
            const montant_max = document.getElementById("montant_max").value;
            const type_pret = document.getElementById("type_pret").value;
            const etat = document.getElementById("etat").value;

            const params = new URLSearchParams();
            if (client) params.append("client", client);
            if (date_debut) params.append("date_debut", date_debut);
            if (date_fin) params.append("date_fin", date_fin);
            if (montant_min) params.append("montant_min", montant_min);
            if (montant_max) params.append("montant_max", montant_max);
            if (type_pret) params.append("type_pret", type_pret);
            if (etat) params.append("etat", etat);

            ajax("GET", `/prets?${params.toString()}`, null, (data) => {
                const tbody = document.querySelector("#table-prets tbody");
                tbody.innerHTML = "";
                data.forEach(pret => {
                    const tr = document.createElement("tr");

                    // Ajouter un gestionnaire d'événement au clic
                    tr.style.cursor = "pointer";
                    tr.addEventListener("click", () => {
                        window.location.href = `details_pret_manda.php?id_pret=${pret.id_pret}`;
                    });

                    tr.innerHTML = `
            <td>${pret.id_pret}</td>
            <td>${pret.client_nom} ${pret.client_prenom}</td>
            <td>${pret.type_pret_libelle}</td>
            <td>${pret.montant}</td>
            <td>${pret.taux}</td>
            <td>${pret.date_pret}</td>
            <td>${pret.date_limite}</td>
            <td>voir detail</td>
        `;
                    tbody.appendChild(tr);
                });
            });

        }

        function resetFilters() {
            document.getElementById("filter-prets-form").reset();
            filtrerPrets();
        }

        // Initial load
        chargerTypesPret();
        filtrerPrets();
    </script>
</body>

</html>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>