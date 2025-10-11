<!DOCTYPE html>
<html>
<head>
    <title>Vérification de compte</title>
</head>
<body>
    <h2>Entrez votre code de vérification</h2>

    <?php if (isset($message)) echo "<p>$message</p>"; ?>

    <form  action="../control/user_control.php?action=tokenForm" method="post">
        <input type="text" name="token" maxlength="6" required placeholder="6 chiffres">
        <button type="submit">Valider</button>
    </form>
</body>
</html>
