<?php
session_start();
require_once __DIR__ . '/../models/images_models.php';

class ImagesController {
    private $images_model;

    public function __construct() {
        $this->images_model = new ImagesModel();
    }

    public function uploadImage() {
        if (!isset($_SESSION['user_id'])) {
            echo "Vous devez être connecté.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_input'])) {
            $image = $_FILES['image_input'];
            $uniqueName = $this->generateImageName($image);
            if ($uniqueName) {
                $this->saveImage($image, $uniqueName);
            } else {
                echo "Format supporté : PNG uniquement.";
            }
        }
    }

    private function generateImageName($image) {
        $extension = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
        if ($extension === 'png') {
            return uniqid('img_', true) . '.png';
        }
        return null;
    }

    private function saveImage($image, $uniqueName) {
        $uploadDir = __DIR__ . '/../uploads/';
        $uploadPath = $uploadDir . $uniqueName;

        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            $this->images_model->saveImageName($uniqueName, $_SESSION['user_id']);
            echo "Image uploadée avec succès !";
        } else {
            echo "Erreur lors de l'upload.";
        }
    }
}

// Contrôleur
$controller = new ImagesController();
if (isset($_POST['upload']) || isset($_FILES['image_input'])) {
    $controller->uploadImage();
}
