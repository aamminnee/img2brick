<?php
session_start();
header("Content-Type: application/json");
require_once __DIR__ . '/../models/images_models.php';

class CropImageController {
    private $model;
    private $uploadDir;

    public function __construct() {
        $this->model = new ImagesModel();
        $this->uploadDir = __DIR__ . '/../uploads/';
    }

    public function processCrop() {
        // check user session
        if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
            echo json_encode([
                "status" => "error",
                "message" => $t['access_denied'] ?? "Access denied."
            ]);
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // check required parameters
        if (!isset($_FILES['cropped_image']) || !isset($_POST['original_name'])) {
            echo json_encode([
                "status" => "error",
                "message" => $t['missing_parameters'] ?? "Missing parameters."
            ]);
            exit;
        }

        $originalName = basename($_POST['original_name']);
        $cropped = $_FILES['cropped_image'];

        // if no crop (unchanged image)
        if ($cropped['size'] === 0) {
            echo json_encode([
                "status" => "success",
                "file" => $originalName
            ]);
            exit;
        }

        // generate unique file name
        $ext = pathinfo($cropped['name'], PATHINFO_EXTENSION);
        $newName = uniqid('img_crop_', true) . '.' . $ext;
        $newPath = $this->uploadDir . $newName;

        // move cropped file to uploads/
        if (!move_uploaded_file($cropped['tmp_name'], $newPath)) {
            echo json_encode([
                "status" => "error",
                "message" => $t['crop_save_error'] ?? "Error saving cropped image."
            ]);
            exit;
        }

        // delete old file from uploads/
        $this->model->deleteImageFile($originalName);

        // update database with new file name
        $updateResult = $this->model->updateImage($originalName, $newName, $user_id);

        if ($updateResult) {
            echo json_encode([
                "status" => "success",
                "file" => $newName
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => $t['db_update_error'] ?? "Error updating database."
            ]);
        }

        exit;
    }
}

$controller = new CropImageController();
$controller->processCrop();
