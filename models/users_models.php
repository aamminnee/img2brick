<?php
class UsersModel {
    private $conn;

    public function __construct() {
        $this->conn = mysqli_connect("localhost", "root", "", "img2brick");
        if (!$this->conn) {
            die("Erreur de connexion : " . mysqli_connect_error());
        }
    }

    // get user by username
    public function getUserByUsername($username) {
        $request = mysqli_prepare($this->conn, "SELECT id_user, mdp FROM users WHERE username = ?");
        mysqli_stmt_bind_param($request, "s", $username);
        mysqli_stmt_execute($request);
        $res = mysqli_stmt_get_result($request);
        return $res->fetch_assoc();
    }

    // get user status by id
    public function getStatusById($id_user) {
        $request = mysqli_prepare($this->conn, "SELECT etat FROM users WHERE id_user = ?");
        mysqli_stmt_bind_param($request, "i", $id_user);
        mysqli_stmt_execute($request);
        $res = mysqli_stmt_get_result($request);
        return $res->fetch_assoc();
    }

    // add user 
    public function addUser($email, $username, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $request = mysqli_prepare($this->conn, "INSERT INTO users (email, username, etat, mdp) VALUES (?, ?, 'invalide', ?)");
        mysqli_stmt_bind_param($request, "sss", $email, $username, $hashed);
        return mysqli_stmt_execute($request);
    }

    // activate user
    public function activateUser($id_user) {
        $request = mysqli_prepare($this->conn, "UPDATE users SET etat = 'valide' WHERE id_user = ?");
        mysqli_stmt_bind_param($request, "i", $id_user);
        return mysqli_stmt_execute($request);
    }

    public function resetPassword($id_user, $password) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $request = mysqli_prepare($this->conn, "UPDATE users SET mdp = ? WHERE id_user = ?");
        mysqli_stmt_bind_param($request, "si", $hashed, $id_user);
        return mysqli_stmt_execute($request);
    }
}
