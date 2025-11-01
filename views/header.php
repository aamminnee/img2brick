<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// load translation model
require_once __DIR__ . '/../models/translation_models.php';

// load translations dynamically according to chosen language
$lang = $_SESSION['lang'] ?? 'en';
$theme = $_SESSION['theme'] ?? 'light';
$translationModel = new TranslationModel();
$t = $translationModel->getTranslations($lang);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <style>
        /* base style and theme adaptation */
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: <?= $theme === 'dark' ? '#121212' : '#f5f5f5' ?>;
            color: <?= $theme === 'dark' ? '#f0f0f0' : '#222' ?>;
            margin: 0;
            padding: 0;
        }

        /* header layout */
        header {
            background-color: <?= $theme === 'dark' ? '#1e1e1e' : '#ffffff' ?>;
            color: <?= $theme === 'dark' ? '#f0f0f0' : '#222' ?>;
            padding: 15px 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        header p {
            margin: 5px 10px;
        }

        /* link styling */
        a {
            color: <?= $theme === 'dark' ? '#4da6ff' : '#0078d7' ?>;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        a:hover {
            color: <?= $theme === 'dark' ? '#82c7ff' : '#005fa3' ?>;
            text-decoration: underline;
        }

        hr {
            border: none;
            border-top: 1px solid <?= $theme === 'dark' ? '#333' : '#ddd' ?>;
            margin-top: 10px;
        }

        .header-left, .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .username {
            font-weight: bold;
            color: <?= $theme === 'dark' ? '#fff' : '#000' ?>;
        }
    </style>
</head>
<body>
<header>
    <div class="header-left">
        <a href="../views/images_views.php"><?= $t['home'] ?? 'Home' ?></a>
        <a href="../views/compte_views.php"><?= $t['my_account'] ?? 'My Account' ?></a>
        <a href="../control/setting_control.php"><?= $t['setting_title'] ?? 'Settings' ?></a>
    </div>

    <div class="header-right">
        <?php if (isset($_SESSION['username'])): ?>
            <span>
                <?= $t['logged_in_as'] ?? 'Logged in as' ?> :
                <span class="username"><?= htmlspecialchars($_SESSION['username']) ?></span>
            </span>
            |
            <a href="../control/user_control.php?action=logout"><?= $t['logout'] ?? 'Logout' ?></a>
            <a href="../control/commande_control.php">commande</a>
        <?php else: ?>
            <a href="../views/login_views.php"><?= $t['login'] ?? 'Login' ?></a>
            <a href="../views/register_views.php"><?= $t['register'] ?? 'Register' ?></a>
        <?php endif; ?>
    </div>
</header>
<hr>
