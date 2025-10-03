<?php
class UsersModel {
    private $conn;

    function __construct() {
        $this->conn = mysqli_connect("localhost","root","","img2brick");
        if (!$this->conn) die("Erreur de connexion : " . mysqli_connect_error());
    }

    public function getUserByUsername($username) {
        $request = mysqli_prepare($this->conn, "SELECT id, mdp FROM user WHERE username = ?");
        mysqli_stmt_bind_param($request,"s",$username);
        mysqli_stmt_execute($request);
        $res = mysqli_stmt_get_result($request);
        $res->fetch_assoc();
    }

    public function addUser($email,$username,$password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $request = mysqli_prepare($this->conn, "INSERT INTO user (email, username, mdp) VALUES (?,?,?)");
        mysqli_stmt_bind_param($request,"sss",$email,$username,$hashed);
        return mysqli_stmt_execute($request);
    }
}
