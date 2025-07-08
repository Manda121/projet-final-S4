<?php
$pageTitle = 'Ajouter un Prêt';
$activeMenu = 'ajout_pret';
ob_start();

$_SESSION['id_user'] = 3;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Ajouter un Prêt</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Menu mobile toggle -->
  <!-- <button class="mobile-menu-toggle" onclick="toggleSidebar()">
    ☰
  </button> -->

  <!-- Menu latéral
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h2>Gestion Prêts</h2>
      <p>Système de gestion</p>
    </div>
    <nav class="menu-list">
      <a href="index.html" class="menu-item">
        <span class="menu-icon">🏠</span>
        <span class="menu-text">Accueil</span>
      </a>
      <a href="ajout_pret.php" class="menu-item active">
        <span class="menu-icon">💰</span>
        <span class="menu-text">Faire un Prêt</span>
      </a>
      <a href="gestion_pret.php" class="menu-item">
        <span class="menu-icon">✅</span>
        <span class="menu-text">Validation de Prêt</span>
      </a>
      <a href="ajout_client.php" class="menu-item">
        <span class="menu-icon">👤</span>
        <span class="menu-text">Ajouter un Client</span>
      </a>
      <a href="remboursement.php" class="menu-item">
        <span class="menu-icon">💸</span>
        <span class="menu-text">Simulation </span>
      </a>
      <div style="margin-top: 50px; border-top: 1px solid #34495e; padding-top: 20px;">
        <a href="logout.php" class="menu-item">
          <span class="menu-icon">🚪</span>
          <span class="menu-text">Déconnexion</span>
        </a>
      </div>
    </nav>
  </div> -->

  <!-- Contenu principal -->
  <div class="main-content" style="margin-left: -5%; width: 90%">
    <div class="container">
      <header class="content-header">
        <h1>Gestion des Prêts</h1>
        <p>Ajoutez et gérez facilement les prêts de vos clients</p>
      </header>

      <!-- Formulaire -->
      <div class="form-container">
        <h2>Ajouter un nouveau prêt</h2>
        <form id="pret-form">
          <input type="hidden" id="id">
          
          <div class="form-grid">
            <div class="form-group">
              <label for="id_user">Utilisateur</label>
              <select id="id_user" required></select>
            </div>

            <div class="form-group">
              <label for="id_type_pret">Type de Prêt</label>
              <select id="id_type_pret" onchange="chargerTaux()" required></select>
            </div>

            <div class="form-group">
              <label for="date_pret">Date du Prêt</label>
              <input type="date" id="date_pret" required/>
            </div>

            <div class="form-group">
              <label for="montant">Montant</label>
              <input type="number" id="montant" step="0.01" required/>
            </div>

            <div class="form-group">
              <label for="date_limite">Date Limite</label>
              <input type="date" id="date_limite" required/>
            </div>

            <div class="form-group">
              <label for="taux_assurance">Taux d'Assurance</label>
              <input type="number" id="taux_assurance" step="0.01" required/>
            </div>

            <div class="form-group">
              <label for="id_taux">Taux</label>
              <select id="id_taux" required></select>
            </div>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" rows="3" required></textarea>
          </div>

          <div class="form-actions">
            <button type="button" onclick="ajouterPret()" class="btn">Ajouter le prêt</button>
          </div>
        </form>
        <div id="error-message" class="error-message"></div>
      </div>

      <!-- Liste des prêts -->
      <div class="table-container">
        <h2>Liste des Prêts</h2>
        <table id="table-prets">
          <thead>
            <tr>
              <th>ID</th>
              <th>Utilisateur</th>
              <th>Type Prêt</th>
              <th>Date Prêt</th>
              <th>Description</th>
              <th>Montant</th>
              <th>Taux Assurance</th>
              <th>Date Limite</th>
              <th>État</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    const apiBase = "http://localhost/projet-final-S4/ws";

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('active');
    }

    // Fermer le menu mobile en cliquant en dehors
    document.addEventListener('click', function(event) {
      const sidebar = document.getElementById('sidebar');
      const toggle = document.querySelector('.mobile-menu-toggle');
      
      if (window.innerWidth <= 768 && 
          !sidebar.contains(event.target) && 
          !toggle.contains(event.target)) {
        sidebar.classList.remove('active');
      }
    });

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

    function chargerUtilisateurs() {
      ajax("GET", "/clients", null, (data) => {
        const select = document.getElementById("id_user");
        select.innerHTML = '<option value="">Sélectionner un utilisateur</option>';
        if (Array.isArray(data)) {
          data.forEach(user => {
            const option = document.createElement("option");
            option.value = user.id_user;
            option.textContent = `${user.nom} ${user.prenom} (${user.email})`;
            select.appendChild(option);
          });
        }
      });
    }

    function chargerTypesPret() {
      const id_user_session = <?php echo $_SESSION['id_user']; ?>;
      ajax("GET", `/types-pret-by-user?id_user=${id_user_session}`, null, (data) => {
        const select = document.getElementById("id_type_pret");
        select.innerHTML = '<option value="">Sélectionner un type de prêt</option>';
        if (Array.isArray(data)) {
          data.forEach(type => {
            const option = document.createElement("option");
            option.value = type.id_type_pret;
            option.textContent = type.libelle;
            select.appendChild(option);
          });
        }
      });
    }

    function chargerTaux() {
      const id_type_pret = document.getElementById("id_type_pret").value;
      const tauxSelect = document.getElementById("id_taux");
      tauxSelect.innerHTML = '<option value="">Sélectionner un taux</option>';
      if (id_type_pret) {
        ajax("GET", `/taux-by-type-pret?id_type_pret=${id_type_pret}`, null, (data) => {
          if (Array.isArray(data)) {
            data.forEach(taux => {
              const option = document.createElement("option");
              option.value = taux.id_taux;
              option.textContent = `${taux.taux}%`;
              tauxSelect.appendChild(option);
            });
          }
        });
      }
    }

    function ajouterPret() {
      const id_user = document.getElementById("id_user").value;
      const date_pret = document.getElementById("date_pret").value;
      const description = document.getElementById("description").value;
      const montant = document.getElementById("montant").value;
      const date_limite = document.getElementById("date_limite").value;
      const taux_assurance = document.getElementById("taux_assurance").value;
      const id_taux = document.getElementById("id_taux").value;
      const etat = "en attente";

      if (!id_user || !date_pret || !description || !montant || !date_limite || !taux_assurance || !id_taux) {
        alert("Veuillez remplir tous les champs.");
        return;
      }

      const data = `id_user=${encodeURIComponent(id_user)}&date_pret=${encodeURIComponent(date_pret)}&description=${encodeURIComponent(description)}&montant=${encodeURIComponent(montant)}&date_limite=${encodeURIComponent(date_limite)}&taux_assurance=${encodeURIComponent(taux_assurance)}&id_taux=${encodeURIComponent(id_taux)}&etat=${encodeURIComponent(etat)}`;
      ajax("POST", "/prets", data, (response) => {
        const errorMessage = document.getElementById("error-message");
        if (response.message) {
          if (response.message.includes("Échec")) {
            errorMessage.classList.add("show");
            errorMessage.textContent = response.message;
          } else {
            errorMessage.classList.remove("show");
            alert(response.message);
            resetForm();
            chargerPrets();
          }
        } else {
          console.warn("Réponse inattendue:", response);
          alert("L'opération a réussi, mais la réponse n'est pas conforme.");
        }
      });
    }

    function chargerPrets() {
      ajax("GET", "/prets", null, (data) => {
        const tbody = document.querySelector("#table-prets tbody");
        tbody.innerHTML = "";
        if (Array.isArray(data)) {
          data.forEach(pret => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${pret.id_pret || ''}</td>
              <td>${pret.id_user || ''}</td>
              <td>${pret.id_type_pret || ''}</td>
              <td>${pret.date_pret || ''}</td>
              <td>${pret.description || ''}</td>
              <td>${pret.montant || ''}</td>
              <td>${pret.taux_assurance || ''}</td>
              <td>${pret.date_limite || ''}</td>
              <td>${pret.etat || ''}</td>
            `;
            tbody.appendChild(tr);
          });
        }
      });
    }

    function resetForm() {
      document.getElementById("id").value = "";
      document.getElementById("id_user").value = "";
      document.getElementById("id_type_pret").value = "";
      document.getElementById("date_pret").value = "";
      document.getElementById("description").value = "";
      document.getElementById("montant").value = "";
      document.getElementById("date_limite").value = "";
      document.getElementById("taux_assurance").value = "";
      document.getElementById("id_taux").value = "";
      chargerUtilisateurs();
      chargerTypesPret();
    }

    window.onload = () => {
      chargerUtilisateurs();
      chargerTypesPret();
      chargerPrets();
    };
  </script>
</body>
</html>


<?php
$pageContent = ob_get_clean();
require 'template.php';
?>