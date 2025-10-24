<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/header.php'; 
?>
<head>
    <meta charset="UTF-8">
      <style>
          body {
              font-family: Arial, sans-serif;
              background-color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#121212' : '#f5f5f5' ?>;
              color: <?= ($_SESSION['theme'] ?? 'light') === 'dark' ? '#fff' : '#000' ?>;
              transition: all 0.3s ease;
          }
    </style>
</head>
<body>
    <h2><?= $t['upload_title'] ?? 'Upload an Image' ?></h2>

    <!-- Classic Upload Form -->
    <form action="../control/images_control.php" method="post" enctype="multipart/form-data">
        <label for="image_input"><?= $t['image_label'] ?? 'Image' ?></label>
        <input type="file" name="image_input" id="image_input" accept="image/*" required>
        <button type="submit" name="upload"><?= $t['upload_button'] ?? 'Upload' ?></button>
    </form>

    <h3><?= $t['drag_title'] ?? 'Drag and drop a PNG file below' ?></h3>

    <div id="zone" style="width:300px;height:150px;border:2px dashed gray;text-align:center;line-height:150px;border-radius:10px;">
      <?= $t['drag_here'] ?? 'Drop your PNG file here' ?>
    </div>

    <div id="message"></div>
    <div id="preview"></div>

    <script>
    const zone = document.getElementById("zone");
    const message = document.getElementById("message");
    const preview = document.getElementById("preview");

    zone.addEventListener("dragover", (event) => {
      event.preventDefault(); // allow drop
    });

    zone.addEventListener("drop", (event) => {
      event.preventDefault();

      const files = event.dataTransfer.files;

      if (files.length === 0) {
        message.textContent = "<?= $t['no_file_detected'] ?? 'No file detected.' ?>";
        preview.innerHTML = "";
        return;
      }

      if (files.length > 1) {
        message.textContent = "<?= $t['only_one_file'] ?? 'Please drop only one file at a time.' ?>";
        preview.innerHTML = "";
        return;
      }

      const file = files[0];

      if (file.type !== "image/png") {
        message.textContent = "<?= $t['png_only'] ?? 'Error: Only PNG format is allowed.' ?>";
        preview.innerHTML = "";
        return;
      }

      message.textContent = "<?= $t['valid_png'] ?? 'Valid PNG file!' ?>";
      preview.innerHTML = "";

      const img = document.createElement("img");
      img.src = URL.createObjectURL(file);
      img.style.maxWidth = "200px";
      img.style.display = "block";
      preview.appendChild(img);

      // AJAX upload for drag & drop
      const formData = new FormData();
      formData.append("image_input", file);
      formData.append("upload", true);

      fetch("../control/images_control.php", {
        method: "POST",
        body: formData
      })
      .then(response => response.text())
      .then(data => {
        message.textContent += " | " + data;
      })
      .catch(err => {
        message.textContent = "<?= $t['upload_error'] ?? 'Error during upload:' ?> " + err;
      });
    });
    </script>
</body>
</html>
