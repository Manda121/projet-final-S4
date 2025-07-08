```php
<?php
session_start();
$pageTitle = 'Création Type de Prêt';
$activeMenu = 'create_type_pret_manda';
ob_start();
?>

<style>
    /* Styles for Création Type de Prêt page, matching style.css theme */
.content-header {
    margin-bottom: 30px;
}

.content-header h1 {
    font-size: 2em;
    color: #2c3e50;
    margin-bottom: 10px;
}

.content-header p {
    color: #7f8c8d;
    font-size: 1em;
}

.pret-form-wrapper {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.pret-form-wrapper h2 {
    font-size: 1.5em;
    color: #2c3e50;
    margin-bottom: 20px;
}

.pret-form-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.pret-form-field {
    display: flex;
    flex-direction: column;
}

.pret-form-field label {
    font-size: 0.95em;
    color: #34495e;
    margin-bottom: 8px;
    font-weight: 500;
}

.pret-form-field input {
    padding: 10px;
    border: 1px solid #dcdcdc;
    border-radius: 5px;
    font-size: 1em;
    transition: border-color 0.3s ease;
}

.pret-form-field input:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 5px rgba(255,215,0,0.3);
}

.pret-taux-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.pret-taux-row input {
    flex: 1;
}

.pret-form-buttons {
    margin-top: 20px;
    text-align: right;
}

.pret-table-wrapper {
    background: #f9f9f9;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.pret-table-wrapper h2 {
    font-size: 1.5em;
    color: #2c3e50;
    margin-bottom: 20px;
}

#table-types-prets {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.95em;
}

#table-types-prets th,
#table-types-prets td {
    border: 1px solid #dcdcdc;
    padding: 12px;
    text-align: left;
}

#table-types-prets th {
    background: #2c3e50;
    color: #ecf0f1;
    font-weight: 500;
}

#table-types-prets tbody tr:nth-child(even) {
    background: #f4f4f4;
}

#table-types-prets tbody tr:hover {
    background: rgba(255,215,0,0.1);
}

.pret-editable input {
    width: 100%;
    padding: 8px;
    border: 1px solid #dcdcdc;
    border-radius: 5px;
    font-size: 0.95em;
}

.pret-editable .pret-tauxu div {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}

.pret-editable .pret-tauxu input {
    flex: 1;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .pret-form-layout {
        grid-template-columns: 1fr;
    }

    .pret-table-wrapper {
        overflow-x: auto;
    }

    #table-types-prets {
        min-width: 600px;
    }
}

#ajout {
    background-color: red ;
    color: white;
}
</style>

<link rel="stylesheet" href="style.css">

<div class="container">
    <header class="content-header">
        <h1>Création de Type de Prêt</h1>
        <p>Ajoutez un nouveau type de prêt avec ses paramètres</p>
    </header>

    <div class="pret-form-wrapper">
        <h2>Nouveau Type de Prêt</h2>
        <form id="type-pret-form">
            <div class="pret-form-layout">
                <div class="pret-form-field">
                    <label for="libelle">Libellé</label>
                    <input type="text" id="libelle" required/>
                </div>
                <div class="pret-form-field">
                    <label for="montantMinimum">Mandat Minimum</label>
                    <input type="number" id="montantMinimum" required/>
                </div>
                <div class="pret-form-field">
                    <label for="montantMaximum">Mandat Maximum</label>
                    <input type="number" id="montantMaximum" required/>
                </div>
                <div class="pret-form-field">
                    <label for="delaiMax">Délai Maximum (mois)</label>
                    <input type="number" id="delaiMax" required/>
                </div>
                <div class="pret-form-field">
                    <label>Taux (%)</label>
                    <div id="taux-container">
                        <div class="pret-taux-row">
                            <input type="number" name="taux[]" step="0.01" required/>
                            <button type="button" class="menu-" onclick="removeTaux(this)">🗑️</button>
                        </div>
                    </div>
                    <button type="button" class="btn" id="ajout" onclick="addTaux()">Ajouter Taux</button>
                </div>
            </div>
            <div class="pret-form-buttons">
                <button type="button" class="btn" onclick="ajouterTypePret()">Créer Type de Prêt</button>
            </div>
        </form>
    </div>

    <div class="pret-table-wrapper">
        <h2>Liste des Types de Prêts</h2>
        <table id="table-types-prets">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Libellé</th>
                    <th>Taux</th>
                    <th>Montant Min</th>
                    <th>Montant Max</th>
                    <th>Délai Max (mois)</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<script>
const apiBase = "/projet-final-S4/ws";

function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                callback(JSON.parse(xhr.responseText));
            } else {
                alert("Erreur: " + xhr.statusText);
            }
        }
    };
    xhr.send(data);
}

function addTaux() {
    const container = document.getElementById("taux-container");
    const newRow = document.createElement("div");
    newRow.className = "pret-taux-row";
    newRow.innerHTML = `
        <input type="number" name="taux[]" step="0.01" required>
        <button type="button" class="menu-item" onclick="removeTaux(this)">🗑️</button>
    `;
    container.appendChild(newRow);
}

function removeTaux(button) {
    const row = button.closest(".pret-taux-row");
    const tauxCell = button.closest(".pret-tauxu");
    const input = row ? row.querySelector("input") : button.previousElementSibling;

    if (tauxCell) {
        if (tauxCell.querySelectorAll("input").length > 1) {
            const tauxValue = input.value;
            const tr = button.closest("tr");
            let deletedDATA = tr.dataset.deletedDATA ? JSON.parse(tr.dataset.deletedDATA) : [];
            deletedDATA.push(tauxValue);
            tr.dataset.deletedDATA = JSON.stringify(deletedDATA);
            button.parentElement.remove();
        } else {
            alert("Vous devez conserver au moins un taux.");
        }
    } else if (document.querySelectorAll("#taux-container .pret-taux-row").length > 1) {
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
    if (row.classList.contains("pret-editable")) {
        const libelle = row.querySelector(".pret-libelleu input").value;
        const taux = Array.from(row.querySelectorAll(".pret-tauxu input")).map(input => input.value);
        const montant_min = row.querySelector(".pret-montant-minu input").value;
        const montant_max = row.querySelector(".pret-montant-maxu input").value;
        const delai_mois_max = row.querySelector(".pret-delai-mois-maxu input").value;
        const deletedDATA = row.dataset.deletedDATA ? JSON.parse(row.dataset.deletedDATA) : [];

        const data = `libelle=${encodeURIComponent(libelle)}&taux=${encodeURIComponent(JSON.stringify(taux))}&montantMinimum=${montant_min}&montantMaximum=${montant_max}&delaiMax=${delai_mois_max}&deletedDATA=${encodeURIComponent(JSON.stringify(deletedDATA))}`;
        ajax("PUT", `/types_prets/${pret.id_type_pret}`, data, () => {
            row.classList.remove("pret-editable");
            delete row.dataset.deletedDATA;
            chargerTypePret();
        });
    } else {
        row.classList.add("pret-editable");
        row.querySelector(".pret-libelleu").innerHTML = `<input type="text" value="${pret.libelle}">`;
        row.querySelector(".pret-tauxu").innerHTML = pret.taux.split(", ").map(t => `<div><input type="number" step="0.01" value="${t}"><button type="button" class="menu-item" onclick="removeTaux(this)">🗑️</button></div>`).join("") + `<button type="button" class="menu-item" onclick="addTauxInRow(this)">Ajouter taux</button>`;
        row.querySelector(".pret-montant-minu").innerHTML = `<input type="number" value="${pret.montant_min}">`;
        row.querySelector(".pret-montant-maxu").innerHTML = `<input type="number" value="${pret.montant_max}">`;
        row.querySelector(".pret-delai-mois-maxu").innerHTML = `<input type="number" value="${pret.delai_mois_max}">`;
        button.textContent = "💾";
    }
}

function addTauxInRow(button) {
    const tauxCell = button.closest(".pret-tauxu");
    const newInput = document.createElement("div");
    newInput.innerHTML = `<input type="number" step="0.01" required><button type="button" class="menu-item" onclick="removeTaux(this)">🗑️</button>`;
    tauxCell.insertBefore(newInput, button);
}

function resetForm() {
    document.getElementById("libelle").value = "";
    document.getElementById("taux-container").innerHTML = `
        <div class="pret-taux-row">
            <input type="number" name="taux[]" step="0.01" required>
            <button type="button" class="menu-item" onclick="removeTaux(this)">🗑️</button>
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
                <td class="pret-libelleu">${e.libelle}</td>
                <td class="pret-tauxu">${e.taux}</td>
                <td class="pret-montant-minu">${e.montant_min}</td>
                <td class="pret-montant-maxu">${e.montant_max}</td>
                <td class="pret-delai-mois-maxu">${e.delai_mois_max}</td>
            `;
            tbody.appendChild(tr);
        });
    });
}

window.onload = () => {
    chargerTypePret();
};
</script>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>
```