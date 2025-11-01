<?php
// controller for commandes: loads commandes for the current user and shows the view

session_start();

require_once __DIR__ . '/../models/commande_models.php';
require_once __DIR__ . '/../models/mosaic_models.php';

// check user logged in, if not redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../views/login_views.php");
    exit;
}

$commandeModel = new CommandeModel();
$mosaicModel = new MosaicModel();

$user_id = $_SESSION['user_id'];

// get commandes for user
$commandes = $commandeModel->getCommandeByUserId($user_id);

// include view (view must only display $commandes)
include __DIR__ . '/../views/commande_views.php';
