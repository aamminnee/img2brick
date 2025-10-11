<?php 
session_start();

include __DIR__ . '/header.php'; 
?>
  
<h2>Upload an Image</h2>

<!-- Formulaire classique -->
<form action="../control/images_control.php" method="post" enctype="multipart/form-data">
    <label for="image_input">Image</label>
    <input type="file" name="image_input" id="image_input" accept="image/*" required>
    <button type="submit" name="upload">Upload</button>
</form>

<h3>Drag and drop a PNG file below</h3>

<div id="zone" style="width:300px;height:150px;border:2px dashed gray;text-align:center;line-height:150px;border-radius:10px;">
  Drop your PNG file here
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
    message.textContent = "No file detected.";
    preview.innerHTML = "";
    return;
  }

  if (files.length > 1) {
    message.textContent = "Please drop only one file at a time.";
    preview.innerHTML = "";
    return;
  }

  const file = files[0];

  if (file.type !== "image/png") {
    message.textContent = "Error: Only PNG format is allowed.";
    preview.innerHTML = "";
    return;
  }

  message.textContent = "Valid PNG file!";
  preview.innerHTML = "";

  const img = document.createElement("img");
  img.src = URL.createObjectURL(file);
  img.style.maxWidth = "200px";
  img.style.display = "block";
  preview.appendChild(img);

  // Upload via AJAX pour drag & drop
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
    message.textContent = "Erreur lors de l'upload: " + err;
  });
});
</script>

</body>
</html>
