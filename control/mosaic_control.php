<?php
session_start();
require_once __DIR__ . '/../models/mosaic_models.php';

class MosaicController {
    private $mosaic_model;

    public function __construct() {
        $this->mosaic_model = new MosaicModel();
    }

    public function validateChoice() {
        // check if user is logged in and validated
        if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
            echo $t['access_denied'] ?? "Access denied.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choice'])) {
            $choice = $_POST['choice'];
            $user_id = $_SESSION['user_id'];
            $image_name = $_SESSION['last_image'] ?? null;
            $id_image = $_SESSION['last_image_id'] ?? null;

            // check if image exists in session
            if (!$image_name || !$id_image) {
                echo $t['image_not_found'] ?? "Error: image not found in session.";
                exit;
            }

            // create mosaic file
            $destDir = __DIR__ . '/../mosaic_data/';
            if (!is_dir($destDir)) mkdir($destDir, 0777, true);

            $ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $baseName = pathinfo($image_name, PATHINFO_FILENAME);
            $destName = $choice . "_" . $baseName . "." . $ext;
            $srcPath = __DIR__ . '/../uploads/' . $image_name;
            $destPath = $destDir . $destName;

            if (!copy($srcPath, $destPath)) {
                echo $t['mosaic_creation_error'] ?? "Error while creating mosaic file.";
                exit;
            }

            // save mosaic in database
            $result = $this->mosaic_model->saveMosaicName($destName, $user_id, $id_image);
            if ($result) {
                $_SESSION['mosaic_id'] = $this->mosaic_model->getMosaicIdByName($destName);
                $_SESSION['selected_mosaic'] = $choice;
                include __DIR__ . '/../views/payment_views.php';
                exit;
            } else {
                echo "<p>" . ($t['db_save_error'] ?? "An error occurred while saving in database.") . "</p>";
            }
        } else {
            echo $t['no_selection_received'] ?? "No selection received.";
        }
    }
}

$controller = new MosaicController();

if (isset($_POST['choice'])) {
    $controller->validateChoice();
} else {
    echo $t['no_action_detected'] ?? "No action detected.";
}
