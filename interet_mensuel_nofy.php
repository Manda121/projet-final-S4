<?php
$pageTitle = 'Interet mensuel';
$activeMenu = 'interet_mensuel';
ob_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des étudiants</title>
 <link rel="stylesheet" href="style_nofy.css">
</head>
<body>

  <h1>Interet gagnee par mois</h1>

  <div>
    <input type="date" id="debut" placeholder="debut">
    entre
    <input type="date" id="fin" placeholder="fin">
    <button onclick="chargerInteret()">Ajouter / Modifier</button>
  </div>

  <table id="table-interet">
    <thead>
      <tr>5
        <th>date et annee</th><th>Interet mensuel</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
    <div class="chart-container">
        <h1>Graphe: </h1>
  <div style="position: relative; height: 300px;">
  <div class="y-axis" id="y-axis"></div>
  <div id="bar-chart"></div>
</div>
    </div>
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

    function chargerInteret() {
        const debut=document.getElementById('debut').value;
        const fin=document.getElementById('fin').value;
        const id=1;
        const data = `debut=${debut}&fin=${fin}`;
        ajax("POST", `/interet/${id}`,data,(response) => {
        const tbody = document.querySelector("#table-interet tbody");
        tbody.innerHTML = "";
        const labels = response.datas.map(e => e.annee_mois);
        const values = response.datas.map(e => e.interet_mensuel);

        drawBarChart(labels, values);
        response.datas.forEach(e => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${e.annee_mois}</td>
            <td>${e.interet_mensuel}</td>
            
          `;
          tbody.appendChild(tr);
        });
      });
    }
  
 function drawBarChart(labels, values) {
  const container = document.getElementById("bar-chart");
  const yAxis = document.getElementById("y-axis");

  container.innerHTML = "<div class='bar-container'></div>";
  yAxis.innerHTML = "";

  if (!values || values.length === 0) return;

  const maxVal = Math.max(...values);
  const barContainer = container.querySelector(".bar-container");

  // Ajouter les labels 
  const steps = 5;
  for (let i = steps; i >= 0; i--) {
    const val = Math.round((maxVal / steps) * i);
    const yLabel = document.createElement("div");
    yLabel.classList.add("y-axis-label");
    yLabel.innerText = val.toLocaleString('fr-FR') + " Ar";
    yAxis.appendChild(yLabel);
  }

  // Dessiner les barres
  values.forEach((val, i) => {
    const bar = document.createElement("div");
    bar.classList.add("bar");
    const heightPercent = (val / maxVal) * 100;
    bar.style.height = heightPercent + "%";
    bar.title = `${labels[i]}: ${val} Ar`;

    // Valeur X (mois) 
    const label = document.createElement("div");
    label.classList.add("bar-label-x");
    label.textContent = labels[i];
    bar.appendChild(label);

    barContainer.appendChild(bar);
  });
}
  </script>

</body>
</html>


<?php
$pageContent = ob_get_clean();
require 'template.php';
?>