<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Banque Nationale</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            display: flex;
            align-items: center;
        }

        .logo::before {
            content: "🏦";
            margin-right: 0.5rem;
            font-size: 2rem;
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn-secondary {
            background: transparent;
            border: 2px solid white;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: white;
            color: #1e3c72;
        }

        /* Hero Section */
        .hero {
            /* background: linear-gradient(rgba(30, 60, 114, 0.8), rgba(42, 82, 152, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23f0f0f0" width="1200" height="800"/><text x="600" y="400" font-family="Arial" font-size="48" fill="%23999" text-anchor="middle" dominant-baseline="middle">Image de banque moderne</text></svg>'); */
            background: url("image/banque.png");
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-content {
            max-width: 1200px;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            padding: 0 2rem;
        }

        .hero-text {
            background-color: rgba(255, 255, 255, 0.1);
            /* gris clair transparent */
            backdrop-filter: blur(10px);
            /* effet de flou */
            -webkit-backdrop-filter: blur(10px);
            /* support Safari */
            border-radius: 30px;
            padding: 20px;
            color: white;
            /* pour que le texte ressorte bien */
        }


        .hero-text h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .features li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
        }

        .features li::before {
            content: "✓";
            color: #4CAF50;
            font-weight: bold;
            margin-right: 1rem;
        }

        /* Form Container */
        .form-container {
            background: white;
            padding: 2.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: #1e3c72;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2a5298;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.2rem;
            color: #666;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 60, 114, 0.3);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
        }

        .form-footer a {
            color: #2a5298;
            text-decoration: none;
            font-weight: 500;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }

        .success-message {
            color: #27ae60;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: none;
        }

        .hidden {
            display: none;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h1 {
                font-size: 2rem;
            }

            .header-content {
                padding: 0 1rem;
            }

            .form-container {
                padding: 2rem;
                margin: 0 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">Banque Nationale</div>
            <nav class="nav-buttons">
                <a href="#" class="btn-secondary" onclick="showRegisterForm()" id="header-register-btn">S'inscrire</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1 style="color: #1e3c72;">Votre banque de confiance</h1>
                <p>Gérez vos finances en toute sécurité avec nos services bancaires modernes et personnalisés.</p>
                <ul class="features" style="color: yellow;">
                    <li>Sécurité maximale de vos données</li>
                    <li>Services 24h/24 et 7j/7</li>
                    <li>Conseils personnalisés</li>
                    <li>Solutions innovantes</li>
                </ul>
            </div>

            <!-- Login Form -->
            <div class="form-container" id="login-form">
                <div class="form-header">
                    <h2>Connexion</h2>
                    <p>Accédez à votre espace personnel</p>
                </div>

                <div class="error-message" id="login-error"></div>
                <div class="success-message" id="login-success"></div>

                <form>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="jean.dupont@email.com" required>
                    </div>
                    <div class="form-group">
                        <label for="mdp">Mot de passe</label>
                        <div class="password-container">
                            <input type="password" id="mdp" name="mdp" value="password123" required>
                            <button type="button" class="toggle-password" aria-label="Afficher/masquer le mot de passe">👁️</button>
                        </div>
                    </div>
                    <button type="button" class="btn-primary" onclick="login()">Se connecter</button>
                </form>

                <div class="form-footer">
                    <a href="#" onclick="showRegisterForm()">Pas encore de compte ? S'inscrire</a>
                </div>
            </div>

            <!-- Register Form -->
            <div class="form-container hidden" id="register-form">
                <div class="form-header">
                    <h2>Inscription</h2>
                    <p>Créez votre compte bancaire</p>
                </div>

                <div class="error-message" id="register-error"></div>
                <div class="success-message" id="register-success"></div>

                <form>
                    <div class="form-group">
                        <label for="reg_email">Email</label>
                        <input type="email" id="reg_email" name="reg_email" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_nom">Nom</label>
                        <input type="text" id="reg_nom" name="reg_nom" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_prenom">Prénom</label>
                        <input type="text" id="reg_prenom" name="reg_prenom" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_mdp">Mot de passe</label>
                        <div class="password-container">
                            <input type="password" id="reg_mdp" name="reg_mdp" required oninput="validatePassword()">
                            <button type="button" class="toggle-password" aria-label="Afficher/masquer le mot de passe">👁️</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reg_mdp_confirm">Confirmer le mot de passe</label>
                        <div class="password-container">
                            <input type="password" id="reg_mdp_confirm" name="reg_mdp_confirm" required oninput="validatePassword()">
                            <button type="button" class="toggle-password" aria-label="Afficher/masquer le mot de passe">👁️</button>
                        </div>
                        <div class="error-message" id="password-error"></div>
                        <div class="password-requirements">
                            Le mot de passe doit contenir au moins 8 caractères et une majuscule.
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date_naissance">Date de naissance</label>
                        <input type="date" id="date_naissance" name="date_naissance" required>
                    </div>
                    <div class="form-group">
                        <label for="etablissement">etablissement</label>
                        <select id="etablissement" name="etablissement" required>
                            <select>
                    </div>
                    <div class="form-group" style="display: none;">
                        <label for="role">Type de compte</label>
                        <select id="role" name="role" required>
                            <option value="finance">Professionnel financier</option>
                            <option value="client">Client particulier</option>
                        </select>
                    </div>
                    <button type="button" class="btn-primary" onclick="register()">S'inscrire</button>
                </form>

                <div class="form-footer">
                    <a href="#" onclick="showLoginForm()">Déjà un compte ? Se connecter</a>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Fonctions pour basculer entre les formulaires
        function showRegisterForm() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
            document.getElementById('header-register-btn').textContent = 'Se connecter';
            document.getElementById('header-register-btn').onclick = showLoginForm;
            document.title = "Inscription - Banque Nationale";
            clearMessages();
        }

        function showLoginForm() {
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('header-register-btn').textContent = 'S\'inscrire';
            document.getElementById('header-register-btn').onclick = showRegisterForm;
            document.title = "Connexion - Banque Nationale";
            clearMessages();
        }

        function clearMessages() {
            const errorElements = document.querySelectorAll('.error-message');
            const successElements = document.querySelectorAll('.success-message');

            errorElements.forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });

            successElements.forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
        }

        // Fonction de validation du mot de passe
        function validatePassword() {
            const password = document.getElementById('reg_mdp').value;
            const confirmPassword = document.getElementById('reg_mdp_confirm').value;
            const errorElement = document.getElementById('password-error');

            let errorMessage = '';

            // Vérification de la longueur
            if (password.length < 8) {
                errorMessage += 'Le mot de passe doit contenir au moins 8 caractères. ';
            }

            // Vérification de la majuscule
            if (!/[A-Z]/.test(password)) {
                errorMessage += 'Le mot de passe doit contenir au moins une majuscule. ';
            }

            // Vérification de la correspondance
            if (password !== confirmPassword && confirmPassword !== '') {
                errorMessage += 'Les mots de passe ne correspondent pas.';
            }

            if (errorMessage) {
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
            } else {
                errorElement.style.display = 'none';
            }

            return errorMessage === '';
        }

        // Gestion de l'affichage/masquage des mots de passe
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('input');
                if (input.type === 'password') {
                    input.type = 'text';
                    button.textContent = '🙈';
                } else {
                    input.type = 'password';
                    button.textContent = '👁️';
                }
            });
        });

        const apiBase = "/projet-final-S4/ws";

        function ajax(method, url, data, callback, errorCallback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4) {
                    if (xhr.status === 200) {
                        callback(JSON.parse(xhr.responseText));
                    } else {
                        if (errorCallback) {
                            errorCallback(xhr);
                        }
                    }
                }
            };
            xhr.send(data);
        }

        function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('mdp').value;

            if (!email || !password) {
                showLoginError('Veuillez remplir tous les champs');
                return;
            }

            ajax('POST', '/users/login', `email=${encodeURIComponent(email)}&mot_de_passe=${encodeURIComponent(password)}`, (response) => {
                if (response.success) {
                    showLoginSuccess('Connexion réussie ! Redirection en cours...');
                    setTimeout(() => {
                        window.location.href = 'home.php';
                    }, 1500);
                } else {
                    showLoginError(response.message || 'Identifiants incorrects');
                }
            }, (xhr) => {
                showLoginError('Erreur de connexion au serveur');
            });
        }

        fetchEtablissements();
        // document.addEventListener('DOMContentLoaded', () => {
        //     // Fetch establishments and populate dropdown

        //     // Toggle password visibility
        //     document.querySelectorAll('.toggle-password').forEach(button => {
        //         button.addEventListener('click', () => {
        //             const passwordInput = button.previousElementSibling;
        //             passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        //             button.textContent = passwordInput.type === 'password' ? '👁️' : '🙈';
        //         });
        //     });
        // });

        function fetchEtablissements() {
            ajax('GET', '/etablissements', null, (response) => {
                const select = document.getElementById('etablissement');
                response.forEach(etablissement => {
                    const option = document.createElement('option');
                    option.value = etablissement.id_etablissement;
                    option.textContent = etablissement.nom;
                    select.appendChild(option);
                });
            }, (xhr) => {
                showRegisterError('Erreur lors de la récupération des établissements');
            });
        }

        // Fonction de validation du mot de passe
        function validatePassword() {
            const password = document.getElementById('reg_mdp').value;
            const confirmPassword = document.getElementById('reg_mdp_confirm').value;
            const errorElement = document.getElementById('password-error');

            let errorMessage = '';

            // Vérification de la longueur
            if (password.length < 8) {
                errorMessage += 'Le mot de passe doit contenir au moins 8 caractères. ';
            }

            // Vérification de la majuscule
            if (!/[A-Z]/.test(password)) {
                errorMessage += 'Le mot de passe doit contenir au moins une majuscule. ';
            }

            // Vérification de la correspondance
            if (password !== confirmPassword) {
                errorMessage += 'Les mots de passe ne correspondent pas.';
            }

            errorElement.textContent = errorMessage;

            return errorMessage === '';
        }

        function register() {
            if (!validatePassword()) {
                showRegisterError("Veuillez corriger les erreurs de mot de passe avant de soumettre.");
                return;
            }

            const email = document.getElementById('reg_email').value;
            const mot_de_passe = document.getElementById('reg_mdp').value;
            const role_user = document.getElementById('role').value;
            const nom = document.getElementById('reg_nom').value;
            const prenom = document.getElementById('reg_prenom').value;
            const date_de_naissance = document.getElementById('date_naissance').value;
            const etablissement = document.getElementById('etablissement').value;

            if (!email || !mot_de_passe || !role_user || !nom || !prenom || !date_de_naissance || !etablissement) {
                showRegisterError("Veuillez remplir tous les champs obligatoires, y compris l'établissement.");
                return;
            }

            const data =
                `email=${encodeURIComponent(email)}&` +
                `mot_de_passe=${encodeURIComponent(mot_de_passe)}&` +
                `role_user=${encodeURIComponent(role_user)}&` +
                `nom=${encodeURIComponent(nom)}&` +
                `prenom=${encodeURIComponent(prenom)}&` +
                `date_de_naissance=${encodeURIComponent(date_de_naissance)}&` +
                `etablissement=${encodeURIComponent(etablissement)}`;

            ajax('POST', '/users', data, (response) => {
                if (response.success) {
                    showRegisterSuccess("Inscription réussie ! Vous pouvez maintenant vous connecter.");

                    // Attendre 2 secondes avant de recharger
                    setTimeout(() => {
                        location.reload();
                    }, 2000);

                } else {
                    showRegisterError(response.message || "Erreur lors de l'inscription.");
                    showLoginForm();
                }
            }, (xhr) => {
                showLoginForm();
                showLoginSuccess("inscription avec succes");
            });
        }

        function showLoginError(message) {
            const errorElement = document.getElementById('login-error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';

            const successElement = document.getElementById('login-success');
            successElement.style.display = 'none';
        }

        function showLoginSuccess(message) {
            const successElement = document.getElementById('login-success');
            successElement.textContent = message;
            successElement.style.display = 'block';

            const errorElement = document.getElementById('login-error');
            errorElement.style.display = 'none';
        }

        function showRegisterError(message) {
            const errorElement = document.getElementById('register-error');
            errorElement.textContent = message;
            errorElement.style.display = 'block';

            const successElement = document.getElementById('register-success');
            successElement.style.display = 'none';
        }

        function showRegisterSuccess(message) {
            const successElement = document.getElementById('register-success');
            successElement.textContent = message;
            successElement.style.display = 'block';

            const errorElement = document.getElementById('register-error');
            errorElement.style.display = 'none';
        }
    </script>
</body>

</html>