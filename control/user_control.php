<?php
session_start();
require_once __DIR__ . '/../models/users_models.php';

class UserController {
    private $user_model;

    public function __construct() {
        $this->user_model = new UsersModel();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->user_model->getUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../views/images_views.php");
                exit;
            } else {
                $message = "Nom d'utilisateur ou mot de passe incorrect";
                include __DIR__ . '/../views/user_views.php';
            }
        } elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
            $this->logout();
        } else {
            include __DIR__ . '/../views/user_views.php';
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/images_views.php");
        exit;
    }
}

$controller = new UserController();
$controller->login();
