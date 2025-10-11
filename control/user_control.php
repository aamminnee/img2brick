<?php

session_start();

require_once __DIR__ . '/../models/users_models.php';
require_once __DIR__ . '/../models/tokens_models.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload
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

    public function tokenForm() {
        PRINT_R($_SESSION);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
            $token = $_POST['token'];
            $token_data = $this->token_model->verifyToken($token);
            if ($token_data) {
                // Token is valid, delete it
                $this->token_model->deleteToken($token_data['id_token']);
                // Redirect to login or dashboard
                if ($token_data['types'] === 'validation') {
                    $this->user_model->activateUser($token_data['id_user']);
                    header("Location: ../views/login_views.php");
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
            print_r($_ENV);
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
    if (isset($_GET['action']) && $_GET['action'] === 'tokenForm') {
        $controller->tokenForm();
    }
}


