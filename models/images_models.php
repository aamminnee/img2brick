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
    public function saveImageName($image_name, $user_id) {
        $sql = "INSERT INTO images (identifiant, id_user) VALUES (?, ?)";
        $request = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($request, "si", $image_name, $user_id);
        $result = mysqli_stmt_execute($request);
        return $result;
    }

    // Récupère toutes les images d'un utilisateur
    public function getLastImageByUser($user_id, $limit = 6) {
        $sql = "SELECT identifiant FROM images WHERE id_user = ? ORDER BY id DESC LIMIT ?";
        $request = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($request, "ii", $user_id, $limit);
        mysqli_stmt_execute($request);
        $result = mysqli_stmt_get_result($request);
        return $result;
    }
}
