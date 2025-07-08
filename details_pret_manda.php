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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
            color: #2a2a2a;
            line-height: 1.5;
            padding: 20px;
        }

        .loan-details {
            max-width: 1200px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border: 1px solid #d1d5db;
        }

        h2 {
            color: #00205b;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            border-bottom: 2px solid #00205b;
            padding-bottom: 8px;
        }

        h3 {
            color: #00205b;
            font-size: 22px;
            font-weight: 600;
            margin: 25px 0 15px;
        }

        dl#loan-info {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 8px;
            margin-bottom: 20px;
        }

        dt {
            font-weight: 500;
            color: #00205b;
            padding: 6px 0;
            border-right: 1px solid #d1d5db;
            font-size: 12px;
        }

        dd {
            padding: 6px 0;
            font-size: 12px;
            color: #2a2a2a;
        }

        #table-remises {
            width: 100%;
            border-collapse: collapse;
            background: #ffffff;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
        }

        #table-remises thead {
            background: #00205b;
            color: #ffffff;
        }

        #table-remises th {
            padding: 16px;
            text-align: left;
            font-weight: 700;
            font-size: 17px;
        }

        #table-remises tbody tr {
            border-bottom: 1px solid #d1d5db;
            transition: background 0.2s ease;
        }

        #table-remises tbody tr:hover {
            background: #f1f5f9;
        }

        #table-remises td {
            padding: 14px 16px;
            font-size: 15px;
            color: #2a2a2a;
        }

        .error {
            color: #b91c1c;
            background: #fef2f2;
            padding: 10px;
            margin: 15px 0;
            text-align: center;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .loan-details {
                padding: 15px;
            }

            dl#loan-info {
                grid-template-columns: 1fr;
                gap: 6px;
            }

            dt {
                border-right: none;
                border-bottom: 1px solid #d1d5db;
                font-size: 11px;
            }

            dd {
                font-size: 11px;
            }

            #table-remises th {
                padding: 12px;
                font-size: 15px;
            }

            #table-remises td {
                padding: 10px 12px;
                font-size: 13px;
            }
        }

        :focus {
            outline: 2px solid #00205b;
            outline-offset: 2px;
        }
    </style>
</head>

<body>
    <div class="loan-details">
        <h2>Détails du Prêt #<span id="id_pret"></span></h2>
        <dl id="loan-info">
            <dt>Client:</dt>
            <dd id="client"></dd>
            <dt>Type de Prêt:</dt>
            <dd id="type_pret"></dd>
            <dt>Montant:</dt>
            <dd id="montant"></dd>
            <dt>Taux d'intérêt:</dt>
            <dd id="taux"></dd>
            <dt>Taux d'assurance:</dt>
            <dd id="taux_assurance"></dd>
            <dt>Date du prêt:</dt>
            <dd id="date_pret"></dd>
            <dt>Date limite:</dt>
            <dd id="date_limite"></dd>
            <dt>État:</dt>
            <dd id="etat"></dd>
            <dt>Description:</dt>
            <dd id="description"></dd>
        </dl>
        <button onclick="exporterPDF()">Exporter en PDF</button>
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

        function exporterPDF() {
            ajax("GET", `/prets/${idPret}`, null, (data) => {
                const formData = new FormData();
                formData.append("pret", JSON.stringify(data));

                fetch("generer_pret_pdf.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(resp => resp.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement("a");
                        a.href = url;
                        a.download = `pret_${data.id_pret}.pdf`;
                        a.click();
                    })
                    .catch(err => {
                        alert("Erreur lors de l’exportation PDF.");
                        console.error(err);
                    });
            });
        }

        function chargerDetailsPret() {
            ajax("GET", `/prets/${idPret}`, null, (data) => {
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

        chargerDetailsPret();
    </script>
</body>

</html>