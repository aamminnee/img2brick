<?php include __DIR__ . '/header.php'; ?>

<?php 
if (!isset($_SESSION['image'])) {
        echo "<h2>Uploader une image</h2>";
        echo "<form action='../control/images_control.php' method='post' enctype='multipart/form-data'>";
            echo "<label for='image_input'>Image</label>";
            echo "<input type='file' name='image_input' id='image_input' accept='image/*' required>";
            echo "<button type='submit' name='upload'>Envoyer</button>";
        echo "</form>";
    echo "</body>";
    echo "</html>";
} else { 
    echo "<h2>Image uploadée avec succès !</h2>";
    echo "<p>Voici l'image que vous avez uploadée :</p>";
    echo "<img src='../uploads/" . htmlspecialchars($_SESSION['image']) . "' alt='Image uploadée' style='max-width: 300px; height: auto;'>";
}
?>