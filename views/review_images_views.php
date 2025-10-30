<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/header.php'; // load header and translations

// check if user is valid
if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
    echo $t['access_denied'] ?? 'access denied';
    exit;
}

$image = $_GET['img'] ?? null;
$uploadDir = __DIR__ . '/../uploads/';

// check if image exists
if (!$image || !file_exists($uploadDir . $image)) {
    echo $t['image_not_found'] ?? 'image not found';
    exit;
}

// store current image in session for mosaic
$_SESSION['last_image'] = $image;
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['review_mosaics_title'] ?? 'Your generated mosaics' ?></title>
<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background-color: <?= ($_SESSION['theme'] ?? 'light')==='dark' ? '#121212' : '#f9f9f9' ?>;
    color: <?= ($_SESSION['theme'] ?? 'light')==='dark' ? '#ffffff' : '#333333' ?>;
    text-align: center;
    padding: 40px;
}
h2 { font-size: 2em; margin-bottom: 40px; color: #0077cc; }

form { display: flex; justify-content: center; flex-wrap: wrap; gap: 40px; }

.img-box {
    background-color: <?= ($_SESSION['theme'] ?? 'light')==='dark' ? '#1e1e1e' : '#ffffff' ?>;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.25);
    padding: 20px;
    width: 300px;
    transition: transform 0.3s ease;
    text-align: center;
}
.img-box:hover { transform: scale(1.03); }
.img-box img { width: 100%; border-radius: 8px; margin-bottom: 10px; }

.blue img { filter: saturate(150%) hue-rotate(190deg); }
.red img { filter: saturate(150%) hue-rotate(-20deg); }
.bw img { filter: grayscale(100%); }

.details { font-size: 0.9em; margin-top: 8px; line-height: 1.4em; color: #666; }
.radio-choice { margin-top: 10px; }
.radio-choice input { transform: scale(1.3); cursor: pointer; }

.submit-btn {
    margin-top: 40px;
    background-color: #0077cc;
    color: white;
    border: none;
    padding: 14px 28px;
    font-size: 1.1em;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}
.submit-btn:hover { background-color: #005fa3; }
</style>
</head>
<body>

<h2><?= $t['review_mosaics_title'] ?? 'Your generated mosaics' ?></h2>

<form action="../control/mosaic_control.php" method="POST">
    <div class="img-box blue">
        <img src="../uploads/<?= htmlspecialchars($image) ?>" alt="<?= $t['blue_version'] ?? 'Blue version' ?>">
        <label>
            <input type="radio" name="choice" value="blue" required> <?= $t['select'] ?? 'Select' ?>
        </label>
    </div>

    <div class="img-box red">
        <img src="../uploads/<?= htmlspecialchars($image) ?>" alt="<?= $t['red_version'] ?? 'Red version' ?>">
        <label>
            <input type="radio" name="choice" value="red"> <?= $t['select'] ?? 'Select' ?>
        </label>
    </div>

    <div class="img-box bw">
        <img src="../uploads/<?= htmlspecialchars($image) ?>" alt="<?= $t['bw_version'] ?? 'Black & White' ?>">
        <label>
            <input type="radio" name="choice" value="bw"> <?= $t['select'] ?? 'Select' ?>
        </label>
    </div>

    <button type="submit" class="submit-btn"><?= $t['confirm_choice'] ?? 'Confirm my choice' ?></button>
</form>

</body>
</html>
