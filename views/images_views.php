<?php include __DIR__ . '/header.php'; ?>

<h2>Uploader une image</h2>
<form action="../control/images_control.php" method="post" enctype="multipart/form-data">
    <label for="image_input">Image</label>
    <input type="file" name="image_input" id="image_input" accept="image/*" required>
    <button type="submit" name="upload">Envoyer</button>
</form>
</body>
</html>