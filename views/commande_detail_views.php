<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// check that commande and mosaic exist
if (!isset($commande) || !isset($mosaic)) {
    echo "<p>error: order not found.</p>";
    exit;
}

// translations
$tr = $t ?? [];

// order info
$status = $commandeModel->getCommandeStatusById($commande['id_commande']);
$statusClass = strtolower(str_replace(' ', '-', $status));
$orderCode = 'CMD-' . date('Y', strtotime($commande['date_commande'])) . '-' . str_pad($commande['id_commande'], 5, '0', STR_PAD_LEFT);

// mosaic image
$identifiant = $mosaic['identifiant'] ?? ''; 
$thumb = '../mosaic_data/' . htmlspecialchars($identifiant ?: 'placeholder.png');

// detect image color filter from identifiant
$filterCSS = 'none';
if (stripos($identifiant, 'blue') !== false) {
    $filterCSS = 'brightness(1.1) saturate(1.4) hue-rotate(200deg)';
    $color = 'Blue';
} elseif (stripos($identifiant, 'red') !== false) {
    $filterCSS = 'brightness(1.1) saturate(1.4) hue-rotate(-20deg)';
    $color = 'Red';
} elseif (stripos($identifiant, 'bw') !== false) {
    $filterCSS = 'grayscale(100%) contrast(1.1)';
    $color = 'Black & White';
}

// calculate estimated dates
$dateCommande = strtotime($commande['date_commande']);
$expeditionDate = date('d/m/Y', strtotime('+2 days', $dateCommande));
$livraisonDate = date('d/m/Y', strtotime('+7 days', $dateCommande));

$size = @getimagesize($thumb); // le @ évite un warning si le fichier n'existe pas
if ($size) {
    $width = $size[0];
    $height = $size[1];
    $sizeText = $width . '×' . $height . ' px';
} else {
    $sizeText = '-';
}
?>


<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tr['order_details'] ?? 'Order Details') ?></title>
    <style>
        /* global style */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
            color: #222;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 900px;
            background: #fff;
            margin: auto;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: #333;
            border-left: 5px solid #0078d7;
            padding-left: 10px;
        }

        p {
            margin: 6px 0;
        }

        /* palette design */
        .blue-theme {
            --accent-color: #0078d7;
        }
        .red-theme {
            --accent-color: #d71e00;
        }
        .bw-theme {
            --accent-color: #444;
        }

        /* button style */
        .btn {
            display: inline-block;
            background-color: var(--accent-color, #0078d7);
            color: white;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: 0.2s ease;
        }
        .btn:hover {
            opacity: 0.9;
        }

        /* order status colors */
        .status.waiting { color: orange; font-weight: bold; }
        .status.shipped { color: royalblue; font-weight: bold; }
        .status.delivered { color: green; font-weight: bold; }

        /* mosaic image */
        img.mosaic {
            width: 200px;
            border-radius: 10px;
            margin: 10px 0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        /* document links */
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            margin: 6px 0;
        }
        a {
            color: var(--accent-color, #0078d7);
        }
    </style>
</head>
<body class="<?php 
    if (stripos($identifiant, 'blue') !== false) echo 'blue-theme';
    elseif (stripos($identifiant, 'red') !== false) echo 'red-theme';
    elseif (stripos($identifiant, 'bw') !== false) echo 'bw-theme';
?>">
    <div class="container">
        <h2><?= htmlspecialchars($tr['order_details'] ?? 'order details') ?> - <?= htmlspecialchars($orderCode) ?></h2>
        <p><strong><?= $tr['date'] ?? 'date' ?>:</strong> <?= date('d/m/Y', strtotime($commande['date_commande'])) ?></p>
        <p><strong><?= $tr['status'] ?? 'status' ?>:</strong> 
            <span class="status <?= htmlspecialchars($statusClass) ?>"><?= htmlspecialchars($status) ?></span>
        </p>

        <?php if (strtolower($status) !== 'livrée' && strtolower($status) !== 'delivered'): ?>
            <p>
                <strong><?= $tr['estimated_shipping'] ?? 'estimated shipping' ?>:</strong> <?= htmlspecialchars($expeditionDate) ?><br>
                <strong><?= $tr['estimated_delivery'] ?? 'estimated delivery' ?>:</strong> <?= htmlspecialchars($livraisonDate) ?>
            </p>
        <?php endif; ?>


        <h3><?= $tr['delivery_address'] ?? 'delivery address' ?></h3>
        <p>
            <?= htmlspecialchars($commande['adresse']) ?><br>
            <?= htmlspecialchars($commande['code_postal']) ?> <?= htmlspecialchars($commande['ville']) ?><br>
            <?= htmlspecialchars($commande['pays']) ?><br>
            <?= htmlspecialchars($commande['telephone']) ?>
        </p>

        <h3><?= $tr['mosaic_details'] ?? 'mosaic details' ?></h3>
        <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($tr['selected_mosaic'] ?? 'selected mosaic') ?>" 
             class="mosaic" style="filter: <?= htmlspecialchars($filterCSS) ?>;">

        <p><strong><?= $tr['size'] ?? 'size' ?>:</strong> <?= htmlspecialchars($sizeText) ?></p>
        <p><strong><?= $tr['palette'] ?? 'palette' ?>:</strong> <?= htmlspecialchars($color ?? '-') ?></p>

        <h3><?= $tr['documents'] ?? 'downloadable documents' ?></h3>
        <ul>
            <li><a href="<?= $thumb ?>" download><?= $tr['download_image'] ?? 'download final image' ?></a></li>
        </ul>

        <a class="btn" href="mailto:amine.mourali77@gmail.com"><?= htmlspecialchars($tr['contact_support'] ?? 'contact support') ?></a>
    </div>
</body>
</html>
