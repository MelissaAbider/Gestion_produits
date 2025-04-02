<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits</title>
    <link rel="stylesheet" href="./css/styles.css">
    <script>
        // script pour conserver la position de défilement après le rechargement de la page
        document.addEventListener("DOMContentLoaded", function() {
            // restaurer la position de défilement si elle existe
            if (sessionStorage.getItem('scrollPosition')) {
                window.scrollTo(0, sessionStorage.getItem('scrollPosition'));
            }

            // stocker la position de défilement avant de soumettre un formulaire ou cliquer sur un lien d'action
            const saveScrollPosition = function() {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            };

            // appliquer aux formulaires
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', saveScrollPosition);
            });

            // appliquer aux liens d'action
            document.querySelectorAll('a.button, a.delete-btn').forEach(link => {
                link.addEventListener('click', saveScrollPosition);
            });
        });
    </script>
</head>

<body>
    <div class="container">
        <h1>Gestion des Produits</h1>

        <!-- panneau utilisateur (connexion/déconnexion) -->
        <div class="user-panel">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="welcome-message">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <a href="index.php?action=logout" class="logout-btn">Se déconnecter</a>
            <?php else: ?>
                <a href="index.php?action=login" class="login-btn">Se connecter</a>
            <?php endif; ?>
        </div>

        <!-- affichage des messages d'erreur -->
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="error-message">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); // effacer le message après l'affichage
                ?>
            </div>
        <?php endif; ?>

        <!-- affichage des messages de succès -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); // effacer le message après l'affichage
                ?>
            </div>
        <?php endif; ?>