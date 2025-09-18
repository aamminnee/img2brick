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
    <a href="../control/commande_controller.php?action=view">Mon Panier</a></p>
<?php else: ?>
    <p><a href="../views/user_views.php">Connexion</a></p>
<?php endif; ?>
<hr>
</header>

