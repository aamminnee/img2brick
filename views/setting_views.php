<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['setting_title'] ?? 'Settings' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
            color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
            transition: all 0.3s ease;
        }
        .setting-container {
            max-width: 600px;
            margin: 60px auto;
            padding: 20px;
            border-radius: 8px;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#1e1e1e' : '#fff' ?>;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 { text-align: center; }
        .setting-item { margin: 20px 0; text-align: center; }
        .button {
            padding: 10px 20px;
            background-color: #0078d7;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            margin: 5px;
        }
        .button:hover { background-color: #005fa3; }
    </style>
</head>
<body>
<div class="setting-container">
    <h1><?= $t['setting_title'] ?? 'Settings' ?></h1>

    <div class="setting-item">
        <h3><?= $t['language'] ?? 'Language' ?></h3>
        <a href="?action=setLanguage&lang=fr" class="button">🇫🇷 Français</a>
        <a href="?action=setLanguage&lang=en" class="button">🇬🇧 English</a>
    </div>

    <div class="setting-item">
        <h3><?= $t['theme'] ?? 'Theme' ?></h3>
        <a href="?action=setTheme&theme=light" class="button"><?= $t['light'] ?? 'Light' ?></a>
        <a href="?action=setTheme&theme=dark" class="button"><?= $t['dark'] ?? 'Dark' ?></a>
    </div>

    <div class="setting-item">
        <h3><?= $t['account'] ?? 'Account' ?></h3>
        <a href="../views/compte.php" class="button">👤 <?= $t['my_account'] ?? 'My Account' ?></a>
    </div>
</div>
</body>
</html>
