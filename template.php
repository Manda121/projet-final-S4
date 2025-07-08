<?php
$pageTitle = isset($pageTitle) ? $pageTitle : 'Gestion Prêts';
$activeMenu = isset($activeMenu) ? $activeMenu : 'accueil';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Reset et base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color:rgb(0, 41, 82);
            line-height: 1.6;
        }

        /* Toggle mobile button */
        .mobile-menu-toggle {
          display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            background: linear-gradient(45deg, #5a67d8, #6b46c1);
            transform: scale(1.05);
        }

        .mobile-menu-toggle:active {
            transform: scale(0.95);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
            background: rgba(0,0,0,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.8em;
            margin-bottom: 5px;
            color: #ffd700;
            font-weight: 600;
        }

        .sidebar-header p {
            font-size: 0.9em;
            color: #bdc3c7;
            opacity: 0.8;
        }

        /* Menu */
        .menu-list {
            padding: 20px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            margin: 2px 0;
        }

        .menu-item:hover {
            background: rgba(52, 73, 94, 0.8);
            border-left-color: #ffd700;
            transform: translateX(5px);
        }

        .menu-item.active {
            background: rgba(255, 215, 0, 0.1);
            border-left-color: #ffd700;
            color: #ffd700;
        }

        .menu-icon {
            font-size: 1.2em;
            margin-right: 15px;
            width: 25px;
            text-align: center;
        }

        .menu-text {
            font-size: 0.95em;
            font-weight: 500;
        }

        /* Main content */
        .main-content {
            margin-left: 280px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Responsive - Mobile */
        @media (max-width: 768px) {
            .mobile-menu-toggle {
                display: block;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 80px 10px 20px 10px;
            }
            
            .container {
                padding: 15px;
                margin: 0;
                border-radius: 0;
            }
        }

        /* Overlay pour mobile */
        @media (max-width: 768px) {
            .sidebar.active::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: -1;
            }
        }

        /* Tablet */
        @media (max-width: 1024px) and (min-width: 769px) {
            .sidebar {
                width: 250px;
            }
            
            .main-content {
                margin-left: 250px;
            }
            
            .menu-item {
                padding: 12px 20px;
            }
            
            .menu-text {
                font-size: 0.9em;
            }
        }

        /* Amélioration du scrollbar pour la sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: #34495e;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #ffd700;
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #ffed4e;
        }

        /* Style pour les états de focus (accessibilité) */
        .mobile-menu-toggle:focus,
        .menu-item:focus {
            outline: 2px solid #ffd700;
            outline-offset: 2px;
        }

        /* Style pour les petits écrans */
        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
            }
            
            .mobile-menu-toggle {
                top: 15px;
                left: 15px;
                padding: 10px 12px;
                font-size: 16px;
            }
            
            .main-content {
                padding: 70px 5px 20px 5px;
            }
            
            .sidebar-header {
                padding: 20px 15px;
            }
            
            .sidebar-header h2 {
                font-size: 1.5em;
            }
            
            .menu-item {
                padding: 12px 20px;
            }
        }
    </style>
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
            <a href="home.php" class="menu-item <?php echo $activeMenu === 'accueil' ? 'active' : ''; ?>">
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
                <span class="menu-text">Créer un type prêt</span>
            </a>
            <a href="liste_prets_manda.php" class="menu-item <?php echo $activeMenu === 'liste_prets_manda' ? 'active' : ''; ?>">
                <span class="menu-icon">📋</span>
                <span class="menu-text">Liste Prêt</span>
            </a>
            <a href="remboursement.php" class="menu-item <?php echo $activeMenu === 'remboursement' ? 'active' : ''; ?>">
                <span class="menu-icon">💸</span>
                <span class="menu-text">Remboursement</span>
            </a>
            <a href="fond_nofy.php" class="menu-item <?php echo $activeMenu === 'ajout_fond' ? 'active' : ''; ?>">
                <span class="menu-icon">💰</span>
                <span class="menu-text">Ajouter un fond</span>
            </a>
            <a href="interet_mensuel_nofy.php" class="menu-item <?php echo $activeMenu === 'interet_mensuel' ? 'active' : ''; ?>">
                <span class="menu-icon">📈</span>
                <span class="menu-text">Intérêt mensuel</span>
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
            console.log("toggleSidebar called");
            console.log("sidebar element:", sidebar);
            console.log("sidebar classes:", sidebar.classList.toString());
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

        // Fermer le menu mobile avec la touche Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && window.innerWidth <= 768) {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.remove('active');
            }
        });

        // Gérer le redimensionnement de la fenêtre
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>