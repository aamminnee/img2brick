<?php
class ImagesModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    // Enregistre une nouvelle image liée à un utilisateur
    public function saveImageName($image_name, $user_id) {
        $sql = "INSERT INTO images (identifiant, id_user) VALUES (?, ?)";
        $request = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($request, "si", $image_name, $user_id);
        $result = mysqli_stmt_execute($request);
        return $result;
    }

    // Met à jour l’image d’un utilisateur
    public function updateImage($old_name, $new_name, $user_id) {
        $sql = "UPDATE images SET identifiant = ? WHERE identifiant = ? AND id_user = ?";
        $request = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($request, "ssi", $new_name, $old_name, $user_id);
        return mysqli_stmt_execute($request);
    }

    // Supprime un fichier image du disque
    public function deleteImageFile($file_name) {
        $filePath = __DIR__ . '/../uploads/' . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Récupère la dernière image d’un utilisateur
    public function getLastImageByUser($user_id) {
        $sql = "SELECT identifiant FROM images WHERE id_user = ? ORDER BY id DESC LIMIT 1";
        $request = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($request, "i", $user_id);
        mysqli_stmt_execute($request);
        $result = mysqli_stmt_get_result($request);
        return mysqli_fetch_assoc($result);
    }
}
