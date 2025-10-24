<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['verify_title'] ?? 'Account Verification' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
            color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        a {
            color: #0078d7;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2><?= $t['verify_header'] ?? 'Enter your verification code' ?></h2>

    <?php if (isset($message)) echo "<p>$message</p>"; ?>

    <form action="../control/user_control.php?action=tokenForm" method="post">
        <input type="text" name="token" maxlength="6" required 
               placeholder="<?= $t['verify_placeholder'] ?? '6 digits' ?>">
        <button type="submit"><?= $t['verify_button'] ?? 'Validate' ?></button>
    </form>
    <?php
        if (!isset($_SESSION['status']) || $_SESSION['status'] == 'invalide') {
            echo '<p><a href="../views/login_views.php">' . ($t['login_link_text'] ?? 'Go to Login') . '</a></p>';
        }
    ?>
</body>
</html>
