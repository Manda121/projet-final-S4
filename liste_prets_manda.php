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
    <title>Liste des Prêts</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        input,
        select,
        button {
            margin: 5px;
            padding: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .filter-form {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="filter-form">
        <h2>Filtrer les Prêts</h2>
        <form id="filter-prets-form">
            <label for="client">Client:</label>
            <input type="text" id="client" name="client" placeholder="Nom ou prénom du client">

            <label for="date_debut">Date de début:</label>
            <input type="date" id="date_debut" name="date_debut">

            <label for="date_fin">Date de fin:</label>
            <input type="date" id="date_fin" name="date_fin">

            <label for="montant_min">Montant minimum:</label>
            <input type="number" id="montant_min" name="montant_min" step="0.01">

            <label for="montant_max">Montant maximum:</label>
            <input type="number" id="montant_max" name="montant_max" step="0.01">

            <label for="type_pret">Type de prêt:</label>
            <select id="type_pret" name="type_pret">
                <option value="">Tous</option>
                <!-- Options will be populated dynamically -->
            </select>

            <label for="etat">État:</label>
            <select id="etat" name="etat">
                <option value="">Tous</option>
                <option value="en attente">En attente</option>
                <option value="validee">Validée</option>
                <option value="refusee">Refusée</option>
            </select>

            <button type="button" onclick="filtrerPrets()">Filtrer</button>
            <button type="button" onclick="resetFilters()">Réinitialiser</button>
        </form>
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
                <th>État</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        const apiBase = "http://localhost/projet-final-S4/ws";

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
            <td>${pret.etat}</td>
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