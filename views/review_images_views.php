<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/header.php'; 
if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
    echo "Accès refusé.";
    exit;
}

$image = $_GET['img'] ?? null;
$uploadDir = __DIR__ . '/../uploads/';

if (!$image || !file_exists($uploadDir . $image)) {
    echo "Image introuvable.";
    exit;
}

$imagePath = "../uploads/" . htmlspecialchars($image);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Image Review</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
        color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
        text-align: center;
        padding: 30px;
    }

    .gallery {
        display: flex;
        justify-content: center;
        gap: 30px;
        flex-wrap: wrap;
    }

    .img-box img {
        width: 300px;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease;
    }

    .img-box img:hover {
        transform: scale(1.05);
    }

    .grayscale img { filter: grayscale(100%); }
    .sepia img { filter: sepia(100%); }
    .blur img { filter: blur(3px); }

</style>
</head>
<body>
<h2>Prévisualisation de votre image</h2>
<div class="gallery">
    <div class="img-box grayscale">
        <img src="<?= $imagePath ?>" alt="Grayscale">
        <p>Noir & Blanc</p>
    </div>
    <div class="img-box sepia">
        <img src="<?= $imagePath ?>" alt="Sepia">
        <p>Effet Sépia</p>
    </div>
    <div class="img-box blur">
        <img src="<?= $imagePath ?>" alt="Blur">
        <p>Effet Flou</p>
    </div>
</div>
</body>
</html>
