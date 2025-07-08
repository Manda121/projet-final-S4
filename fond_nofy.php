<?php
session_start();
$pageTitle = 'Ajouter un fond';
$activeMenu = 'ajout_fond';
ob_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des fonds</title>  
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="remboursement.css">
</head>
<body>

  <h1>Gestion des fonds</h1>

  <div>
    <input type="hidden" id="id">
    <div class="form-group">
      <input type="number" id="montant" placeholder="montant">
    </div>
    <button onclick="ajouterFond()" class="btn">Ajouter / Modifier</button>
    <br><br>
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
    const apiBase = "/projet-final-S4/ws";

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