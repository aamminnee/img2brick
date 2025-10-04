<?php if (isset($message)) echo "<p>$message</p>"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>inscription utilisateur</title>
</head>
<body>
    <h2>Inscription</h2>
    <form action="../control/user_control.php" method="post">
        <label for="username">Nom d'utilisateur</label>
        <input type="text" name="username" id="username" required>
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>
        <button type="submit" name="register">Se connecter</button>
    </form> 
</body>
</html>
