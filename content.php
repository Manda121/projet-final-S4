<?php
// content.php
if (basename($_SERVER['PHP_SELF']) === 'ajout_pret.php') {
    include 'ajout_pret.php';
} else {
    // Contenu par défaut ou redirection
    echo "<h2>Page par défaut</h2><p>Sélectionnez une option dans le menu.</p>";
}
?>