<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/header.php';

//security check: ensure order data exists
if (empty($_SESSION['order_id']) || empty($_SESSION['mosaic_id'])) {
    echo "<p>Error: no order found.</p>";
    exit;
}

//get order information (mocked for now)
$orderId = $_SESSION['order_id'];
$address = $_SESSION['address'] ?? "Unknown address";
$postal = $_SESSION['postal'] ?? "";
$city = $_SESSION['city'] ?? "";
$country = $_SESSION['country'] ?? "";
$price = $_SESSION['price'] ?? 0;
$imagePath = "../mosaic_data/" . htmlspecialchars($_SESSION['selected_mosaic'] . "_" . $_SESSION['last_image'], ENT_QUOTES, 'UTF-8');

//apply filter based on mosaic type
$filterCSS = match($_SESSION['selected_mosaic']) {
    'blue' => 'brightness(1.1) saturate(1.4) hue-rotate(200deg)',
    'red'  => 'brightness(1.1) saturate(1.4) hue-rotate(-20deg)',
    'bw'   => 'grayscale(100%) contrast(1.1)',
    default => 'none'
};
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['order_confirmation'] ?? 'Order Confirmation' ?></title>
<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background-color: #fff;
    color: #333;
    text-align: center;
    padding: 40px;
}
.container {
    max-width: 700px;
    margin: 0 auto;
    background: #f9f9f9;
    border-radius: 12px;
    padding: 30px 40px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
h1 {
    color: #2e7d32;
    font-size: 2.2rem;
}
p.subtitle {
    color: #555;
    margin-bottom: 30px;
    font-size: 1.1rem;
}
.summary {
    text-align: left;
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.summary img {
    max-width: 120px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-right: 15px;
}
.details {
    margin-top: 10px;
}
strong {
    color: #0d47a1;
}
button {
    background-color: #1565c0;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s;
    margin-top: 30px;
}
button:hover {
    background-color: #0d47a1;
}
.flex {
    display: flex;
    align-items: center;
}
</style>
</head>
<body>

<div class="container">
    <h1><?= $t['thank_you_order'] ?? 'Thank you for your order!' ?></h1>
    <p class="subtitle">
        <?= $t['order_processing'] ?? 'Your mosaic is being prepared.<br>You will receive a confirmation email shortly.' ?>
    </p>

    <div class="summary">
        <div class="flex">
            <img src="<?= $imagePath ?>" 
                alt="<?= $t['chosen_mosaic'] ?? 'Chosen Mosaic' ?>" 
                style="filter: <?= htmlspecialchars($filterCSS, ENT_QUOTES, 'UTF-8') ?>;">
            <div class="details">
                <p><strong><?= $t['order_number'] ?? 'Order Number:' ?></strong> <?= $orderId ?></p>
                <p><strong><?= $t['amount_paid'] ?? 'Amount Paid:' ?></strong> <?= number_format($price, 2, ',', ' ') ?> €</p>
            </div>
        </div>
    
        <hr style="margin: 15px 0;">

        <p><strong><?= $t['shipping_address'] ?? 'Shipping Address:' ?></strong><br>
        <?= htmlspecialchars("$address, $postal $city, $country", ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <form action="../views/images_views.php" method="POST">
        <button type="submit"><?= $t['return_home'] ?? 'Return to Home' ?></button>
    </form>
</div>

</body>
</html>
