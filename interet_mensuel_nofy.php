<?php
session_start();
$pageTitle = 'Interet mensuel';
$activeMenu = 'interet_mensuel';
ob_start();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestion des prêts</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <style>
    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 20px;
      color: #333;
    }
    h1 {
      text-align: center;
      color: #1a3c34;
      margin-bottom: 30px;
      font-size: 2rem;
    }
    .filter-container {
      display: flex;
      gap: 10px;
      justify-content: center;
      align-items: center;
      margin-bottom: 30px;
      flex-wrap: wrap;
    }
    .filter-container input[type="date"], .filter-container input[type="number"] {
      padding: 10px;
      border: 1px solid #d1d5db;
      border-radius: 6px;
      font-size: 14px;
      background: white;
      transition: border-color 0.3s;
    }
    .filter-container input:focus {
      outline: none;
      border-color: #4facfe;
      box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
    }
    .filter-container button {
      padding: 10px 20px;
      background: linear-gradient(90deg, #4facfe, #00f2fe);
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      font-weight: 500;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .filter-container button:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .table-container {
      max-width: 900px;
      margin: 20px auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      display: none;
    }
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }
    th {
      background: #f8fafc;
      font-weight: 600;
      color: #1a3c34;
    }
    td {
      color: #4b5563;
    }
    .chart-container {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      height: 300px;
    }
    canvas {
      max-width: 100%;
      max-height: 250px;
    }
  </style>
</head>
<body>
  <h1>Intérêt, assurance et paiement mensuel</h1>

  <div class="filter-container">
    <input type="number" id="clientId" placeholder="ID Client (facultatif)">
    <input type="date" id="debut" placeholder="Début">
    <span>à</span>
    <input type="date" id="fin" placeholder="Fin">
    <button onclick="chargerInteret()">Afficher</button>
  </div>

  <div class="table-container">
    <table id="table-interet">
      <thead>
        <tr>
          <th>Date et année</th>
          <th>Intérêt mensuel</th>
          <th>Assurance mensuelle</th>
          <th>Paiement mensuel total</th>
          <th>Nombre de prêts</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="chart-container">
    <h2>Diagramme</h2>
    <canvas id="line-chart"></canvas>
  </div>

  <script>
    const apiBase = "/projet-final-S4/ws";
    let chartInstance = null;

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

    function chargerInteret() {
      const clientId = document.getElementById('clientId').value || 0;
      const debut = document.getElementById('debut').value;
      const fin = document.getElementById('fin').value;
      const id = 1; // id_etablissement
      const data = `debut=${debut}&fin=${fin}&clientId=${clientId}`;
      ajax("POST", `/interet/${id}`, data, (response) => {
        const tbody = document.querySelector("#table-interet tbody");
        tbody.innerHTML = "";
        const table = document.getElementById("table-interet");
        table.style.display = "table";

        const labels = response.datas.map(e => e.annee_mois);
        const interets = response.datas.map(e => e.interet_mensuel);
        const assurances = response.datas.map(e => e.assurance_mensuelle);
        const paiements = response.datas.map(e => e.paiement_mensuel_total);

        drawLineChart(labels, interets, assurances, paiements);

        response.datas.forEach(e => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${e.annee_mois}</td>
            <td>${e.interet_mensuel.toLocaleString('fr-FR', { minimumFractionDigits: 2 })} Ar</td>
            <td>${e.assurance_mensuelle.toLocaleString('fr-FR', { minimumFractionDigits: 2 })} Ar</td>
            <td>${e.paiement_mensuel_total.toLocaleString('fr-FR', { minimumFractionDigits: 2 })} Ar</td>
            <td>${e.nombre_pret}</td>
          `;
          tbody.appendChild(tr);
        });
      });
    }

    function drawLineChart(labels, interets, assurances, paiements) {
      const ctx = document.getElementById('line-chart').getContext('2d');

      if (chartInstance) {
        chartInstance.destroy();
      }

      chartInstance = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [
            {
              label: 'Intérêt mensuel (Ar)',
              data: interets,
              borderColor: '#4facfe',
              backgroundColor: 'rgba(79, 172, 254, 0.1)',
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#00f2fe',
              pointBorderColor: '#4facfe',
              pointHoverBackgroundColor: '#fff',
              pointHoverBorderColor: '#4facfe',
              pointRadius: 4,
              pointHoverRadius: 6
            },
            {
              label: 'Assurance mensuelle (Ar)',
              data: assurances,
              borderColor: '#ff6b6b',
              backgroundColor: 'rgba(255, 107, 107, 0.1)',
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#ff8787',
              pointBorderColor: '#ff6b6b',
              pointHoverBackgroundColor: '#fff',
              pointHoverBorderColor: '#ff6b6b',
              pointRadius: 4,
              pointHoverRadius: 6
            },
            {
              label: 'Paiement mensuel total (Ar)',
              data: paiements,
              borderColor: '#34c759',
              backgroundColor: 'rgba(52, 199, 89, 0.1)',
              fill: true,
              tension: 0.4,
              pointBackgroundColor: '#50d970',
              pointBorderColor: '#34c759',
              pointHoverBackgroundColor: '#fff',
              pointHoverBorderColor: '#34c759',
              pointRadius: 4,
              pointHoverRadius: 6
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              grid: { display: false },
              title: { display: true, text: 'Mois et Année', color: '#1a3c34', font: { size: 12 } },
              ticks: { maxRotation: 45, minRotation: 45, font: { size: 10 } }
            },
            y: {
              beginAtZero: true,
              grid: { color: '#e5e7eb' },
              title: { display: true, text: 'Montant (Ar)', color: '#1a3c34', font: { size: 12 } },
              ticks: {
                callback: function(value) {
                  return value.toLocaleString('fr-FR') + ' Ar';
                },
                font: { size: 10 }
              }
            }
          },
          plugins: {
            legend: { display: true, position: 'top' },
            tooltip: {
              backgroundColor: '#1a3c34',
              titleFont: { size: 10 },
              bodyFont: { size: 10 },
              callbacks: {
                label: function(context) {
                  return `${context.dataset.label}: ${context.parsed.y.toLocaleString('fr-FR', { minimumFractionDigits: 2 })} Ar`;
                }
              }
            }
          },
          animation: {
            duration: 800,
            easing: 'easeOutQuart'
          }
        }
      });
    }
  </script>
</body>
</html>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>