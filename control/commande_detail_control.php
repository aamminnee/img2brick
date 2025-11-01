<?php
session_start();
require_once __DIR__ . '/../models/commande_models.php';
require_once __DIR__ . '/../models/mosaic_models.php';

$commandeModel = new CommandeModel();
$mosaicModel = new MosaicModel();
// check commande id in GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$commande = $commandeModel->getCommandeById($id);
// get mosaic details
$mosaic = $mosaicModel->getMosaicById($commande['id_mosaic']);
include __DIR__ . '/../views/header.php';
$tr = $t ?? [];
// Inclut la vue
include __DIR__ . '/../views/commande_detail_views.php';
?>
