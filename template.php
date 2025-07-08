<?php
session_start();
$_SESSION['id_user'] = 3; // Valeur de test pour l'utilisateur
$pageTitle = isset($pageTitle) ? $pageTitle : 'Gestion Prêts';
$activeMenu = isset($activeMenu) ? $activeMenu : 'accueil';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Menu mobile toggle -->
  <button class="mobile-menu-toggle" onclick="toggleSidebar()">
    ☰
  </button>

  <!-- Menu latéral -->
  <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <h2>Gestion Prêts</h2>
      <p>Système de gestion</p>
    </div>
    <nav class="menu-list">
      <a href="home.html" class="menu-item <?php echo $activeMenu === 'accueil' ? 'active' : ''; ?>">
        <span class="menu-icon">🏠</span>
        <span class="menu-text">Accueil</span>
      </a>
      <a href="ajout_pret.php" class="menu-item <?php echo $activeMenu === 'ajout_pret' ? 'active' : ''; ?>">
        <span class="menu-icon">💰</span>
        <span class="menu-text">Faire un Prêt</span>
      </a>
      <a href="gestion_pret.php" class="menu-item <?php echo $activeMenu === 'gestion_pret' ? 'active' : ''; ?>">
        <span class="menu-icon">✅</span>
        <span class="menu-text">Validation de Prêt</span>
      </a>
      <a href="ajout_client.php" class="menu-item <?php echo $activeMenu === 'ajout_client' ? 'active' : ''; ?>">
        <span class="menu-icon">👤</span>
        <span class="menu-text">Ajouter un Client</span>
      </a>
      <a href="create_type_pret_manda.php" class="menu-item <?php echo $activeMenu === 'create_type_pret_manda' ? 'active' : ''; ?>">
        <span class="menu-icon">💸</span>
        <span class="menu-text">Creer un type pret</span>
      </a>
      <a href="liste_prets_manda.php" class="menu-item <?php echo $activeMenu === 'liste_prets_manda' ? 'active' : ''; ?>">
        <span class="menu-icon">💸</span>
        <span class="menu-text">liste Pret</span>
      </a>
      <a href="remboursement.php" class="menu-item <?php echo $activeMenu === 'remboursement' ? 'active' : ''; ?>">
        <span class="menu-icon">💸</span>
        <span class="menu-text">Remboursement</span>
      </a>
      <a href="fond_nofy.php" class="menu-item <?php echo $activeMenu === 'ajout_fond' ? 'active' : ''; ?>">
        <span class="menu-icon">💸</span>
        <span class="menu-text">ajouter un fond</span>
      </a>
      <a href="interet_mensuel_nofy.php" class="menu-item <?php echo $activeMenu === 'interet_mensuel' ? 'active' : ''; ?>">
        <span class="menu-icon">💸</span>
        <span class="menu-text">Interet mensuel</span>
      </a>
      <div style="margin-top: 50px; border-top: 1px solid #34495e; padding-top: 20px;">
        <a href="logout.php" class="menu-item">
          <span class="menu-icon">🚪</span>
          <span class="menu-text">Déconnexion</span>
        </a>
      </div>
    </nav>
  </div>

  <!-- Contenu principal -->
  <div class="main-content">
    <div class="container">
      <?php echo $pageContent; ?>
    </div>
  </div>

  <script>
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
  </script>
</body>
</html>