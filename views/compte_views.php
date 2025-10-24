<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/header.php';
require_once __DIR__ . '/../models/images_models.php';
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'fr' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['account_title'] ?? 'My Account' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
            color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#1e1e1e' : '#fff' ?>;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 40px; }
        .section h3 { margin-bottom: 10px; }
        .button {
            padding: 10px 20px;
            margin: 5px;
            background-color: #0078d7;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover { background-color: #005fa3; }
        .user-info p { margin: 5px 0; }
        .images-grid { display: flex; flex-wrap: wrap; gap: 10px; }
        .images-grid img { width: 120px; height: 120px; object-fit: cover; border-radius: 6px; }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px; width: 18px;
            left: 3px; bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: #0078d7; }
        input:checked + .slider:before { transform: translateX(26px); }
    </style>
</head>
<body>
<a href="../views/setting_views.php" class="button">
    <?= $t['back'] ?? 'back' ?>
</a>
<div class="container">
    <h2><?= $t['account_title'] ?? 'My Account' ?></h2>

    <!-- Section Images -->
    <div class="section">
        <h3><?= $t['account_images'] ?? 'Your Images' ?></h3>
        <div class="images-grid">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $img): ?>
                    <img src="../uploads/<?= htmlspecialchars($img) ?>" alt="User Image">
                <?php endforeach; ?>
            <?php else: ?>
                <p><?= $t['no_images'] ?? 'No images uploaded yet.' ?></p>
            <?php endif; ?>
        </div>
        <button class="button"><?= $t['view_more_images'] ?? 'View More' ?></button>
    </div>

    <!-- Section User Info -->
    <div class="section user-info">
        <h3><?= $t['account_info'] ?? 'Account Information' ?></h3>
        <p><?= $t['username'] ?? 'Username' ?>: <strong><?= $_SESSION['username'] ?? 'N/A' ?></strong></p>
        <p><?= $t['email'] ?? 'Email' ?>: <strong><?= $_SESSION['email'] ?? 'N/A' ?></strong></p>
        <p><?= $t['status'] ?? 'Status' ?>: <strong><?= $_SESSION['status'] ?? 'N/A' ?></strong></p>
        <?php if (isset($_SESSION['status']) && $_SESSION['status'] === 'invalide'): ?>
            <a href="../control/user_control.php?action=validateEmail" class="button">
                <?= $t['valide_email'] ?? 'Valide Email' ?>
            </a>
         <?php elseif (isset($_SESSION['status']) && $_SESSION['status'] === 'valide'): ?>
            <a href="../control/user_control.php?action=resetPassword" class="button">
                <?= $t['reset_password'] ?? 'Reset Password' ?>
            </a>
         <?php endif; ?>
    </div>

    <!-- Section Two-Factor Authentication -->
    <div class="section">
        <h3><?= $t['two_factor_auth'] ?? 'Two-Factor Authentication' ?></h3>
        <label class="toggle-switch">
            <input type="checkbox" id="twoFA">
            <span class="slider"></span>
        </label>
        <span><?= $t['enable_2fa'] ?? 'Enable 2FA' ?></span>
    </div>

</div>

</body>
</html>
