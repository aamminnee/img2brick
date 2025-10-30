<?php
class ImagesModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    // Enregistre une nouvelle image (nom + binaire)
    public function saveImageName($image_name, $user_id) {
        $filePath = __DIR__ . '/../uploads/' . $image_name;
        $imageData = null;

        if (file_exists($filePath)) {
            $imageData = file_get_contents($filePath);
        }

        $sql = "INSERT INTO images (identifiant, id_user, image) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);

        // 1er param string, 2e int, 3e blob
        mysqli_stmt_bind_param($stmt, "sib", $image_name, $user_id, $null);
        mysqli_stmt_send_long_data($stmt, 2, $imageData);

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    // Met à jour le nom + le contenu binaire
    public function updateImage($old_name, $new_name, $user_id) {
        $filePath = __DIR__ . '/../uploads/' . $new_name;
        $imageData = null;

        if (file_exists($filePath)) {
            $imageData = file_get_contents($filePath);
        }

        $sql = "UPDATE images SET identifiant = ?, image = ? WHERE identifiant = ? AND id_user = ?";
        $stmt = mysqli_prepare($this->conn, $sql);

        // l'ordre ici est TRES important
        mysqli_stmt_bind_param($stmt, "sbsi", $new_name, $null, $old_name, $user_id);
        mysqli_stmt_send_long_data($stmt, 1, $imageData);

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }

    // Supprime un fichier du disque
    public function deleteImageFile($file_name) {
        $filePath = __DIR__ . '/../uploads/' . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Récupère la dernière image d’un utilisateur
    public function getLastImageByUser($user_id) {
        $sql = "SELECT identifiant FROM images WHERE id_user = ? ORDER BY id DESC LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    // Récupère l'id de la derniere image de l'utilisateur
    public function getLastIdImageByUser($user_id, $name_image) {
        $sql = "SELECT id FROM images WHERE id_user = ? AND identifiant = ? ORDER BY id DESC LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $name_image);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}
