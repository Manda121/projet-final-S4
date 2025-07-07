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

function addTaux() {
    const container = document.getElementById("taux-container");
    const newRow = document.createElement("div");
    newRow.className = "taux-row";
    newRow.innerHTML = `
        <input type="number" name="taux[]" step="0.01" required>
        <button type="button" onclick="removeTaux(this)">🗑️</button>
    `;
    container.appendChild(newRow);
}

function removeTaux(button) {
    const row = button.closest(".taux-row");
    const tauxCell = button.closest(".tauxu");
    const input = row ? row.querySelector("input") : button.previousElementSibling;

    if (tauxCell) {
        // In edit mode (within table row)
        if (tauxCell.querySelectorAll("input").length > 1) {
            const tauxValue = input.value;
            // Store the rate to be deleted
            const tr = button.closest("tr");
            let deletedTaux = tr.dataset.deletedTaux ? JSON.parse(tr.dataset.deletedTaux) : [];
            deletedTaux.push(tauxValue);
            tr.dataset.deletedTaux = JSON.stringify(deletedTaux);
            button.parentElement.remove();
        } else {
            alert("Vous devez conserver au moins un taux.");
        }
    } else if (document.querySelectorAll("#taux-container .taux-row").length > 1) {
        // In create form
        row.remove();
    } else {
        alert("Vous devez conserver au moins un taux.");
    }
}

function ajouterTypePret() {
    const libelle = document.getElementById("libelle").value;
    const taux = Array.from(document.querySelectorAll('input[name="taux[]"]')).map(input => input.value);
    const montantMinimum = document.getElementById("montantMinimum").value;
    const montantMaximum = document.getElementById("montantMaximum").value;
    const delaiMax = document.getElementById("delaiMax").value;

    const data = `libelle=${encodeURIComponent(libelle)}&taux=${encodeURIComponent(JSON.stringify(taux))}&montantMinimum=${montantMinimum}&montantMaximum=${montantMaximum}&delaiMax=${delaiMax}`;
    ajax("POST", "/types_prets", data, () => {
        resetForm();
        chargerTypePret();
    });
}

function UpdateTypePret(button, pret) {
    const row = button.closest("tr");
    if (row.classList.contains("editable")) {
        const libelle = row.querySelector(".libelleu input").value;
        const taux = Array.from(row.querySelectorAll(".tauxu input")).map(input => input.value);
        const montant_min = row.querySelector(".montant_minu input").value;
        const montant_max = row.querySelector(".montant_maxu input").value;
        const delai_mois_max = row.querySelector(".delai_mois_maxu input").value;
        const deletedTaux = row.dataset.deletedTaux ? JSON.parse(row.dataset.deletedTaux) : [];

        const data = `libelle=${encodeURIComponent(libelle)}&taux=${encodeURIComponent(JSON.stringify(taux))}&montantMinimum=${montant_min}&montantMaximum=${montant_max}&delaiMax=${delai_mois_max}&deletedTaux=${encodeURIComponent(JSON.stringify(deletedTaux))}`;
        ajax("PUT", `/types_prets/${pret.id_type_pret}`, data, () => {
            row.classList.remove("editable");
            delete row.dataset.deletedTaux; // Clear deleted rates
            chargerTypePret();
        });
    } else {
        row.classList.add("editable");
        row.querySelector(".libelleu").innerHTML = `<input type="text" value="${pret.libelle}">`;
        row.querySelector(".tauxu").innerHTML = pret.taux.split(", ").map(t => `<div><input type="number" step="0.01" value="${t}"><button type="button" onclick="removeTaux(this)">🗑️</button></div>`).join("") + `<button type="button" onclick="addTauxInRow(this)">Ajouter taux</button>`;
        row.querySelector(".montant_minu").innerHTML = `<input type="number" value="${pret.montant_min}">`;
        row.querySelector(".montant_maxu").innerHTML = `<input type="number" value="${pret.montant_max}">`;
        row.querySelector(".delai_mois_maxu").innerHTML = `<input type="number" value="${pret.delai_mois_max}">`;
        button.textContent = "💾";
    }
}

function addTauxInRow(button) {
    const tauxCell = button.closest(".tauxu");
    const newInput = document.createElement("div");
    newInput.innerHTML = `<input type="number" step="0.01" required><button type="button" onclick="removeTaux(this)">🗑️</button>`;
    tauxCell.insertBefore(newInput, button);
}

function resetForm() {
    document.getElementById("libelle").value = "";
    document.getElementById("taux-container").innerHTML = `
        <div class="taux-row">
            <input type="number" name="taux[]" step="0.01" required>
            <button type="button" onclick="removeTaux(this)">🗑️</button>
        </div>
    `;
    document.getElementById("montantMinimum").value = "";
    document.getElementById("montantMaximum").value = "";
    document.getElementById("delaiMax").value = "";
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

chargerTypePret();

        chargerTypePret();
    </script>
</body>

</html>