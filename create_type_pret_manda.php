<?php
session_start();
$_SESSION["id_user"] = 1;
$_SESSION["id_etablissement"] = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creation de type de pret</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        input,
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

        .editable input {
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <?php include 'form_create_type_manda.html'; ?>

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

        function chargerTypePret() {
            ajax("GET", "/types_prets", null, (data) => {
                const tbody = document.querySelector("#table-types-prets tbody");
                tbody.innerHTML = "";
                data.forEach(e => {
                    const tr = document.createElement("tr");
                    tr.setAttribute("data-id", e.id_type_pret);
                    tr.innerHTML = `
                        <td>${e.id_type_pret}</td>
                        <td class="libelleu">${e.libelle}</td>
                        <td class="tauxu">${e.taux}</td>
                        <td class="montant_minu">${e.montant_min}</td>
                        <td class="montant_maxu">${e.montant_max}</td>
                        <td class="delai_mois_maxu">${e.delai_mois_max}</td>
                        <td>
                            <button onclick='UpdateTypePret(this, ${JSON.stringify(e)})'>✏️</button>
                            <button onclick='supprimerTypePret(${e.id_type_pret})'>🗑️</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            });
        }

        function ajouterTypePret() {
            const libelle = document.getElementById("libelle").value;
            const taux = document.getElementById("taux").value;
            const montantMinimum = document.getElementById("montantMinimum").value;
            const montantMaximum = document.getElementById("montantMaximum").value;
            const delaiMax = document.getElementById("delaiMax").value;

            const data = `libelle=${encodeURIComponent(libelle)}&taux=${taux}&montantMinimum=${montantMinimum}&montantMaximum=${montantMaximum}&delaiMax=${delaiMax}`;
            ajax("POST", "/types_prets", data, () => {
                resetForm();
                chargerTypePret();
            });
        }

        function UpdateTypePret(button, pret) {
            const row = button.closest("tr");
            if (row.classList.contains("editable")) {
                // Save changes
                const libelle = row.querySelector(".libelleu input").value;
                const taux = row.querySelector(".tauxu input").value;
                const montant_min = row.querySelector(".montant_minu input").value;
                const montant_max = row.querySelector(".montant_maxu input").value;
                const delai_mois_max = row.querySelector(".delai_mois_maxu input").value;

                const data = `libelle=${encodeURIComponent(libelle)}&taux=${taux}&montantMinimum=${montant_min}&montantMaximum=${montant_max}&delaiMax=${delai_mois_max}`;
                console.log(data);
                ajax("PUT", `/types_prets/${pret.id_type_pret}`, data, () => {
                    row.classList.remove("editable");
                    chargerTypePret();
                });
            } else {
                // Enter edit mode
                row.classList.add("editable");
                row.querySelector(".libelleu").innerHTML = `<input type="text" value="${pret.libelle}">`;
                row.querySelector(".tauxu").innerHTML = `<input type="number" value="${pret.taux}">`;
                row.querySelector(".montant_minu").innerHTML = `<input type="number" value="${pret.montant_min}">`;
                row.querySelector(".montant_maxu").innerHTML = `<input type="number" value="${pret.montant_max}">`;
                row.querySelector(".delai_mois_maxu").innerHTML = `<input type="number" value="${pret.delai_mois_max}">`;
                button.textContent = "💾";
            }
        }

        // function supprimerTypePret(id) {
        //     if (confirm("Supprimer ce type de prêt ?")) {
        //         ajax("DELETE", `/types_prets/${id}`, null, () => {
        //             chargerTypePret();
        //         });
        //     }
        // }

        function resetForm() {
            document.getElementById("libelle").value = "";
            document.getElementById("taux").value = "";
            document.getElementById("montantMinimum").value = "";
            document.getElementById("montantMaximum").value = "";
            document.getElementById("delaiMax").value = "";
        }

        chargerTypePret();
    </script>
</body>

</html>