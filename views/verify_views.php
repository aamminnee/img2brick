<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['verify_title'] ?? 'Account Verification' ?></title>
</head>
<body>
    <h2><?= $t['verify_header'] ?? 'Enter your verification code' ?></h2>

    <?php if (isset($message)) echo "<p>$message</p>"; ?>

    <form action="../control/user_control.php?action=tokenForm" method="post">
        <input type="text" name="token" maxlength="6" required 
               placeholder="<?= $t['verify_placeholder'] ?? '6 digits' ?>">
        <button type="submit"><?= $t['verify_button'] ?? 'Validate' ?></button>
    </form>
</body>
</html>
