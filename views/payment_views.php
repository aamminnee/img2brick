<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/header.php';
require_once __DIR__ . '/../models/mosaic_models.php';

// check user access
if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
    echo $t['access_denied'] ?? 'access denied';
    exit;
}

// check if mosaic is selected
if (empty($_SESSION['selected_mosaic'])) {
    echo $t['no_mosaic_selected'] ?? 'no mosaic selected';
    exit;
}

// define prices by type
$prices = [
    'blue' => 19.99,
    'red'  => 21.49,
    'bw'   => 14.99
];
$prix = $prices[$_SESSION['selected_mosaic']];

// path to the mosaic image
$imagePath = "../mosaic_data/" . htmlspecialchars($_SESSION['selected_mosaic'] . '_' . $_SESSION['last_image'], ENT_QUOTES, 'UTF-8');

// css filter by mosaic type
$filterCSS = match($_SESSION['selected_mosaic']) {
    'blue' => 'brightness(1.1) saturate(1.4) hue-rotate(200deg)',
    'red'  => 'brightness(1.1) saturate(1.4) hue-rotate(-20deg)',
    'bw'   => 'grayscale(100%) contrast(1.1)',
    default => 'none'
};
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['checkout_title'] ?? 'Finalize my order' ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #ffffff;
            color: #333;
            text-align: center;
            padding: 40px;
        }
        h2 { color: #0d47a1; font-size: 2rem; margin-bottom: 1.5rem; }
        .image-container { display: inline-block; margin-bottom: 20px; }
        .image-container img { max-width: 320px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.4s ease; }
        .image-container:hover img { transform: scale(1.05); }
        p { font-size: 1.2rem; margin-top: 15px; font-weight: bold; }
        form { background: #f9f9f9; width: 60%; max-width: 600px; margin: 40px auto; padding: 25px 30px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); text-align: left; }
        fieldset { border: 1px solid #90caf9; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        legend { color: #1565c0; font-weight: bold; padding: 0 10px; }
        input[type="text"], input[type="tel"] { width: calc(50% - 10px); padding: 10px; margin: 8px 5px; border: 1px solid #ccc; border-radius: 6px; font-size: 1rem; }
        input[readonly] { background-color: #f3f3f3; }
        button { background-color: #1565c0; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-size: 1.1rem; cursor: pointer; transition: background 0.3s; display: block; margin: 0 auto; }
        button:hover { background-color: #0d47a1; }
        </style>
</head>
<body>

    <h2><?= $t['checkout_title'] ?? 'Finalize my order' ?></h2>

    <div class="image-container">
        <img src="<?= $imagePath ?>" alt="<?= $t['selected_mosaic'] ?? 'Selected mosaic' ?>" style="filter: <?= $filterCSS ?>;">
    </div>

    <p><?= $t['estimated_price'] ?? 'Estimated price' ?> : <?= number_format($prix, 2, ',', ' ') ?> €</p>

    <form action="../control/payment_control.php" method="POST">
        <fieldset>
            <legend><?= $t['shipping_info'] ?? 'Shipping Information' ?></legend>
            <input type="text" name="address" placeholder="<?= $t['address'] ?? 'Address' ?>" value="2 rue Albert Einstein" required>
            <input type="text" name="postal" placeholder="<?= $t['postal_code'] ?? 'Postal Code' ?>" value="77420" required>
            <input type="text" name="city" placeholder="<?= $t['city'] ?? 'City' ?>" value="Champs sur Marne" required>
            <input type="text" name="country" placeholder="<?= $t['country'] ?? 'Country' ?>" value="France" required>
            <input type="tel" name="phone" placeholder="<?= $t['phone'] ?? 'Phone' ?>" value="0160957500" required>
        </fieldset>

        <fieldset>
            <legend><?= $t['payment'] ?? 'Payment' ?></legend>
            <input type="text" name="card_number" value="4242 4242 4242 4242" required>
            <input type="text" name="expiry" value="12/34" required>
            <input type="text" name="cvc" value="123" required>
        </fieldset>

        <button type="submit"><?= $t['confirm_order'] ?? 'Confirm Order' ?></button>
    </form>

    <p><?= $t['payment_info'] ?? 'Simulated payment method as part of the project (no real payment)' ?></p>

</body>
</html>
