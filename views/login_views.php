<?php 
if (isset($message)) echo "<p>$message</p>"; 
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['login_title'] ?? 'User Login' ?></title>    
</head>
<body>
    <h2><?= $t['login_header'] ?? 'Login' ?></h2>

    <form action="../control/user_control.php" method="post">
        <label for="username"><?= $t['username_label'] ?? 'Username' ?></label>
        <input type="text" name="username" id="username" required>

        <label for="password"><?= $t['password_label'] ?? 'Password' ?></label>
        <input type="password" name="password" id="password" required>

        <button type="submit" name="login"><?= $t['login_button'] ?? 'Login' ?></button>
    </form>
</body>
</html>
