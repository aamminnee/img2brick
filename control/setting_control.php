<?php
session_start();
require_once __DIR__ . '/../models/translation_models.php';

class SettingController {
    private $translation_model;
    private $translations;

    public function __construct() {
        $this->translation_model = new TranslationModel();

        // determine current language and default to French
        $lang = $_SESSION['lang'] ?? 'fr';

        // gestion language choice
        if (isset($_GET['action']) && $_GET['action'] === 'setLanguage' && isset($_GET['lang'])) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
        }
        // gestion theme choice
        if (isset($_GET['action']) && $_GET['action'] === 'setTheme' && isset($_GET['theme'])) {
            $theme = $_GET['theme'];
            $_SESSION['theme'] = $theme;
        }
        // load translations
        $this->translations = $this->translation_model->getTranslations($lang);
    }

    public function t($key) {
        return $this->translations[$key] ?? $key;
    }

    public function showSetting() {
        $t = $this->translations;
        include __DIR__ . '/../views/setting_views.php';
    }
}

$controller = new SettingController();
$controller->showSetting();
