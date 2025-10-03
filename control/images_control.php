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
            $identifantImage = $this->generateImageName($image);
            if ($identifantImage === null) {
                header("Location: ../views/images_views.php");
                exit;
            }
            $this->saveImage($image, $identifantImage);
        }
    }

    private function generateImageName($image) {
        $uniqueName = null;
        $extension = pathinfo($image['name'], PATHINFO_EXTENSION); // Récupérer l'extension du fichier
        if ($extension === 'png') {
            $uniqueName = uniqid('img_', true) . '.png';  // Générer un nom unique
        } else {
            echo "<p> Format supporté : png uniquement. </p>";
        }
        return $uniqueName;
    }

    private function saveImage($image, $uniqueName) {
    // Chemin complet où stocker l'image
    $uploadDir = __DIR__ . '/../uploads/';  // remonte d'un dossier vers uploads/
    $uploadPath = $uploadDir . $uniqueName;
    if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
        echo "Image uploadée avec succès !";
        $this->images_model->saveImageName($uniqueName, $_SESSION['user_id']);
        $_SESSION['image'] = $uniqueName; 
    } else {
        echo "Erreur lors de l'upload.";
    }
}
 
}


$controller = new ImagesController();
if (isset($_POST['upload'])) {
    $controller->uploadImage();
}
