<?php
?>

<h2>Connexion</h2>

<?php if (!empty($error_message)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<form method="POST" action="index.php?action=login">
    <div class="form-group">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Se connecter</button>
</form>

<div class="login-links">
    <a href="index.php?action=home">Retour à l'accueil</a>
</div>