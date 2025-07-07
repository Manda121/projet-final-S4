<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<style>
    body {
        font-family: sans-serif;
        padding: 20px;
    }

    input,
    button {
        margin: 5px;
        padding: 5px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
    }

    th,
    td {
        border: 1px solid #ccc;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .editable input {
        width: 100%;
        box-sizing: border-box;
    }

    /* Style pour le conteneur du champ mot de passe + bouton */
    .password-container {
        display: inline-flex;
        align-items: center;
    }

    .password-container input {
        margin-right: 0;
    }

    .toggle-password {
        background: none;
        border: none;
        cursor: pointer;
        margin-left: 5px;
        font-size: 18px;
        user-select: none;
    }

    .error-message {
        color: red;
        font-size: 12px;
        margin-left: 5px;
    }

    .hidden {
        display: none;
    }
</style>

<body>
    <div id="login-form">
        email: <input type="email" name="email" id="email">
        <span class="password-container">
            mot de passe: <input type="password" name="mdp" id="mdp">
            <button type="button" class="toggle-password" aria-label="Afficher / cacher le mot de passe">👁️</button>
        </span>
        <button onclick="login()">connecter</button>
        <a href="#" onclick="showRegisterForm()">s'inscrire</a>
        <p class="succes" id="succes" style="display:block; color:green;"></p>
    </div>

    <div id="register-form" class="hidden">
        email: <input type="email" name="reg_email" id="reg_email">
        nom: <input type="text" name="reg_nom" id="reg_nom">
        <span class="password-container">
            mot de passe: <input type="password" name="reg_mdp" id="reg_mdp" oninput="validatePassword()">
            <button type="button" class="toggle-password" aria-label="Afficher / cacher le mot de passe">👁️</button>
        </span>
        <span class="password-container">
            confirmer mot de passe: <input type="password" name="reg_mdp_confirm" id="reg_mdp_confirm" oninput="validatePassword()">
            <button type="button" class="toggle-password" aria-label="Afficher / cacher le mot de passe">👁️</button>
        </span>
        <div id="password-error" class="error-message"></div>
        role: <select name="role" id="role">
            <option value="client">client</option>
            <option value="finance">finance</option>
        </select>
        <button onclick="register()">s'inscrire</button>
        <a href="#" onclick="showLoginForm()">se connecter</a>
    </div>

    <script>
        // Fonctions pour basculer entre les formulaires
        function showRegisterForm() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
            document.title = "Inscription";
        }

        function showLoginForm() {
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
            document.title = "Login";
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

        // Gestion de l'affichage/masquage des mots de passe
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('input[type="password"], input[type="text"]');
                if (input.type === 'password') {
                    input.type = 'text';
                    button.textContent = '🙈';
                } else {
                    input.type = 'password';
                    button.textContent = '👁️';
                }
            });
        });

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

        function login() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('mdp').value;

            if (!email || !password) {
                showError('Veuillez remplir tous les champs');
                return;
            }

            ajax('POST', '/users/login', `email=${encodeURIComponent(email)}&mot_de_passe=${encodeURIComponent(password)}`, (response) => {
                if (response.success) {
                    window.location.href = 'index.php';
                } else {
                    const errorElement = document.getElementById('login-error');
                    errorElement.textContent = "response.success";
                }
            }, (xhr) => {
                // Si ton ajax() supporte un callback d’erreur (comme xhr.status !== 200)
                showError(xhr.responseText || 'Erreur serveur inconnue.');
            });
        }


        function register() {
            if (!validatePassword()) {
                alert('Veuillez corriger les erreurs avant de soumettre.');
                return;
            }

            const data = {
                email: document.getElementById('reg_email').value,
                mot_de_passe: document.getElementById('reg_mdp').value,
                role_user: document.getElementById('role').value,
                nom: 'nom', // À remplacer par des champs supplémentaires si nécessaire
                prenom: 'prenom', // À remplacer par des champs supplémentaires si nécessaire
                date_de_naissance: '2000-01-01' // À remplacer par un champ date si nécessaire
            };

            if (!data.email || !data.mot_de_passe) {
                alert('Veuillez remplir tous les champs obligatoires');
                return;
            }

            ajax('POST', '/users', JSON.stringify(data), (response) => {
                if (response.id) {
                    alert('Inscription réussie ! Vous pouvez maintenant vous connecter.');
                    showLoginForm();
                } else {
                    alert("Erreur lors de l'inscription");
                }
            }, 'application/json');
        }

        function showError(message) {
            const errorElement = document.getElementById('login-error');
            errorElement.textContent = message;
        }
    </script>
</body>

</html>