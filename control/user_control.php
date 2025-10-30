<?php
session_start();

require_once __DIR__ . '/../models/users_models.php';
require_once __DIR__ . '/../models/tokens_models.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../'); 
$dotenv->load();

class UserController {
    private $user_model;
    private $token_model;
    private $mail;
    private $translations;

    public function __construct() {
        $this->user_model = new UsersModel();
        $this->token_model = new TokensModel();
        $this->mail = new PHPMailer(true);
        $this->token_model->deleteToken();

        // determine language for translation, default fr
        $lang = $_SESSION['lang'] ?? 'fr';
        require_once __DIR__ . '/../models/translation_models.php';
        $translation_model = new TranslationModel();
        $this->translations = $translation_model->getTranslations($lang);
    }

    // helper for translations
    private function t($key, $default='') {
        return $this->translations[$key] ?? $default;
    }

    // login action
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
                // display translated error
                $message = $this->t('login_error', "Incorrect username or password.");
                include __DIR__ . '/../views/login_views.php';
            }
        } else {
            include __DIR__ . '/../views/login_views.php';
        }
    }

    // register action
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['username'], $_POST['password'])) {
            $email = $_POST['email'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            if ($this->user_model->addUser($email, $username, $password)) {
                // get the newly added user
                $user = $this->user_model->getUserByUsername($username);
                $token = $this->token_model->generateToken($user['id_user'], "validation");
                $this->sendVerificationEmail($email, $token);
                header("Location: ../views/verify_views.php");
                exit;
            } else {
                $message = $this->t('register_error', "Registration failed, please try again.");
                include __DIR__ . '/../views/register_views.php';
            }
        } else {
            include __DIR__ . '/../views/register_views.php';
        }
    }

    // reset password form
    public function resetPasswordForm() {
        if (isset($_POST['reset_password'], $_POST['password'], $_POST['password_confirm'])) {
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            if ($password === $password_confirm) {
                $this->user_model->setPassword($_SESSION['user_id'], $password);
                $message = $this->t('password_reset_success', "Password reset successfully.");
                include __DIR__ . '/../views/images_views.php';
            } else {
                $message = $this->t('password_mismatch', "Passwords do not match.");
                include __DIR__ . '/../views/reset_password_views.php';
            }
        } else {
            include __DIR__ . '/../views/reset_password_views.php';
        }
    }

    // send reset password email
    public function resetPassword() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/login_views.php");
            exit;
        }
        $token = $this->token_model->generateToken($_SESSION['user_id'], "reinitialisation");
        $this->sendVerificationEmail($_SESSION['email'], $token);
        include __DIR__ . '/../views/verify_views.php';
    }

    // send validation email
    public function validateEmail() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../views/login_views.php");
            exit;
        }
        $token = $this->token_model->generateToken($_SESSION['user_id'], "validation");
        $this->sendVerificationEmail($_SESSION['email'], $token);
        include __DIR__ . '/../views/verify_views.php';
    }

    // token verification
    public function tokenForm() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'])) {
            $token = $_POST['token'];
            $token_data = $this->token_model->verifyToken($token);

            if ($token_data) {
                $this->token_model->deleteToken();

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
                $message = $this->t('token_invalid', "Invalid or expired code.");
                include __DIR__ . '/../views/verify_views.php';
            }
        } else {
            include __DIR__ . '/../views/verify_views.php';
        }
    }

    // send verification email
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
            $this->mail->Subject = $this->t('verification_code_subject', "Verification code");
            $this->mail->Body    = $this->t('verification_code_body', "Your verification code is: <b>$token</b>");
            $this->mail->send();
        } catch (Exception $e) {
            echo $this->t('mail_error', "Error sending email: ") . $this->mail->ErrorInfo;
        }
    }

    // enable/disable 2FA
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
            $message = $this->t('2fa_enabled', "Two-factor authentication enabled.");
        } elseif ($action === 'disable') {
            $this->user_model->setModeById($id_user, null);
            $_SESSION['mode'] = null;
            $message = $this->t('2fa_disabled', "Two-factor authentication disabled.");
        } else {
            $message = $this->t('invalid_request', "Invalid request.");
        }
        include __DIR__ . '/../views/setting_views.php';
    }

    // logout
    public function logout() {
        session_unset();
        session_destroy();
        header("Location: ../views/images_views.php"); 
        exit;
    }
}

// --- EXECUTION ---
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
