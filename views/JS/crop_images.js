// dom elements
const image = document.getElementById('image');
const cropButton = document.getElementById('cropButton');
const message = document.getElementById('message');
const warnings = document.getElementById('warnings');
const aspectSelect = document.getElementById('aspect');
const sizeSelect = document.getElementById('size');

// translation messages from php
// these variables must be injected via PHP in the page that includes this JS
// example in crop_images_views.php:
// <script>const t = <?= json_encode([
//   'large_image_warning' => $t['large_image_warning'] ?? 'The image is very large. It may be resized for better performance.',
//   'no_crop_applied' => $t['no_crop_applied'] ?? 'No crop applied, original image kept.',
//   'crop_success' => $t['crop_success'] ?? 'Image successfully cropped!',
//   'error_prefix' => $t['error_prefix'] ?? 'Error:',
//   'processing' => $t['processing'] ?? 'Processing...'
// ]) ?>;</script>

// initialize cropper.js
let cropper = new Cropper(image, {
    aspectRatio: NaN, // free by default
    viewMode: 1,
    background: false,
    autoCropArea: 1,
});

// check if image is too large
image.onload = () => {
    if (image.naturalWidth > 3000 || image.naturalHeight > 3000) {
        warnings.textContent = t.large_image_warning;
    }
};

// handle aspect ratio change
aspectSelect.addEventListener('change', () => {
    const value = aspectSelect.value === "NaN" ? NaN : eval(aspectSelect.value);
    cropper.setAspectRatio(value);
});

// handle crop button click
cropButton.addEventListener('click', () => {
    message.textContent = t.processing;
    warnings.textContent = "";

    const canvasData = cropper.getCroppedCanvas();
    const originalWidth = image.naturalWidth;
    const originalHeight = image.naturalHeight;

    // if no crop applied
    if (canvasData.width === originalWidth && canvasData.height === originalHeight) {
        message.textContent = t.no_crop_applied;
        window.location.href = "review_images_views.php?img=" + encodeURIComponent(image.dataset.originalName);
        return;
    }

    // otherwise, send cropped image
    canvasData.toBlob(blob => {
        const formData = new FormData();
        formData.append('cropped_image', blob, 'cropped.png');
        formData.append('original_name', image.dataset.originalName);
        formData.append('size', sizeSelect.value); // selected mosaic size

        fetch('../control/crop_images_control.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                message.textContent = t.crop_success;
                window.location.href = "review_images_views.php?img=" + encodeURIComponent(data.file);
            } else {
                message.textContent = t.error_prefix + data.message;
            }
        })
        .catch(err => {
            message.textContent = t.error_prefix + err.message;
        });
    });
});
