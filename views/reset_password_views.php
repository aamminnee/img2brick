<?php 
if (isset($message)) echo "<p>$message</p>"; 
Require_once __DIR__ . '/header.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['reset_password_title'] ?? 'Reset your password' ?></title>  
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
    <h2><?= $t['reset_password_header'] ?? 'Reset Password' ?></h2>

    <form action="../control/user_control.php" method="post">
        <label for="password"><?= $t['new_password_label'] ?? 'New password' ?></label>
        <input type="password" name="password" id="password" required>

        <label for="password_confirm"><?= $t['confirm_password_label'] ?? 'Confirm password' ?></label>
        <input type="password" name="password_confirm" id="password_confirm" required>

        <button type="submit" name="reset_password"><?= $t['reset_password_button'] ?? 'Reset' ?></button>
    </form>
</body>
</html>
