<?php 
if (isset($message)) echo "<p>$message</p>"; 
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['register_title'] ?? 'User Registration' ?></title>
</head>
<body>
    <h2><?= $t['register_header'] ?? 'Register' ?></h2>

    <form action="../control/user_control.php" method="post">
        <label for="username"><?= $t['username_label'] ?? 'Username' ?></label>
        <input type="text" name="username" id="username" required>

        <label for="email"><?= $t['email_label'] ?? 'Email' ?></label>
        <input type="email" name="email" id="email" required>

        <label for="password"><?= $t['password_label'] ?? 'Password' ?></label>
        <input type="password" name="password" id="password" required>

        <button type="submit" name="register"><?= $t['register_button'] ?? 'Register' ?></button>
    </form>
</body>
</html>
