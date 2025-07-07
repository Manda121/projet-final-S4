<?php
session_start();
$_SESSION["id_user"] = 1;
$_SESSION["id_etablissement"] = 1;

// Check if id_pret is provided
if (!isset($_GET['id_pret']) || !is_numeric($_GET['id_pret'])) {
    die("Erreur : ID du prêt non spécifié ou invalide.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Prêt</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }
        .loan-details {
            margin-bottom: 20px;
        }
        .loan-details h2 {
            margin-bottom: 10px;
        }
        .loan-details dl {
            display: grid;
            grid-template-columns: 150px auto;
            gap: 10px;
        }
        .loan-details dt {
            font-weight: bold;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="loan-details">
        <h2>Détails du Prêt #<span id="id_pret"></span></h2>
        <dl id="loan-info">
            <dt>Client:</dt><dd id="client"></dd>
            <dt>Type de Prêt:</dt><dd id="type_pret"></dd>
            <dt>Montant:</dt><dd id="montant"></dd>
            <dt>Taux d'intérêt:</dt><dd id="taux"></dd>
            <dt>Taux d'assurance:</dt><dd id="taux_assurance"></dd>
            <dt>Date du prêt:</dt><dd id="date_pret"></dd>
            <dt>Date limite:</dt><dd id="date_limite"></dd>
            <dt>État:</dt><dd id="etat"></dd>
            <dt>Description:</dt><dd id="description"></dd>
        </dl>
    </div>

    <h3>Remboursements</h3>
    <table id="table-remises">
        <thead>
            <tr>
                <th>ID Remise</th>
                <th>Montant</th>
                <th>Date de Remise</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        const apiBase = "http://localhost/projet-final-S4/ws";
        const idPret = <?php echo json_encode($_GET['id_pret']); ?>;

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        document.getElementById("loan-info").innerHTML = `<p class="error">Erreur : Impossible de charger les détails du prêt.</p>`;
                    }
                }
            };
            xhr.send(data);
        }

        function chargerDetailsPret() {
            ajax("GET", `/prets/${idPret}`, null, (data) => {
                // Populate loan details
                document.getElementById("id_pret").textContent = data.id_pret;
                document.getElementById("client").textContent = `${data.client_nom} ${data.client_prenom}`;
                document.getElementById("type_pret").textContent = data.type_pret_libelle;
                document.getElementById("montant").textContent = data.montant;
                document.getElementById("taux").textContent = data.taux;
                document.getElementById("taux_assurance").textContent = data.taux_assurance;
                document.getElementById("date_pret").textContent = data.date_pret;
                document.getElementById("date_limite").textContent = data.date_limite;
                document.getElementById("etat").textContent = data.etat;
                document.getElementById("description").textContent = data.description || "Aucune description";

                // Populate remises table
                const tbody = document.querySelector("#table-remises tbody");
                tbody.innerHTML = "";
                data.remises.forEach(remise => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${remise.id_remise}</td>
                        <td>${remise.montant}</td>
                        <td>${remise.date_remise}</td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        // Load details on page load
        chargerDetailsPret();
    </script>
</body>
</html>