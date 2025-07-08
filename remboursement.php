<?php
$pageTitle = 'Gestion des Remboursements';
$activeMenu = 'remboursement';
ob_start();
?>

<header class="content-header">
  <h1>Gestion des Remboursements</h1>
  <p>Suivez et enregistrez les remboursements pour les prêts validés.</p>
  <link rel="stylesheet" href="remboursement.css">
</header>

<div class="form-container">
  <h2>Sélectionner un Prêt</h2>
  <form id="remboursement-form">
    <div class="form-group">
      <label for="pret_select">Prêt Validé :</label>
      <select id="pret_select" onchange="chargerSimulation()">
        <option value="">Sélectionner un prêt</option>
      </select>
    </div>
  </form>
</div>

<div id="simulation-result">
  <!-- Layout principal: Détails à gauche, Paiement à droite -->
  <div class="details-payment-container">
    <!-- Fiche détails du prêt -->
    <div class="loan-details-card">
      <h3>📋 Détails du Prêt</h3>
      <div class="detail-item">
        <span class="detail-label">ID Prêt :</span>
        <span class="detail-value" id="id_pret">-</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Montant Initial :</span>
        <span class="detail-value amount" id="montant_initial">-</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Taux Annuel :</span>
        <span class="detail-value" id="taux_annuel">-</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Date de Début :</span>
        <span class="detail-value" id="date_pret">-</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Date Limite :</span>
        <span class="detail-value" id="date_limite">-</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Total Remboursé :</span>
        <span class="detail-value amount" id="total_remis">-</span>
      </div>
      <div class="detail-item">
        <span class="detail-label">Reste à Payer :</span>
        <span class="detail-value remaining" id="remaining">-</span>
      </div>
    </div>

    <!-- Formulaire de paiement -->
    <div class="payment-form-card">
      <h3>💰 Nouveau Paiement</h3>
      <div class="form-group">
        <label for="payment_amount">Montant du Paiement :</label>
        <input type="number" id="payment_amount" placeholder="0.00 Ar" step="0.01" required>
      </div>
      <div class="form-group">
        <label for="payment_date">Date du Paiement :</label>
        <input type="date" id="payment_date" required>
      </div>
      <button class="btn" onclick="recordPayment()">Enregistrer Paiement</button>
      <div id="error-message" class="error-message"></div>
    </div>
  </div>

  <!-- Tableaux en bas -->
  <div class="tables-container">
    <div class="table-container">
      <h3>📊 Échéancier de Remboursement</h3>
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

    <div class="table-container">
      <h3>💳 Historique des Paiements</h3>
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
  </div>
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
          // Remplir les détails du prêt
          document.getElementById("id_pret").textContent = data.pret.id_pret;
          document.getElementById("montant_initial").textContent = parseFloat(data.pret.montant).toFixed(2) + " Ar";
          document.getElementById("taux_annuel").textContent = parseFloat(data.pret.taux_annuel).toFixed(2) + "%";
          document.getElementById("date_pret").textContent = data.pret.date_pret;
          document.getElementById("date_limite").textContent = data.pret.date_limite;
          document.getElementById("total_remis").textContent = parseFloat(data.total_remis).toFixed(2) + " Ar";
          
          const remaining = parseFloat(data.pret.montant) - parseFloat(data.total_remis);
          const remainingElement = document.getElementById("remaining");
          remainingElement.textContent = remaining.toFixed(2) + " Ar";
          remainingElement.className = remaining <= 0 ? "detail-value remaining paid" : "detail-value remaining";

          // Désactiver le formulaire si le prêt est remboursé
          const paymentForm = document.querySelector(".payment-form-card");
          const paymentButton = paymentForm.querySelector(".btn");
          const paymentInputs = paymentForm.querySelectorAll("input");
          if (remaining <= 0) {
            paymentButton.disabled = true;
            paymentInputs.forEach(input => input.disabled = true);
            paymentButton.textContent = "Prêt Remboursé";
            paymentButton.style.backgroundColor = "#95a5a6";
            paymentButton.style.cursor = "not-allowed";
          } else {
            paymentButton.disabled = false;
            paymentInputs.forEach(input => input.disabled = false);
            paymentButton.textContent = "Enregistrer Paiement";
            paymentButton.style.backgroundColor = "";
            paymentButton.style.cursor = "";
          }

          // Remplir l'échéancier
          const tbodyEcheancier = document.getElementById("echeancier-table").getElementsByTagName('tbody')[0];
          tbodyEcheancier.innerHTML = '';
          data.echeancier.forEach(row => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${row.mois}</td>
              <td>${parseFloat(row.capital_restant).toFixed(2)} Ar</td>
              <td>${parseFloat(row.interet).toFixed(2)} Ar</td>
              <td>${parseFloat(row.capital_rembourse).toFixed(2)} Ar</td>
              <td>${parseFloat(row.annuite).toFixed(2)} Ar</td>
              <td>${row.date_paiement}</td>
            `;
            tbodyEcheancier.appendChild(tr);
          });

          // Remplir l'historique des paiements
          const tbodyRemises = document.getElementById("remises-table").getElementsByTagName('tbody')[0];
          tbodyRemises.innerHTML = '';
          data.remises.forEach(remise => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${remise.id_remise}</td>
              <td>${parseFloat(remise.montant).toFixed(2)} Ar</td>
              <td>${remise.date_remise}</td>
            `;
            tbodyRemises.appendChild(tr);
          });

          simulationResult.classList.add('show');
        } else {
          simulationResult.classList.remove('show');
          alert(data.message || "Erreur lors de la simulation.");
        }
      });
    } else {
      simulationResult.classList.remove('show');
    }
  }

  function recordPayment() {
    const id_pret = document.getElementById("pret_select").value;
    const montant = parseFloat(document.getElementById("payment_amount").value);
    const date_remise = document.getElementById("payment_date").value;
    const errorMessage = document.getElementById("error-message");
    const remainingText = document.getElementById("remaining").textContent;
    const remaining = parseFloat(remainingText.replace(" Ar", ""));

    // Condition d'arrêt : vérifier si le prêt est déjà remboursé
    if (remaining <= 0) {
      errorMessage.classList.add('show');
      errorMessage.textContent = "Ce prêt est déjà entièrement remboursé.";
      return;
    }

    // Vérification des champs
    if (!id_pret || !montant || !date_remise) {
      errorMessage.classList.add('show');
      errorMessage.textContent = "Veuillez remplir tous les champs du paiement.";
      return;
    }

    // Vérifier si le paiement excède le reste à payer
    if (montant > remaining) {
      errorMessage.classList.add('show');
      errorMessage.textContent = `Le montant du paiement (${montant.toFixed(2)} Ar) ne peut pas dépasser le reste à payer (${remaining.toFixed(2)} Ar).`;
      return;
    }

    const data = `id_pret=${encodeURIComponent(id_pret)}&montant=${encodeURIComponent(montant)}&date_remise=${encodeURIComponent(date_remise)}`;
    ajax("POST", "/remboursement-payment", data, (response) => {
      if (response.success) {
        errorMessage.classList.remove('show');
        alert(response.message);
        document.getElementById("payment_amount").value = '';
        document.getElementById("payment_date").value = '';
        chargerSimulation();
      } else {
        errorMessage.classList.add('show');
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