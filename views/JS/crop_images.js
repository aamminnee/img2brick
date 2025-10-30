// dom elements
const image = document.getElementById('image');
const cropButton = document.getElementById('cropButton');
const message = document.getElementById('message');
const warnings = document.getElementById('warnings');
const aspectSelect = document.getElementById('aspect');
const sizeSelect = document.getElementById('size');

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
        warnings.textContent = "The image is very large. It may be resized for better performance.";
    }
};

// handle aspect ratio change
aspectSelect.addEventListener('change', () => {
    const value = aspectSelect.value === "NaN" ? NaN : eval(aspectSelect.value);
    cropper.setAspectRatio(value);
});

// handle crop button click
cropButton.addEventListener('click', () => {
    message.textContent = "Processing...";
    warnings.textContent = "";

    const canvasData = cropper.getCroppedCanvas();
    const originalWidth = image.naturalWidth;
    const originalHeight = image.naturalHeight;

    // if no crop applied
    if (canvasData.width === originalWidth && canvasData.height === originalHeight) {
        message.textContent = "No crop applied, original image kept.";
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
                message.textContent = "Image successfully cropped!";
                window.location.href = "review_images_views.php?img=" + encodeURIComponent(data.file);
            } else {
                message.textContent = "Error: " + data.message;
            }
        })
        .catch(err => {
            message.textContent = "Error: " + err.message;
        });
    });
});
