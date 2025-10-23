<?php

class Langue_Model {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function getTranslations($lang) {
        $sql = "SELECT key_name, texte FROM translations WHERE lang = ?";
        $stmt = $this->conn->prepare($sql);
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
