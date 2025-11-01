<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/header.php';

$tr = $t ?? [];
$commandes = $commandes ?? [];
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $tr['orders_title'] ?? 'Mes commandes' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 24px;
            color: #222;
        }
        .container {
            width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        th { background: #f0f0f0; }
        img.thumb {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #919191ff;
        }
        .status.waiting { color: orange; font-weight: bold; }
        .status.shipped { color: royalblue; font-weight: bold; }
        .status.delivered { color: green; font-weight: bold; }
        .btn {
            background: #0078d7;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            text-decoration: none;
        }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
<div class="container">
    <h1><?= $tr['orders_title'] ?? 'Mes commandes' ?></h1>
    <p><?= $tr['orders_intro'] ?? 'Retrouvez ici toutes vos commandes précédentes.' ?></p>
    <a class="btn" href="../views/images_views.php"><?= $tr['new_order'] ?? 'Nouvelle commande' ?></a>

    <?php if (empty($commandes)): ?>
        <p><?= $tr['no_orders'] ?? 'Aucune commande trouvée.' ?></p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th><?= $tr['order_number'] ?? 'Numéro' ?></th>
                    <th><?= $tr['date'] ?? 'Date' ?></th>
                    <th><?= $tr['status'] ?? 'Statut' ?></th>
                    <th><?= $tr['mosaic'] ?? 'Mosaïque' ?></th>
                    <th><?= $tr['amount'] ?? 'Montant' ?></th>
                    <th><?= $tr['actions'] ?? 'Actions' ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commandes as $cmd): 
                    $status = $commandeModel->getCommandeStatusById($cmd['id_commande']);
                    $statusClass = strtolower(str_replace(' ', '-', $status));
                    $orderCode = 'CMD-' . date('Y', strtotime($cmd['date_commande'])) . '-' . str_pad($cmd['id_commande'], 5, '0', STR_PAD_LEFT);

                    // Vérifie que l'identifiant existe
                    $identifiant = $cmd['identifiant'] ?? '';

                    // Détermine le filtre CSS selon le nom du fichier
                    if (str_contains($identifiant, 'blue')) {
                        $filterCSS = 'brightness(1.1) saturate(1.4) hue-rotate(200deg)';
                    } elseif (str_contains($identifiant, 'red')) {
                        $filterCSS = 'brightness(1.1) saturate(1.4) hue-rotate(-20deg)';
                    } elseif (str_contains($identifiant, 'bw')) {
                        $filterCSS = 'grayscale(100%) contrast(1.1)';
                    } else {
                        $filterCSS = 'none';
                    }

                    // Image finale
                    $thumb = '../mosaic_data/' . htmlspecialchars($identifiant ?: 'placeholder.png');
                ?>
                <tr>
                    <td><?= htmlspecialchars($orderCode) ?></td>
                    <td><?= date('d/m/Y', strtotime($cmd['date_commande'])) ?></td>
                    <td class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($status) ?></td>
                    <td><img class="thumb" src="<?= htmlspecialchars($thumb) ?>" style="filter: <?= $filterCSS ?>;border-radius:1px" alt="mosaic"></td>
                    <td><?= number_format($cmd['montant'], 2, ',', ' ') ?> €</td>
                    <td><a class="btn" href="../control/commande_detail_control.php?id=<?= (int)$cmd['id_commande'] ?>"><?= $tr['view_details'] ?? 'Voir détails' ?></a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
