// DOM elements
const image = document.getElementById('image');
const cropButton = document.getElementById('cropButton');
const message = document.getElementById('message');
const warnings = document.getElementById('warnings');
const aspectSelect = document.getElementById('aspect');
const sizeSelect = document.getElementById('size');

// Initialize Cropper.js
let cropper = new Cropper(image, {
    aspectRatio: NaN, // libre par défaut
    viewMode: 1,
    background: false,
    autoCropArea: 1,
});

// Vérification image très grande
image.onload = () => {
    if (image.naturalWidth > 3000 || image.naturalHeight > 3000) {
        warnings.textContent = "⚠️ L'image est très grande. Elle peut être redimensionnée pour de meilleures performances.";
    }
};

// Gestion du changement de ratio
aspectSelect.addEventListener('change', () => {
    const value = aspectSelect.value === "NaN" ? NaN : eval(aspectSelect.value);
    cropper.setAspectRatio(value);
});

// Gestion du bouton crop
cropButton.addEventListener('click', () => {
    message.textContent = "Traitement en cours...";
    warnings.textContent = "";

    const canvasData = cropper.getCroppedCanvas();
    const originalWidth = image.naturalWidth;
    const originalHeight = image.naturalHeight;

    // Si aucune modification du crop
    if (canvasData.width === originalWidth && canvasData.height === originalHeight) {
        message.textContent = "Aucun recadrage appliqué, image originale conservée.";
        window.location.href = "review_images_views.php?img=" + encodeURIComponent(image.dataset.originalName);
        return;
    }

    // Sinon, envoyer l'image cropée
    canvasData.toBlob(blob => {
        const formData = new FormData();
        formData.append('cropped_image', blob, 'cropped.png');
        formData.append('original_name', image.dataset.originalName);
        formData.append('size', sizeSelect.value); // taille du tableau sélectionnée

        fetch('../control/crop_images_control.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                message.textContent = "Image recadrée avec succès !";
                window.location.href = "review_images_views.php?img=" + encodeURIComponent(data.file);
            } else {
                message.textContent = "Erreur : " + data.message;
            }
        })
        .catch(err => {
            message.textContent = "Erreur : " + err.message;
        });
    });
});
