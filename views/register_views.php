<?php
if (isset($message)) echo "<p>$message</p>";
include __DIR__ . '/header.php';
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['register_title'] ?? 'User Registration' ?></title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
    color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
    transition: all 0.3s ease;
}
</style>
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
