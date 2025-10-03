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
            if ($user && password_verify($password, $user['mdp'])) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../views/images_views.php");
                exit;
            } else {
                $message = "Nom d'utilisateur ou mot de passe incorrect";
                include __DIR__ . '/../views/login_views.php';
            }
        } elseif (isset($_GET['action']) && $_GET['action'] == 'logout') {
            $this->logout();
        } else {
            include __DIR__ . '/../views/login_views.php';
        }
    }

    public  function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['username']) && isset($_POST['password'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($this->user_model->addUser($email, $username, $password)) {
                header("Location: ../views/login_views.php");
                exit;
            } else {
                $message = "Erreur lors de l'inscription. Veuillez réessayer.";
                include __DIR__ . '/../views/register_views.php';
            }
        } else {
            include __DIR__ . '/../views/register_views.php';
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/images_views.php");
        exit;
    }
}

$controller = new ImagesController();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $controller->login();
    } elseif ($_POST['action'] === 'register') {
        $controller->register();
    }
}


