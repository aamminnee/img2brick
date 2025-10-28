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
        if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
            echo json_encode(["status" => "error", "message" => "Accès refusé."]);
            exit;
        }

        $user_id = $_SESSION['user_id'];

        if (!isset($_FILES['cropped_image']) || !isset($_POST['original_name'])) {
            echo json_encode(["status" => "error", "message" => "Paramètres manquants."]);
            exit;
        }

        $originalName = basename($_POST['original_name']);
        $cropped = $_FILES['cropped_image'];

        // Si aucun crop effectué, on renvoie le fichier original
        if ($cropped['size'] === 0) {
            echo json_encode(["status" => "success", "file" => $originalName]);
            exit;
        }

        // Nouveau nom unique
        $ext = pathinfo($cropped['name'], PATHINFO_EXTENSION);
        $newName = uniqid('img_crop_', true) . '.' . $ext;
        $newPath = $this->uploadDir . $newName;

        if (!move_uploaded_file($cropped['tmp_name'], $newPath)) {
            echo json_encode(["status" => "error", "message" => "Erreur lors de l'enregistrement du recadrage."]);
            exit;
        }

        // Supprimer l’ancienne image
        $this->model->deleteImageFile($originalName);

        // Mettre à jour la base
        $this->model->updateImage($originalName, $newName, $user_id);

        echo json_encode(["status" => "success", "file" => $newName]);
        exit;
    }
}

$controller = new CropImageController();
$controller->processCrop();
