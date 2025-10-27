<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/header.php';

$isValidUser = isset($_SESSION['user_id']) && ($_SESSION['status'] ?? '') === 'valide';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>img2brick - Dépose d'image</title>
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

#message { margin-top: 15px; font-weight: bold; min-height: 1.5em; }
#continueButton { margin-top: 25px; padding: 10px 25px; background-color: royalblue; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 1rem; display: none; transition: background-color 0.3s; }
#continueButton:hover { background-color: dodgerblue; }
#fileInput { display: none; }
#fileLabel { display: inline-block; padding: 10px 20px; border: 1px solid royalblue; background-color: transparent; color: royalblue; border-radius: 8px; cursor: pointer; transition: background-color 0.3s, color 0.3s; }
#fileLabel:hover { background-color: royalblue; color: white; }
#preview { margin-top: 20px; max-width: 300px; display: none; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.2); }
</style>
</head>

<body>
<h1>img2brick</h1>
<p>Transformez vos images en tableaux de briques !</p>

<div id="drop-zone">
    <p id="drop-text">Glissez-déposez votre image ici, collez-la avec <strong>Ctrl + V</strong>, ou utilisez le bouton ci-dessous.</p>
</div>


<label id="fileLabel" for="fileInput">Choisir un fichier</label>
<input type="file" id="fileInput" accept=".jpg,.jpeg,.png,.webp">

<div id="message"></div>
<button id="continueButton">Continuer</button>

<script>
const isValidUser = <?= json_encode($isValidUser) ?>;
</script>
<script src="JS/drag&drop.js"></script>
</body>
</html>
