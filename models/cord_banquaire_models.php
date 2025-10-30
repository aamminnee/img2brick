<?php
class CordBanquaireModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }


    public function insertCordBanquaire($card_number, $expiry, $cvc) {
        $sql = "INSERT INTO cord_banquaire (card_number, expiry, cvc) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            die("Erreur de préparation : " . mysqli_error($this->conn));
        }
        mysqli_stmt_bind_param($stmt, "sss", $card_number, $expiry, $cvc);
        $success = mysqli_stmt_execute($stmt);
        if ($success) {
            $insertedId = mysqli_insert_id($this->conn); 
            mysqli_stmt_close($stmt);
            return $insertedId; 
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    }



    public function deleteCordBanquaire($id_cord) {
        $sql = "DELETE FROM cord_banquaire WHERE id_cord = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_cord);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    public function __destruct() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
}
