<?php
session_start();
require_once __DIR__ . '/../models/users_models.php';

class UserController {
    private $user_model;

    public function __construct() {
        $this->user_model = new UsersModel();
    }

    // Login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $user = $this->user_model->getUserByUsername($username);
            if ($user && password_verify($password, $user['mdp'])) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id_user']; 
                header("Location: ../views/images_views.php");
                exit;
            } else {
                $message = "Incorrect username or password";
                include __DIR__ . '/../views/login_views.php';
            }
        } else {
            include __DIR__ . '/../views/login_views.php';
        }
    }

    // Register
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($this->user_model->addUser($email, $username, $password)) {
                header("Location: ../views/login_views.php");
                exit;
            } else {
                $message = "Registration failed. Please try again.";
                include __DIR__ . '/../views/register_views.php';
            }
        } else {
            include __DIR__ . '/../views/register_views.php';
        }
    }

    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        include __DIR__ . '/../views/images_views.php';
        exit;
    }
}
$controller = new UserController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $controller->login();
    } elseif (isset($_POST['register'])) {
        $controller->register();
    }
}


