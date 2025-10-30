<?php
class CommandeModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    public function saveCommande($user_id, $mosaic_id, $adresse, $code_postal, $ville, $pays, $telephone, $montant, $id_cord) {
        $sql = "INSERT INTO commande 
                (id_user, id_mosaic, adresse, code_postal, ville, pays, telephone, montant, id_cord) 
                VALUES  (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iissssddi", 
            $user_id, $mosaic_id, $adresse, $code_postal, $ville, $pays, $telephone, $montant, $id_cord
        );
        $result = mysqli_stmt_execute($stmt);
        if ($result) {
            $id_commande = mysqli_insert_id($this->conn);
            mysqli_stmt_close($stmt);
            return $id_commande;
        } else {
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function getCommandeById($commande_id) {
        $sql = "SELECT * FROM commande WHERE id_commande = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $commande_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $commande = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $commande;
    }
}
?>
