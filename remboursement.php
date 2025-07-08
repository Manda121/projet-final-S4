<?php
$pageTitle = 'Gestion des Remboursements';
$activeMenu = 'remboursement';
ob_start();
?>

<header class="content-header">
  <h1>Gestion des Remboursements</h1>
  <p>Suivez et enregistrez les remboursements pour les prêts validés.</p>
</header>

<div class="form-container">
  <h2>Ajouter ou Suivre un Remboursement</h2>
  <form id="remboursement-form" class="form-grid">
    <div class="form-group">
      <label for="pret_select">Sélectionner un Prêt Validé :</label>
      <select id="pret_select" class="form-group" onchange="chargerSimulation()">
        <option value="">Sélectionner un prêt</option>
      </select>
    </div>
  </form>
  <div id="simulation-result" style="display: none;">
    <h3>Détails du Prêt</h3>
    <p>ID Prêt : <span id="id_pret"></span></p>
    <p>Montant Initial : <span id="montant_initial"></span> Ar</p>
    <p>Taux Annuel : <span id="taux_annuel"></span>%</p>
    <p>Date de Début : <span id="date_pret"></span></p>
    <p>Date Limite : <span id="date_limite"></span></p>
    <p>Total Remboursé : <span id="total_remis"></span> Ar</p>
    <p>Reste à Payer : <span id="remaining"></span> Ar</p>
    <h3>Échéancier</h3>
    <div class="table-container">
      <table id="echeancier-table">
        <thead>
          <tr>
            <th>Mois</th>
            <th>Capital Restant</th>
            <th>Intérêt</th>
            <th>Capital Remboursé</th>
            <th>Annuité</th>
            <th>Date de Paiement</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <h3>Paiements Effectués</h3>
    <div class="table-container">
      <table id="remises-table">
        <thead>
          <tr>
            <th>ID Paiement</th>
            <th>Montant</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
    <div class="form-group" style="margin-top: 20px;">
      <label>Montant du Paiement :</label>
      <input type="number" id="payment_amount" placeholder="Montant" step="0.01" required>
      <label>Date du Paiement :</label>
      <input type="date" id="payment_date" required>
      <button class="btn action-btn" onclick="recordPayment()">Enregistrer Paiement</button>
    </div>
  </div>
  <div id="error-message" class="error-message"></div>
</div>

<script>
  const apiBase = "http://localhost/projet-final-S4/ws";

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

  function chargerPretsValides() {
    ajax("GET", "/prets-valides", null, (data) => {
      const select = document.getElementById("pret_select");
      select.innerHTML = '<option value="">Sélectionner un prêt</option>';
      if (Array.isArray(data)) {
        data.forEach(pret => {
          const option = document.createElement("option");
          option.value = pret.id_pret;
          option.textContent = `Prêt #${pret.id_pret} - ${parseFloat(pret.montant).toFixed(2)} Ar`;
          select.appendChild(option);
        });
      }
    });
  }

  function chargerSimulation() {
    const id_pret = document.getElementById("pret_select").value;
    const simulationResult = document.getElementById("simulation-result");
    if (id_pret) {
      ajax("GET", `/remboursement-simulate/${id_pret}`, null, (data) => {
        if (data.echeancier && data.pret) {
          document.getElementById("id_pret").textContent = data.pret.id_pret;
          document.getElementById("montant_initial").textContent = parseFloat(data.pret.montant).toFixed(2);
          document.getElementById("taux_annuel").textContent = parseFloat(data.pret.taux_annuel).toFixed(2);
          document.getElementById("date_pret").textContent = data.pret.date_pret;
          document.getElementById("date_limite").textContent = data.pret.date_limite;
          document.getElementById("total_remis").textContent = parseFloat(data.total_remis).toFixed(2);
          const remaining = parseFloat(data.pret.montant) - parseFloat(data.total_remis);
          document.getElementById("remaining").textContent = remaining.toFixed(2);
          document.getElementById("remaining").className = remaining <= 0 ? "remaining paid" : "remaining";

          const tbodyEcheancier = document.getElementById("echeancier-table").getElementsByTagName('tbody')[0];
          tbodyEcheancier.innerHTML = '';
          data.echeancier.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${row.mois}</td>
              <td>${parseFloat(row.capital_restant).toFixed(2)}</td>
              <td>${parseFloat(row.interet).toFixed(2)}</td>
              <td>${parseFloat(row.capital_rembourse).toFixed(2)}</td>
              <td>${parseFloat(row.annuite).toFixed(2)}</td>
              <td>${row.date_paiement}</td>
            `;
            tbodyEcheancier.appendChild(tr);
          });

          const tbodyRemises = document.getElementById("remises-table").getElementsByTagName('tbody')[0];
          tbodyRemises.innerHTML = '';
          data.remises.forEach(remise => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${remise.id_remise}</td>
              <td>${parseFloat(remise.montant).toFixed(2)}</td>
              <td>${remise.date_remise}</td>
            `;
            tbodyRemises.appendChild(tr);
          });

          simulationResult.style.display = "block";
        } else {
          simulationResult.style.display = "none";
          alert(data.message || "Erreur lors de la simulation.");
        }
      });
    } else {
      simulationResult.style.display = "none";
    }
  }

  function recordPayment() {
    const id_pret = document.getElementById("pret_select").value;
    const montant = parseFloat(document.getElementById("payment_amount").value);
    const date_remise = document.getElementById("payment_date").value;
    const errorMessage = document.getElementById("error-message");

    if (!id_pret || !montant || !date_remise) {
      errorMessage.style.display = "block";
      errorMessage.textContent = "Veuillez remplir tous les champs du paiement.";
      return;
    }

    const data = `id_pret=${encodeURIComponent(id_pret)}&montant=${encodeURIComponent(montant)}&date_remise=${encodeURIComponent(date_remise)}`;
    ajax("POST", "/remboursement-payment", data, (response) => {
      if (response.success) {
        errorMessage.style.display = "none";
        alert(response.message);
        document.getElementById("payment_amount").value = '';
        document.getElementById("payment_date").value = '';
        chargerSimulation();
      } else {
        errorMessage.style.display = "block";
        errorMessage.textContent = response.message;
      }
    });
  }

  window.onload = () => {
    chargerPretsValides();
  };
</script>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>