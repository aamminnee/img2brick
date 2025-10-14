<?php

class TokensModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    // create a token for a user
    public function generateToken($user_id, $type) {
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 minutes'));
        $request = mysqli_prepare($this->conn, "INSERT INTO tokens (id_user, token, types, expires_at) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($request, "isss", $user_id, $token, $type, $expires_at);
        mysqli_stmt_execute($request);
        return $token;
    }

    // check if a token is valid
    public function verifyToken($token) {
        $now = date('Y-m-d H:i:s');
        $request = mysqli_prepare($this->conn, "SELECT * FROM tokens WHERE token = ? AND expires_at > ?");
        mysqli_stmt_bind_param($request, "ss", $token, $now);
        mysqli_stmt_execute($request);
        $res = mysqli_stmt_get_result($request);
        return $res->fetch_assoc();
    }

    // delete a token
    public function deleteToken($id_token) {
        $now = date('Y-m-d H:i:s');
        $request = mysqli_prepare($this->conn, "DELETE FROM tokens WHERE expires_at < ?");
        mysqli_stmt_bind_param($request, "i", $now);
        mysqli_stmt_execute($request);
    }
}
