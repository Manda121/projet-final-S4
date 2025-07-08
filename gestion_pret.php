<?php
session_start();
$pageTitle = 'Gestion des Prêts en Attente';
$activeMenu = 'gestion_pret';
ob_start();
?>

<header class="content-header">
  <h1>Gestion des Prêts en Attente</h1>
  <p>Validez ou refusez les demandes de prêts en attente</p>
  <link rel="stylesheet" href="remboursement.css">
  <style>
    #modif {
      background-color:green ;
    }
    #supp {
      background-color:red ;
    }
  </style>
</header>

<div class="table-container">
  <h2>Liste des Prêts en Attente</h2>
  <div id="error-message" class="error-message"></div>
  <table id="table-prets">
    <thead>
      <tr>
        <th>ID</th>
        <th>ID Utilisateur</th>
        <th>Date Prêt</th>
        <th>Montant</th>
        <th>Taux Assurance</th>
        <th>Date Limite</th>
        <th>État</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
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

  function chargerPretsEnAttente() {
    ajax("GET", "/prets-en-attente", null, (data) => {
      const tbody = document.querySelector("#table-prets tbody");
      tbody.innerHTML = "";
      if (Array.isArray(data)) {
        data.forEach(pret => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${pret.id_pret || ''}</td>
            <td>${pret.id_user || ''}</td>
            <td>${pret.date_pret || ''}</td>
            <td>${pret.montant || ''}</td>
            <td>${pret.taux_assurance || ''}</td>
            <td>${pret.date_limite || ''}</td>
            <td>${pret.etat || 'En attente'}</td>
            <td>
              <button class="btn action-btn" id="modif" onclick="validerPret(${pret.id_pret}, this)">Accepter</button>
              <button class="btn action-btn" id="supp" onclick="refuserPret(${pret.id_pret}, this)">Refuser</button>
            </td>
          `;
          tbody.appendChild(tr);
        });
      }
    });
  }

  function validerPret(id_pret, button) {
    if (id_pret) {
      ajax("PUT", `/prets/${id_pret}/valider`, null, (response) => {
        const errorMessage = document.getElementById("error-message");
        if (response.message) {
          if (response.message.includes("Échec")) {
            errorMessage.classList.add("show");
            errorMessage.textContent = response.message;
          } else {
            errorMessage.classList.remove("show");
            alert(response.message);
            const row = button.closest("tr");
            row.cells[6].textContent = "Accepté";
            row.cells[6].className = "accepted";
            const buttons = row.cells[7].querySelectorAll(".btn");
            buttons.forEach(btn => btn.disabled = true);
          }
        }
      });
    }
  }

  function refuserPret(id_pret, button) {
    if (id_pret) {
      ajax("PUT", `/prets/${id_pret}/refuser`, null, (response) => {
        const errorMessage = document.getElementById("error-message");
        if (response.message) {
          if (response.message.includes("Échec")) {
            errorMessage.classList.add("show");
            errorMessage.textContent = response.message;
          } else {
            errorMessage.classList.remove("show");
            alert(response.message);
            const row = button.closest("tr");
            row.cells[6].textContent = "Refusé";
            row.cells[6].className = "refused";
            const buttons = row.cells[7].querySelectorAll(".btn");
            buttons.forEach(btn => btn.disabled = true);
          }
        }
      });
    }
  }

  window.onload = () => {
    chargerPretsEnAttente();
  };
</script>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>