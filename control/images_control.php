<?php
session_start();
require_once __DIR__ . '/../models/images_models.php';

class ImagesController {
    private $images_model;

    public function __construct() {
        $this->images_model = new ImagesModel();
    }

    public function uploadImage() {
        header("Content-Type: application/json");

        // check if user is logged in and validated
        if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
            echo json_encode([
                "status" => "error",
                "message" => $t['login_required'] ?? "You must be logged in and validated to upload an image."
            ]);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_input'])) {
            $image = $_FILES['image_input'];
            $allowedTypes = ["image/png", "image/jpeg", "image/webp"];

            // check file type
            if (!in_array($image['type'], $allowedTypes)) {
                echo json_encode([
                    "status" => "error",
                    "message" => $t['unsupported_type'] ?? "Unsupported file type. Allowed: JPG, PNG, WEBP."
                ]);
                return;
            }

            // check uploaded file validity
            if (!is_uploaded_file($image['tmp_name'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => $t['invalid_file'] ?? "Invalid file."
                ]);
                return;
            }

            // check file size
            if ($image['size'] > 2 * 1024 * 1024) {
                echo json_encode([
                    "status" => "error",
                    "message" => $t['file_too_large'] ?? "Image too large (>2MB)."
                ]);
                return;
            }

            // check minimum resolution
            list($w, $h) = getimagesize($image['tmp_name']);
            if ($w < 512 || $h < 512) {
                echo json_encode([
                    "status" => "error",
                    "message" => $t['file_too_small'] ?? "Image too small (min 512x512)."
                ]);
                return;
            }

            // generate unique file name and path
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $uniqueName = uniqid('img_', true) . "." . $ext;
            $uploadDir = __DIR__ . '/../uploads/';
            $uploadPath = $uploadDir . $uniqueName;

            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            // move uploaded file
            if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
                // save in database and get last inserted ID
                $last_id = $this->images_model->saveImageName($uniqueName, $_SESSION['user_id']);

                // store in session for mosaics
                $_SESSION['last_image'] = $uniqueName;
                $_SESSION['last_image_id'] = $this->images_model->getLastIdImageByUser($_SESSION['user_id'], $uniqueName)['id'];

                echo json_encode([
                    "status" => "success",
                    "file" => $uniqueName,
                    "message" => $t['file_ok'] ?? "Image successfully loaded, click Continue."
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => $t['upload_error'] ?? "Error during upload."
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => $t['no_file_selected'] ?? "No image selected."
            ]);
        }
    }
}

$controller = new ImagesController();
if (isset($_FILES['image_input'])) {
    $controller->uploadImage();
} else {
    echo $t['no_action_detected'] ?? "No action detected.";
}
