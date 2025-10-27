// Elements from the DOM
const image = document.getElementById('image');
const cropButton = document.getElementById('cropButton');
const message = document.getElementById('message');

// Initialize Cropper.js
const cropper = new Cropper(image, {
    aspectRatio: NaN, // free aspect ratio
    viewMode: 1,
    background: false,
});

// Handle crop and upload
cropButton.addEventListener('click', () => {
    message.textContent = "Processing...";

    // Convert the cropped area into a Blob
    cropper.getCroppedCanvas().toBlob(blob => {
        const formData = new FormData();
        formData.append('cropped_image', blob, 'cropped.png');
        formData.append('original_name', image.dataset.originalName);

        fetch('../control/crop_images_control.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                message.textContent = "Cropped successfully!";
                window.location.href = "review_images_views.php?img=" + encodeURIComponent(data.file);
            } else {
                message.textContent = "Error: " + data.message;
            }
        })
        .catch(err => {
            message.textContent = "Error: " + err;
        });
    });
});
