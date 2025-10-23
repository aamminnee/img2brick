<?php
class TranslationModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    // Récupère les traductions selon la langue
    public function getTranslations($lang) {
        $query = "SELECT key_name, texte FROM translations WHERE lang = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $lang);
        $stmt->execute();
        $result = $stmt->get_result();

        $translations = [];
        while ($row = $result->fetch_assoc()) {
            $translations[$row['key_name']] = $row['texte'];
        }
        return $translations;
    }
}
