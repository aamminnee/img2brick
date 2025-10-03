<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
</head>
<body>
<header>
<?php if(isset($_SESSION['username'])): ?>
    <p>Connecté en tant que : <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> | 
    <a href="../control/user_control.php?action=logout">Déconnexion</a> | 
    <a href="#">Mon Panier</a></p>
<?php else: ?>
    <p><a href="../views/login_views.php">Connexion</a></p>
    <p><a href="../views/register_views.php">Inscription</a></p>
<?php endif; ?>
<hr>
</header>

