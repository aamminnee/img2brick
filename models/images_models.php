<?php
class ImagesModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    // Stocke l'image avec id_user
    public function setImageInput($image_input, $image_output, $user_id) {
        $sql = "INSERT INTO images (image_input, image_output, user_id) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        // BLOB pour image_input
        mysqli_stmt_bind_param($stmt, "bsi", $null, $image_output, $user_id);
        mysqli_stmt_send_long_data($stmt, 0, $image_input);
        mysqli_stmt_execute($stmt);
    }

    // Récupère toutes les images d'un utilisateur
    public function getLastImageByUser($user_id) {
        $sql = "SELECT id, image_input FROM images WHERE user_id = ? ORDER BY id DESC LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result); // retourne un seul résultat
    }
}
