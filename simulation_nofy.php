<?php
session_start();
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
    <select id="listePrets"></select>
    <button onclick="simmuler()"></button>
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


    function chargerPretValider() {
    const select = document.querySelector("#listePrets");
    select.innerHTML = '<option value="">Choisir un prêt...</option>';

    ajax("GET", "/pretValider", null, (response) => {
        response.datas.forEach(pret => {
            const option = document.createElement("option");
            option.value = pret.id_pret;
            option.textContent = `Prêt #${pret.id_pret} - ${pret.client_nom} (${pret.montant}€)`;
            select.appendChild(option);
        });
    });
}
function simuler() {
    // 1. Récupérer l'ID du prêt sélectionné
    const selectPret = document.getElementById('listesPrets');
    const idPret = selectPret.value;
    
    // 2. Vérifier qu'un prêt est sélectionné
    if (!idPret) {
        alert('Veuillez sélectionner un prêt');
        return;
    }

    // 3. Afficher un indicateur de chargement
    const resultDiv = document.getElementById('resultatsSimulation');
    resultDiv.innerHTML = '<div class="loading">Chargement en cours...</div>';

    // 4. Envoyer la requête AJAX
    ajax("POST", "/simulerPret", `id_pret=${idPret}`, (response) => {
        try {
            // 5. Traitement de la réponse
            if (response.error) {
                throw new Error(response.error);
            }

            // 6. Affichage des résultats
            let html = `
                <h3>Résultats de simulation</h3>
                <div class="pret-info">
                    <p><strong>Client:</strong> ${response.infos_pret.client}</p>
                    <p><strong>Type:</strong> ${response.infos_pret.type}</p>
                    <p><strong>Montant initial:</strong> ${response.infos_pret.montant_initial.toFixed(2)} €</p>
                    <p><strong>Capital restant:</strong> ${response.statut.capital_restant.toFixed(2)} €</p>
                </div>
                
                <div class="simulation-summary">
                    <h4>Mensualité</h4>
                    <p>Total: <strong>${response.simulation.total_mensualite.toFixed(2)} €</strong></p>
                    <p>Dont intérêts: ${response.simulation.mensualite.toFixed(2)} €</p>
                    <p>Dont assurance: ${response.simulation.assurance_mensuelle.toFixed(2)} €</p>
                </div>
            `;

            // 7. Affichage du tableau d'amortissement (5 premières échéances)
            if (response.simulation.tableau_amortissement?.length > 0) {
                html += `
                    <h4>Prochaines échéances</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mois</th>
                                <th>Date</th>
                                <th>Capital</th>
                                <th>Intérêts</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                response.simulation.tableau_amortissement.slice(0, 5).forEach(echeance => {
                    html += `
                        <tr>
                            <td>${echeance.mois}</td>
                            <td>${echeance.date}</td>
                            <td>${echeance.capital.toFixed(2)} €</td>
                            <td>${echeance.interets.toFixed(2)} €</td>
                            <td>${echeance.mensualite.toFixed(2)} €</td>
                        </tr>
                    `;
                });

                html += `</tbody></table>`;
            }

            resultDiv.innerHTML = html;

        } catch (error) {
            // 8. Gestion des erreurs
            resultDiv.innerHTML = `<div class="alert alert-danger">Erreur: ${error.message}</div>`;
            console.error("Erreur de simulation:", error);
        }
    }, (error) => {
        // 9. Gestion des erreurs AJAX
        resultDiv.innerHTML = `<div class="alert alert-danger">Erreur serveur: ${error.statusText || 'Connexion impossible'}</div>`;
    });
}
//     function chargerPretValider() {
//     console.log("Tentative de chargement...");
//     ajax("GET", "/pretValider", null, 
//         (r) => console.log("Réponse:", r), 
//         (e) => console.error("Erreur:", e)
//     );
// }

    chargerPretValider();
  </script>

</body>
</html>