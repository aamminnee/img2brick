<?php
session_start();
require_once __DIR__ . '/../models/images_models.php';

class ImagesController {
    private $images_model;

    public function __construct() {
        $this->images_model = new ImagesModel();
    }

    // ajoute une image sur le serveur
    public function uploadImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_input'])) { // si le formulaire est soumis
            // Vérifie si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                header("Location: ../views/user_views.php");
                exit;
            }

            $image = $_FILES['image_input'];
            $imageData = file_get_contents($image['tmp_name']);
            $this->images_model->setImageInput($imageData, null, $_SESSION['user_id']);

            echo "<p>Image enregistrée avec succès ✅</p>";

            // Récupère seulement la dernière image de l'utilisateur
            $lastImage = $this->images_model->getLastImageByUser($_SESSION['user_id']);
            if ($lastImage) {
                $base64 = base64_encode($lastImage['image_input']);
                echo "<img src='data:image/png;base64,$base64' style='max-width:200px; margin:5px;'>";
            }
        }
    }

}

$controller = new ImagesController();
if (isset($_POST['upload'])) {
    $controller->uploadImage();
}
