<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// reset previous mosaic session variables
unset($_SESSION['selected_mosaic']);
unset($_SESSION['last_image']);
unset($_SESSION['mosaic_id']);
unset($_SESSION['last_image_id']); // if also storing image id

// include header (loads translations and theme)
include __DIR__ . '/header.php';

// check if the user is valid
$isValidUser = isset($_SESSION['user_id']) && ($_SESSION['status'] ?? '') === 'valide';
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
<meta charset="UTF-8">
<title><?= $t['upload_title'] ?? 'img2brick - Upload Image' ?></title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
    color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
    text-align: center;
    padding: 40px;
}
h1 { font-size: 2rem; margin-bottom: 0.3rem; }
p { font-size: 1.1rem; color: gray; margin-bottom: 2rem; }

/* drop zone style */
#drop-zone {
    border: 2px dashed royalblue;
    border-radius: 10px;
    padding: 50px;
    width: 60%;
    max-width: 600px;
    margin: 0 auto;
    background-color: rgba(65,105,225,0.05);
    transition: background-color 0.3s, border-color 0.3s;
    cursor: pointer;
}
#drop-zone.dragover { background-color: rgba(65,105,225,0.15); border-color: dodgerblue; }
#drop-zone p { margin: 0; color: #333; }

/* buttons and preview */
#message { margin-top: 15px; font-weight: bold; min-height: 1.5em; }
#continueButton { 
    margin-top: 25px; padding: 10px 25px; 
    background-color: royalblue; color: white; 
    border: none; border-radius: 8px; cursor: pointer; 
    font-size: 1rem; display: none; transition: background-color 0.3s; 
}
#continueButton:hover { background-color: dodgerblue; }
#fileInput { display: none; }
#fileLabel { 
    display: inline-block; padding: 10px 20px; 
    border: 1px solid royalblue; background-color: transparent; 
    color: royalblue; border-radius: 8px; cursor: pointer; 
    transition: background-color 0.3s, color 0.3s; 
}
#fileLabel:hover { background-color: royalblue; color: white; }
#preview { 
    margin-top: 20px; max-width: 300px; 
    display: none; border-radius: 8px; 
    box-shadow: 0 2px 6px rgba(0,0,0,0.2); 
}
</style>
</head>

<body>
<h1>img2brick</h1>
<p><?= $t['upload_subtitle'] ?? 'Turn your images into brick mosaics!' ?></p>

<div id="drop-zone">
    <p id="drop-text">
        <?= $t['drop_instructions'] ?? 'Drag & drop your image here, paste it with <strong>Ctrl + V</strong>, or use the button below.' ?>
    </p>
</div>

<label id="fileLabel" for="fileInput"><?= $t['choose_file'] ?? 'Choose a file' ?></label>
<input type="file" id="fileInput" accept=".jpg,.jpeg,.png,.webp">

<div id="message"></div>
<button id="continueButton"><?= $t['continue'] ?? 'Continue' ?></button>

<script>
// check user validity
const isValidUser = <?= json_encode($isValidUser) ?>;
</script>
<script src="JS/drag&drop.js"></script>
</body>
</html>
