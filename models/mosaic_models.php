<?php
class MosaicModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    public function saveMosaicName($image_name, $user_id, $id_image) {
        $sql = "INSERT INTO mosaique (identifiant, id_user, id_image) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "sii", $image_name, $user_id, $id_image);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    public function getMosaicIdByName($name_mosaic) {
        $sql = "SELECT id_mosaic FROM mosaique WHERE identifiant = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $name_mosaic);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $mosaic = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $mosaic ? $mosaic['id_mosaic'] : null;
    }

    public function getMosaicById($mosaic_id) {
        $sql = "SELECT * FROM mosaique WHERE id_mosaic = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $mosaic_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $mosaic = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $mosaic; // retourne false si non trouvé
    }

    public function getMosaicByIdentifier($identifier) {
        $sql = "SELECT * FROM mosaique WHERE identifiant = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $identifier);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $mosaic = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $mosaic; // retourne false si non trouvé
    }

    public function deleteMosaicFile($file_name) {
        $filePath = __DIR__ . '/../mosaic_data/' . $file_name;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getLastMosaicByUser($user_id, $id_image) {
        $sql = "SELECT identifiant FROM mosaique WHERE id_user = ? AND id_image = ? ORDER BY id_mosaic DESC LIMIT 1";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $id_image);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}
