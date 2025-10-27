<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
    echo "Access denied.";
    exit;
}

$image = $_GET['img'] ?? null;
$uploadDir = __DIR__ . '/../uploads/';

if (!$image || !file_exists($uploadDir . $image)) {
    echo "Image not found.";
    exit;
}

$imagePath = "../uploads/" . htmlspecialchars($image);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Crop Your Image</title>

    <!-- Cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
            color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
            text-align: center;
            padding: 30px;
        }

        #image-container {
            max-width: 600px;
            margin: 20px auto;
        }

        img {
            max-width: 100%;
            display: block;
        }

        #cropButton {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background-color: royalblue;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #cropButton:hover {
            background-color: dodgerblue;
        }

        #message {
            margin-top: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h2>Crop Your Image Before Processing</h2>

    <div id="image-container">
        <!-- ✅ We add data-original-name to send it to JS -->
        <img id="image" src="<?= $imagePath ?>" data-original-name="<?= htmlspecialchars($image) ?>" alt="To Crop">
    </div>

    <button id="cropButton">Save & Continue</button>
    <div id="message"></div>

    <!-- ✅ External Script -->
    <script src="JS/crop_images.js"></script>
</body>
</html>
