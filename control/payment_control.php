<?php
session_start();
require_once __DIR__ . '/../models/commande_models.php';
require_once __DIR__ . '/../models/cord_banquaire_models.php';
require_once __DIR__ . '/../models/mosaic_models.php';

class PaymentController {
    private $commande_model;
    private $cord_banquaire_model;
    private $mosaic_model;

    public function __construct() {
        $this->commande_model = new CommandeModel();
        $this->cord_banquaire_model = new CordBanquaireModel();
        $this->mosaic_model = new MosaicModel();
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

            include __DIR__ . '/../views/confirm_views.php';
        } else {
            echo $t['order_save_error'] ?? "Error while saving order.";
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
