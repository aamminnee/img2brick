<?php if (isset($message)) echo "<p>$message</p>"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>modifie ton mot de passe</title>    
</head>
<body>
    <h2>Connexion</h2>
    <form action="../control/user_control.php" method="post">
        <label for="password">password</label>
        <input type="password" name="password" id="password" required>
        <label for="password">password 2</label>
        <input type="password" name="password" id="password" required>
        <button type="submit" name="reset_password">reinitialisation</button>
    </form>
</body>
</html>
