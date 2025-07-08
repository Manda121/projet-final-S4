<?php
session_start();
$pageTitle = 'Ajouter un fond';
$activeMenu = 'home';
ob_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion Prêts</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: rgb(75, 91, 162);
        }

        .header {
            text-align: center;
            margin-bottom: 50px;
            padding: 40px 0;
        }

        .header h1 {
            color: blue;
            font-size: 3em;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            color: rgb(217, 255, 0);
            font-size: 1.2em;
            opacity: 0.9;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ffd700, #ffed4e);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 3em;
            margin-bottom: 20px;
            display: block;
        }

        .card h3 {
            color: #2c3e50;
            font-size: 1.5em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .card p {
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .card-link {
            display: inline-block;
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 215, 0, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .card-link:hover::before {
            left: 100%;
        }

        .card-link:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .stats-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            color: #ffd700;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1em;
            opacity: 0.9;
        }

        .quick-actions {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .quick-actions h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 2em;
            text-align: center;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border: 2px solid #dee2e6;
            border-radius: 15px;
            text-decoration: none;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, #ffd700, #ffed4e);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .action-btn:hover::before {
            opacity: 0.1;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            border-color: #ffd700;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .action-btn span {
            position: relative;
            z-index: 1;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding: 30px;
            color: rgba(255, 255, 255, 0.8);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2em;
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .card {
                padding: 20px;
            }

            .stats-section {
                padding: 20px;
            }

            .quick-actions {
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Gestion Prêts</h1>
            <h1>ETU003280 ETU003299 ETU3313</h1>
            <p>Système de gestion financière moderne et professionnel</p>
        </div>

        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Prêts Actifs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0%</div>
                    <div class="stat-label">Taux de Remboursement</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Clients Actifs</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Fonds Disponibles</div>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="card">
                <span class="card-icon">💰</span>
                <h3>Nouveau Prêt</h3>
                <p>Créer un nouveau prêt pour vos clients avec toutes les informations nécessaires et les conditions personnalisées.</p>
                <a href="ajout_pret.php" class="card-link">Faire un Prêt</a>
            </div>

            <div class="card">
                <span class="card-icon">✅</span>
                <h3>Validation</h3>
                <p>Gérer et valider les demandes de prêts en attente. Approuver ou rejeter les demandes selon les critères établis.</p>
                <a href="gestion_pret.php" class="card-link">Validation de Prêt</a>
            </div>

            <div class="card">
                <span class="card-icon">👤</span>
                <h3>Gestion Clients</h3>
                <p>Ajouter de nouveaux clients à votre système avec toutes leurs informations personnelles et financières.</p>
                <a href="ajout_client.php" class="card-link">Ajouter un Client</a>
            </div>

            <div class="card">
                <span class="card-icon">📋</span>
                <h3>Types de Prêts</h3>
                <p>Configurer et créer différents types de prêts avec des taux d'intérêt et conditions spécifiques.</p>
                <a href="create_type_pret_manda.php" class="card-link">Créer un Type</a>
            </div>

            <div class="card">
                <span class="card-icon">📊</span>
                <h3>Liste des Prêts</h3>
                <p>Consulter et gérer tous les prêts existants avec leurs statuts et informations détaillées.</p>
                <a href="liste_prets_manda.php" class="card-link">Voir la Liste</a>
            </div>

            <div class="card">
                <span class="card-icon">💸</span>
                <h3>Remboursements</h3>
                <p>Gérer les remboursements des clients, suivre les échéances et mettre à jour les statuts de paiement.</p>
                <a href="remboursement.php" class="card-link">Remboursement</a>
            </div>
        </div>

        <div class="quick-actions">
            <h2>Actions Rapides</h2>
            <div class="actions-grid">
                <a href="fond_nofy.php" class="action-btn">
                    <span>💰</span>
                    <span>Ajouter un Fond</span>
                </a>
                <a href="interet_mensuel_nofy.php" class="action-btn">
                    <span>📈</span>
                    <span>Intérêt Mensuel</span>
                </a>
                <a href="gestion_pret.php" class="action-btn">
                    <span>⚡</span>
                    <span>Validation Rapide</span>
                </a>
                <a href="logout.php" class="action-btn">
                    <span>🚪</span>
                    <span>Déconnexion</span>
                </a>
            </div>
        </div>

        <div class="footer">
            <p>&copy; ETU003280 ETU003299 ETU003313</p>
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
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        alert("Erreur: " + xhr.statusText);
                    }
                }
            };
            xhr.send(data);
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchStats();
        });

        function fetchStats() {
            ajax('GET', '/stats', null, (response) => {
                if (response) {
                    // Update Active Loans
                    document.querySelector('.stat-item:nth-child(1) .stat-number').textContent = response.active_loans;

                    // Update Repayment Rate
                    document.querySelector('.stat-item:nth-child(2) .stat-number').textContent = response.repayment_rate.toFixed(2) + '%';

                    // Update Active Clients
                    document.querySelector('.stat-item:nth-child(3) .stat-number').textContent = response.active_clients;

                    // Update Available Funds
                    document.querySelector('.stat-item:nth-child(4) .stat-number').textContent = formatFunds(response.available_funds);
                } else {
                    console.error('Erreur: réponse vide');
                }
            }, (xhr) => {
                console.error('Erreur lors de la récupération des statistiques:', xhr.statusText);
            });
        }

        function formatFunds(amount) {
            if (amount >= 1000000) {
                return (amount / 1000000).toFixed(1) + 'M';
            } else if (amount >= 1000) {
                return (amount / 1000).toFixed(1) + 'K';
            }
            return amount.toFixed(2);
        }
    </script>
</body>

</html>

<?php
$pageContent = ob_get_clean();
require 'template.php';
?>