<?php
class UsersModel {
    private $conn;

    function __construct() {
        $this->conn = mysqli_connect("localhost","root","","img2brick");
        if (!$this->conn) die("Erreur de connexion : " . mysqli_connect_error());
    }

    public function getUserByUsername($username) {
        $stmt = mysqli_prepare($this->conn, "SELECT id, password FROM user WHERE username=?");
        mysqli_stmt_bind_param($stmt,"s",$username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($res);
    }

    public function addUser($email,$username,$password) {
        $hashed = password_hash($password,PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($this->conn,"INSERT INTO user (email, username, password) VALUES (?,?,?)");
        mysqli_stmt_bind_param($stmt,"sss",$email,$username,$hashed);
        return mysqli_stmt_execute($stmt);
    }
}
