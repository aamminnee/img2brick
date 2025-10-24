<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/translation_models.php';

// Load translations dynamically based on selected language
$lang = $_SESSION['lang'] ?? 'fr';
$translationModel = new TranslationModel();
$t = $translationModel->getTranslations($lang);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
</head>
<body>
<header>
<?php if (isset($_SESSION['username'])): ?>
    <p>
        <?= $t['logged_in_as'] ?? 'Connecté en tant que' ?> :
        <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> |
        <a href="../control/user_control.php?action=logout">
            <?= $t['logout'] ?? 'Déconnexion' ?>
        </a> |
        <a href="#">
            <?= $t['cart'] ?? 'Mon Panier' ?>
        </a>
    </p>
<?php else: ?>
    <p>
        <a href="../views/login_views.php">
            <?= $t['login'] ?? 'Connexion' ?>
        </a>
    </p>
    <p>
        <a href="../views/register_views.php">
            <?= $t['register'] ?? 'Inscription' ?>
        </a>
    </p>
<?php endif; ?>
<p>
    <a href="../control/setting_control.php">
        <?= $t['settings'] ?? 'Paramètres' ?>
    </a>
</p>
<p>
    <a href="../views/images_views.php">
        <?= $t['home'] ?? 'Acceuil' ?>
    </a>
</p>
<p>
    <a href="../views/compte_views.php">
        <?= $t['my_account'] ?? 'My Account' ?>
    </a>
</p>
<hr>
</header>
