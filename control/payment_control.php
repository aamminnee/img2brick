<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../models/commande_models.php';
require_once __DIR__ . '/../models/cord_banquaire_models.php';
require_once __DIR__ . '/../models/mosaic_models.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dotenv\Dotenv;

// load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

class PaymentController {
    private $commande_model;
    private $cord_banquaire_model;
    private $mosaic_model;

    public function __construct() {
        $this->commande_model = new CommandeModel();
        $this->cord_banquaire_model = new CordBanquaireModel();
        $this->mosaic_model = new MosaicModel();
        $this->mail = new PHPMailer(true);

         // determine language for translation, default fr
        $lang = $_SESSION['lang'] ?? 'fr';
        require_once __DIR__ . '/../models/translation_models.php';
        $translation_model = new TranslationModel();
        $this->translations = $translation_model->getTranslations($lang);
    }

    public function PaymentForm() {
        // check if user is logged in and validated
        if (!isset($_SESSION['user_id']) || ($_SESSION['status'] ?? '') !== 'valide') {
            echo $t['access_denied'] ?? "Access denied.";
            exit;
        }

        // check if a mosaic was selected
        if (empty($_SESSION['selected_mosaic'])) {
            echo $t['no_mosaic_selected'] ?? "No mosaic selected.";
            exit;
        }

        // retrieve form data
        $address = $_POST['address'];
        $postal = $_POST['postal'];
        $city = $_POST['city'];
        $country = $_POST['country'];
        $phone = $_POST['phone'];
        $card_number = $_POST['card_number'];
        $expiry = $_POST['expiry'];
        $cvc = $_POST['cvc'];

        $user_id = $_SESSION['user_id'];
        $selected_mosaic = $_SESSION['selected_mosaic'];
        $mosaic_id = $_SESSION['mosaic_id'];

        // determine price based on mosaic type
        $prix = match($selected_mosaic) {
            'blue' => 19.99,
            'red'  => 21.49,
            'bw'   => 14.99,
            default => 0
        };

        // save bank details
        $cord_id = $this->cord_banquaire_model->insertCordBanquaire($card_number, $expiry, $cvc);
        if (!$cord_id) {
            echo $t['bank_info_error'] ?? "Error while saving bank details.";
            return;
        }

        // save order
        $id_commande = $this->commande_model->saveCommande(
            $user_id,
            $mosaic_id,
            $address,
            $postal,
            $city,
            $country,
            $phone,
            $prix,
            $cord_id
        );

        if ($id_commande) {
            $_SESSION['order_id'] = $id_commande;
            $_SESSION['address'] = $address;
            $_SESSION['postal'] = $postal;
            $_SESSION['city'] = $city;
            $_SESSION['country'] = $country;
            $_SESSION['price'] = $prix;
            $this->sendMailCommande($_SESSION['email']);

            include __DIR__ . '/../views/confirm_views.php';
        } else {
            echo $t['order_save_error'] ?? "Error while saving order.";
        }
    }

    // helper for translations
    private function t($key, $default = '') {
        return $this->translations[$key] ?? $default;
    }

    private function sendMailCommande($user_email) {
    // get user info
    $lang = $_SESSION['lang'] ?? 'fr';
    $username = $_SESSION['username'] ?? '';
    
    // commande details from session
    $order_id = $_SESSION['order_id'] ?? '-';
    $address  = $_SESSION['address'] ?? '-';
    $postal   = $_SESSION['postal'] ?? '-';
    $city     = $_SESSION['city'] ?? '-';
    $country  = $_SESSION['country'] ?? '-';
    $price    = $_SESSION['price'] ?? '-';

    // traductions
    $subject = $this->t('order_summary_subject', 'Résumé de votre commande', $lang);
    $body_intro = $this->t('order_summary_intro', 'Bonjour, voici le récapitulatif de votre commande.', $lang);
    $body_address = $this->t('order_summary_address', 'Adresse de livraison', $lang);
    $body_order_id = $this->t('order_summary_order_id', 'Numéro de commande', $lang);
    $body_price = $this->t('order_summary_price', 'Montant payé', $lang);
    $body_footer = $this->t('order_summary_footer', 'Merci pour votre commande !', $lang);

    $body = "
    <p>{$body_intro} <strong>{$username}</strong>,</p>
    <p><strong>{$body_order_id}:</strong> {$order_id}</p>
    <p><strong>{$body_address}:</strong><br>
        {$address}<br>
        {$postal} {$city}<br>
        {$country}
    </p>
    <p><strong>{$body_price}:</strong> {$price} €</p>
    <p>{$body_footer}</p>
    ";

    try {
        $this->mail->isSMTP();
        $this->mail->Host       = $_ENV['MAILJET_HOST'];
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = $_ENV['MAILJET_USERNAME'];
        $this->mail->Password   = $_ENV['MAILJET_PASSWORD'];
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = $_ENV['MAILJET_PORT'];
        $this->mail->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
        $this->mail->addAddress($user_email);
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body    = $body;
        $this->mail->send();
    } catch (Exception $e) {
        echo $this->t('mail_error', "Erreur lors de l'envoi de l'email : ", $lang) . $this->mail->ErrorInfo;
    }
}

}

// --- EXECUTION ---
$controller = new PaymentController();

if (isset($_POST['address'], $_POST['postal'], $_POST['city'], $_POST['country'], $_POST['phone'], $_POST['card_number'], $_POST['expiry'], $_POST['cvc'])) {
    $controller->PaymentForm();
} else {
    echo $t['no_action_detected'] ?? "No action detected.";
}
