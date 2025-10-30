<?php
session_start();
require_once __DIR__ . '/../models/translation_models.php';

class SettingController {
    private $translation_model;
    private $translations;

    public function __construct() {
        $this->translation_model = new TranslationModel();

        // determine current language from session, default to French
        $lang = $_SESSION['lang'] ?? 'fr';

        // handle language change
        if (isset($_GET['action']) && $_GET['action'] === 'setLanguage' && isset($_GET['lang'])) {
            $lang = $_GET['lang'];
            $_SESSION['lang'] = $lang;
        }

        // handle theme change
        if (isset($_GET['action']) && $_GET['action'] === 'setTheme' && isset($_GET['theme'])) {
            $theme = $_GET['theme'];
            $_SESSION['theme'] = $theme;
        }

        // load translations for current language
        $this->translations = $this->translation_model->getTranslations($lang);
    }

    // get translation for a given key, fallback to english text
    public function t($key) {
        return $this->translations[$key] ?? $key;
    }

    // render settings page
    public function showSetting() {
        $t = $this->translations; // pass translations to view
        include __DIR__ . '/../views/setting_views.php';
    }
}

// --- EXECUTION ---
$controller = new SettingController();
$controller->showSetting();
