<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/header.php';

//security check: ensure user is logged in and validated
if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
    echo $t['access_denied'] ?? "Access denied.";
    exit;
}

//check if an image is provided
if (!isset($_GET['img'])) {
    echo $t['no_image_selected'] ?? "No image selected.";
    exit;
}

$image = $_GET['img'];
$uploadDir = __DIR__ . '/../uploads/';

//verify that image exists
if (!file_exists($uploadDir . $image)) {
    echo $t['image_not_found'] ?? "Image not found.";
    exit;
}

$imagePath = "../uploads/" . htmlspecialchars($image);
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
    <meta charset="UTF-8">
    <title><?= $t['crop_preview_title'] ?? 'Crop and Preview' ?></title>

    <!-- cropper.js -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>

    <style>
        * { box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
            color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        header { width: 100%; }

        main {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        //main area
        #main-container {
            flex: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow-y: auto;
        }

        #image-container {
            max-width: 80%;
            max-height: 70vh;
            border: 2px solid royalblue;
            border-radius: 10px;
            overflow: hidden;
        }

        #image { max-width: 100%; display: block; }

        #cropButton {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            background-color: royalblue;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        #cropButton:hover { background-color: dodgerblue; }

        #message { text-align: center; margin-top: 15px; font-weight: bold; }

        //side panel
        #options-panel {
            flex: 1;
            background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#1e1e1e' : '#ffffff' ?>;
            border-left: 2px solid #ccc;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            overflow-y: auto;
        }

        #options-panel h3 { text-align: center; color: royalblue; }
        .option-group { margin-top: 20px; }
        select, button { width: 100%; padding: 8px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; }
        #warnings { color: orange; font-weight: bold; margin-top: 10px; min-height: 1.5em; text-align: center; }
    </style>
</head>

<body>
    <header>
        <!-- header included via include 'header.php' -->
    </header>

    <main>
        <!-- main area -->
        <div id="main-container">
            <h2><?= $t['crop_your_image'] ?? 'Crop your image' ?></h2>
            <div id="image-container">
                <img id="image" src="<?= $imagePath ?>" data-original-name="<?= htmlspecialchars($image) ?>" alt="<?= $t['image_to_crop'] ?? 'Image to crop' ?>">
            </div>

            <button id="cropButton"><?= $t['apply_continue'] ?? 'Apply and Continue' ?></button>
            <div id="message"></div>
        </div>

        <aside id="options-panel">
            <h3><?= $t['render_options'] ?? 'Render Options' ?></h3>

            <div class="option-group">
                <label for="size"><?= $t['board_size'] ?? 'Board size:' ?></label>
                <select id="size">
                    <option value="32"><?= $t['size_32'] ?? '32 x 32 studs' ?></option>
                    <option value="64"><?= $t['size_64'] ?? '64 x 64 studs' ?></option>
                    <option value="96"><?= $t['size_96'] ?? '96 x 96 studs' ?></option>
                </select>
            </div>

            <div class="option-group">
                <label for="aspect"><?= $t['crop_ratio'] ?? 'Crop ratio:' ?></label>
                <select id="aspect">
                    <option value="NaN"><?= $t['ratio_free'] ?? 'Free' ?></option>
                    <option value="1"><?= $t['ratio_square'] ?? 'Square (1:1)' ?></option>
                    <option value="4/3"><?= $t['ratio_43'] ?? '4:3' ?></option>
                    <option value="16/9"><?= $t['ratio_169'] ?? '16:9' ?></option>
                </select>
            </div>

            <div id="warnings"></div>
        </aside>
    </main>

    <script>
        const imageName = "<?= htmlspecialchars($image) ?>";
    </script>
    <script src="JS/crop_images.js"></script>
</body>
</html>
