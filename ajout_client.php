<?php
session_start();
$pageTitle = 'Ajouter un Client';
$activeMenu = 'ajout_client';
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="remboursement.css">
    <title>Document</title>
    <style>
        body {
            background-color:whitesmoke ;
        }
    </style>
</head>
<body>
    

<div class="container">
    <header class="content-header">
        <h1>Ajouter un Client</h1>
        <p>Ajoutez un client avec un rôle client par défaut</p>

    </header>

    <div class="form-container">
        <h2>Ajouter un nouveau client</h2>
        <form id="client-form">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" required/>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" required/>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" required/>
                </div>
                <div class="form-group">
                    <label for="date_de_naissance">Date de Naissance</label>
                    <input type="date" id="date_de_naissance" required/>
                </div>
                <div class="form-group">
                    <label for="mot_de_passe">Mot de passe</label>
                    <input type="password" id="mot_de_passe" required/>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" onclick="addClient()" class="btn">Ajouter Client</button>
            </div>
        </form>
        <div id="error-message" class="error-message"></div>
    </div>

    <div class="table-container">
        <h2>Liste des Clients</h2>
        <table id="users-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date de Naissance</th>
                    <th>Rôle</th>
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
                if (xhr.status === 200 || xhr.status === 400) {
                    try {
                        const parsedData = JSON.parse(xhr.responseText);
                        callback(parsedData);
                    } catch (e) {
                        console.error("Erreur de parsing JSON:", e);
                        console.log("Réponse brute:", xhr.responseText);
                        alert("Réponse invalide du serveur. Vérifiez la console pour plus de détails.");
                    }
                } else {
                    console.error("Erreur:", xhr.status, xhr.responseText);
                    alert("Erreur: " + xhr.statusText + " - " + (xhr.responseText || "Aucune réponse"));
                }
            }
        };
        xhr.send(data);
    }

    function addClient() {
        const nom = document.getElementById("nom").value;
        const prenom = document.getElementById("prenom").value;
        const email = document.getElementById("email").value;
        const date_de_naissance = document.getElementById("date_de_naissance").value;
        const mot_de_passe = document.getElementById("mot_de_passe").value;
        const errorMessage = document.getElementById("error-message");

        if (!nom || !prenom || !email || !date_de_naissance || !mot_de_passe) {
            errorMessage.classList.add("show");
            errorMessage.textContent = "Tous les champs sont requis.";
            return;
        }

        const data = `nom=${encodeURIComponent(nom)}&prenom=${encodeURIComponent(prenom)}&email=${encodeURIComponent(email)}&date_de_naissance=${encodeURIComponent(date_de_naissance)}&mot_de_passe=${encodeURIComponent(mot_de_passe)}`;
        ajax("POST", "/users", data, (response) => {
            errorMessage.classList.toggle("show", !response.success);
            errorMessage.textContent = response.message;
            if (response.success) {
                document.getElementById("nom").value = '';
                document.getElementById("prenom").value = '';
                document.getElementById("email").value = '';
                document.getElementById("date_de_naissance").value = '';
                document.getElementById("mot_de_passe").value = '';
                chargerUsers();
            }
        });
    }

    function chargerUsers() {
        ajax("GET", "/users", null, (data) => {
            const tbody = document.getElementById("users-table").getElementsByTagName('tbody')[0];
            tbody.innerHTML = '';
            if (Array.isArray(data)) {
                data.forEach(user => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${user.id_user || ''}</td>
                        <td>${user.nom || ''}</td>
                        <td>${user.prenom || ''}</td>
                        <td>${user.email || ''}</td>
                        <td>${user.date_de_naissance || ''}</td>
                        <td>${user.role_user || ''}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        });
    }

    window.onload = () => {
        chargerUsers();
    };
</script>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>

</body>
</html>