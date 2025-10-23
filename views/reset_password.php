<?php if (isset($message)) echo "<p>$message</p>"; ?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['reset_password_title'] ?? 'Reset your password' ?></title>    
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
