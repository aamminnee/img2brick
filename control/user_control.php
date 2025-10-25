<?php

session_start();

require_once __DIR__ . '/../models/users_models.php';
require_once __DIR__ . '/../models/tokens_models.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'user_control.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

class UserController {
    private $user_model;
    private $token_model;
    private $mail;

    public function __construct() {
        $this->user_model = new UsersModel();
        $this->token_model = new TokensModel();
        $this->mail = new PHPMailer(true);
        $this->token_model->deleteToken();
    }

    // Login
    public function login() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_regenerate_id(true);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username']) && !empty($_POST['password'])) {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $user = $this->user_model->getUserByUsername($username);
            if ($user && password_verify($password, $user['mdp'])) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id']  = $user['id_user'];
                $_SESSION['email']    = $this->user_model->getEmailById($user['id_user'])['email'];
                $_SESSION['status']   = $this->user_model->getStatusById($user['id_user'])['etat'];
                $_SESSION['mode'] = $this->user_model->getModeById($user['id_user']);
                if ($_SESSION['mode'] === '2FA') {
                    $token = $this->token_model->generateToken($user['id_user'], "2FA");
                    $this->sendVerificationEmail($_SESSION['email'], $token);
                    include __DIR__ . '/../views/verify_views.php';
                    return;
                }
                header("Location: ../views/images_views.php");
                exit;
            } else {
                $message = "Nom d'utilisateur ou mot de passe incorrect.";
                include __DIR__ . '/../views/login_views.php';
            }
        } else {
            include __DIR__ . '/../views/login_views.php';
        }
    }

    // Register
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['username'], $_POST['password'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];
            if ($this->user_model->addUser($email, $username, $password)) {
                // Récupérer l'utilisateur ajouté
                $user = $this->user_model->getUserByUsername($username);
                $token = $this->token_model->generateToken($user['id_user'], "validation");
                $this->sendVerificationEmail($email, $token);
                // Rediriger vers la page de vérification
                header("Location: ../views/verify_views.php");
                exit;
            } else {
                $message = "Échec de l'inscription, veuillez réessayer.";
                include __DIR__ . '/../views/register_views.php';
            }
        } else {
            include __DIR__ . '/../views/register_views.php';
        }
    }

    public function resetPasswordForm() {
        if (isset($_POST['reset_password']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
            if ($password === $password_confirm) {
                $this->user_model->setPassword($_SESSION['user_id'], $password);
                $message = "Mot de passe réinitialisé avec succès.";
                include __DIR__ . '/../views/images_views.php';
            } else {
                $message = "Les mots de passe ne correspondent pas.";
                include __DIR__ . '/../views/reset_password_views.php';
            }
        } else {
            include __DIR__ . '/../views/reset_password_views.php';
        }
    }

    public function resetPassword() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/login_views.php");
            exit;
        }
        $token = $this->token_model->generateToken($_SESSION['user_id'], "reinitialisation");
        $this->sendVerificationEmail($_SESSION['email'], $token);
        include __DIR__ . '/../views/verify_views.php';
    }

    public function validateEmail() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/login_views.php");
            exit;
        }
        $token = $this->token_model->generateToken($_SESSION['user_id'], "validation");
        $this->sendVerificationEmail($_SESSION['email'], $token);
        include __DIR__ . '/../views/verify_views.php';
    }

    public function tokenForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
            $token = $_POST['token'];
            $token_data = $this->token_model->verifyToken($token);
            if ($token_data) {
                // Token is valid, delete it
                $this->token_model->deleteToken();
                // Redirect to login or dashboard
                if ($token_data['types'] === 'validation') {
                    $this->user_model->activateUser($token_data['id_user']);
                    $_SESSION['status'] = $this->user_model->getStatusById($_SESSION['user_id'])['etat'];
                    header("Location: ../views/images_views.php");
                    exit;
                } elseif ($token_data['types'] === 'reinitialisation') {
                    header("Location: ../views/reset_password_views.php");
                    exit;
                } elseif ($token_data['types'] === '2FA') {
                    header("Location: ../views/images_views.php");
                    exit;
                }
            } else {
                $message = "Code invalide ou expiré.";
                include __DIR__ . '/../views/verify_views.php';
            }
        } else {
            include __DIR__ . '/../views/verify_views.php';
        }
    }

    private function sendVerificationEmail($email, $token) {
        try {
            $this->mail->isSMTP();
            $this->mail->Host       = $_ENV['MAILJET_HOST'];
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $_ENV['MAILJET_USERNAME'];
            $this->mail->Password   = $_ENV['MAILJET_PASSWORD'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = $_ENV['MAILJET_PORT'];
            $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $this->mail->addAddress($email);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Code de vérification';
            $this->mail->Body    = "Votre code de vérification est : <b>$token</b>";
            $this->mail->send();
        } catch (Exception $e) {
            echo "Erreur lors de l'envoi du mail : {$this->mail->ErrorInfo}";
        }
    }

    public function toggle2FA() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/login_views.php");
            exit;
        }

        $id_user = $_SESSION['user_id'];
        $action = $_POST['mode'] ?? '';

        if ($action === 'enable') {
            $this->user_model->setModeById($id_user, '2FA');
            $_SESSION['mode'] = '2FA';
            $message = "Two-factor authentication enabled.";
        } elseif ($action === 'disable') {
            $this->user_model->setModeById($id_user, null);
            $_SESSION['mode'] = null;
            $message = "Two-factor authentication disabled.";
        } else {
            $message = "Invalid request.";
        }
        include __DIR__ . '/../views/setting_views.php';
    }


    // Logout
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/images_views.php"); 
        exit;
    }
}


$controller = new UserController();

// POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $controller->login();
    } elseif (isset($_POST['register'])) {
        $controller->register();
    } elseif (isset($_POST['reset_password'])) {
        $controller->resetPasswordForm();
    } elseif (isset($_POST['toggle2FA'])) {
        $controller->toggle2FA();
    }
}

// GET actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'tokenForm':
            $controller->tokenForm();
            break;
        case 'logout':
            $controller->logout();
            break;
        case 'resetPassword':
            $controller->resetPassword();
            break;
        case 'validateEmail':
            $controller->validateEmail();
            break;
    }
}
