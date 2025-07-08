<?php
$pageTitle = 'Ajouter un fond';
$activeMenu = 'ajout_fond';
ob_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des fonds</title>
  <style>
    body { font-family: sans-serif; padding: 20px; }
    input, button { margin: 5px; padding: 5px; }
    table { border-collapse: collapse; width: 100%; margin-top: 20px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
  </style>
</head>
<body>

  <h1>Gestion des fonds</h1>

  <div>
    <input type="hidden" id="id">
    <input type="number" id="montant" placeholder="montant">
    <button onclick="ajouterFond()">Ajouter / Modifier</button>
  </div>

  <table id="table-fond">
    <thead>
      <tr>
        <th>ID</th><th>Montant</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

  <script>
    const apiBase = "http://localhost/projet-final-S4/ws";

    function chargerFond() {
    const id=1;
      ajax("GET", `/fondAll/${id}`, null, (data) => {
        const tbody = document.querySelector("#table-fond tbody");
        tbody.innerHTML = "";
        data.forEach(e => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${e.id_fond}</td>
            <td>${e.montant}</td>
          `;
          tbody.appendChild(tr);
        });
      });
    }


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

    
    function ajouterFond() {
      const id = 1;
      const montant = document.getElementById("montant").value;
      const data = `montant=${montant}&id=${id}`;
        ajax("POST", "/fond", data,()=>{});
        chargerFond();
        resetForm();
    }

    function remplirFormulaire(e) {
      document.getElementById("id").value = e.id;
      document.getElementById("montant").value = e.montant;
    }

   

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("montant").value = "";
    }
    chargerFond();
  </script>

</body>
</html>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>