<?php

/**
 * Cookie-Einwilligungs-Konfiguration
 *
 * Diese Datei enthält alle Konfigurationsoptionen für das Cookie-Einwilligungssystem.
 * Sie ermöglicht die Anpassung des Erscheinungsbilds, des Verhaltens und
 * der datenschutzbezogenen Einstellungen des Cookie-Banners.
 *
 * @package Config
 * @author Muhammad Rabiul
 * @license MIT
 */

return [

    /**
    |--------------------------------------------------------------------------
    | Präfix für die Cookie-Einwilligung
    |--------------------------------------------------------------------------
    | Diese Einstellung bestimmt das Präfix, das für die Cookie-Einwilligung
    | verwendet wird.
    | Der Wert kann über die .env-Datei mit APP_NAME gesteuert werden.
    */
    'cookie_prefix' => env('APP_NAME', 'Laravel_App'),

    /**
     * Cookie-Einwilligungsbanner aktivieren oder deaktivieren
     *
     * @default true
     * @env COOKIE_CONSENT_ENABLED
     */
    'enabled' => env('COOKIE_CONSENT_ENABLED', true),


    /**
    |--------------------------------------------------------------------------
    | Benutzerdefinierte Asset-URL
    |--------------------------------------------------------------------------
    | Optional kann hier eine eigene Basis-URL für die Assets des Pakets
    | angegeben werden.
    | Wenn der Wert null oder leer ist, wird Laravels native asset()-Funktion
    | verwendet.
    |
    | Beispiel:
    | https://ihre-domain.de
    */
    'asset_url' => env('COOKIE_CONSENT_ASSET_URL', null),

    /**
     * Gültigkeitsdauer des Cookie-Einwilligungs-Cookies in Tagen
     *
     * Legt fest, wie lange das Einwilligungs-Cookie im Browser
     * des Benutzers gespeichert wird.
     *
     * @default 365
     * @env COOKIE_CONSENT_LIFETIME
     */
    'cookie_lifetime' => env('COOKIE_CONSENT_LIFETIME', 365),

    /**
     * Gültigkeitsdauer des Ablehnungs-Cookies in Tagen
     *
     * Legt fest, wie lange das Ablehnungs-Cookie gespeichert wird,
     * wenn der Benutzer Cookies ablehnt.
     *
     * @default 7
     * @env COOKIE_REJECT_LIFETIME
     */
    'reject_lifetime' => env('COOKIE_REJECT_LIFETIME', 7),

    /**
     * Layout des Cookie-Einwilligungsbanners
     *
     * Legt fest, wie das Cookie-Banner visuell dargestellt wird.
     *
     * @default 'bar-inline'
     * @env COOKIE_CONSENT_MODAL_LAYOUT
     *
     * @option box        - Kleine schwebende Box
     * @option box-inline - Kleine schwebende Box innerhalb des Layouts
     * @option box-wide   - Größere schwebende Box
     * @option cloud      - Wolkenähnliche schwebende Box
     * @option cloud-inline - Kompakte Box im Cloud-Stil
     * @option bar         - Einfacher Balken oben oder unten
     * @option bar-inline  - Kompakter Balken innerhalb des Layouts
     */
    'consent_modal_layout' => env('COOKIE_CONSENT_MODAL_LAYOUT', 'bar'),

    /**
     * Einstellungsfenster aktivieren
     *
     * Legt fest, ob Benutzer detaillierte Cookie-Einstellungen
     * aufrufen können.
     *
     * @default false
     * @env COOKIE_CONSENT_PREFERENCES_ENABLED
     */
    'preferences_modal_enabled' => env('COOKIE_CONSENT_PREFERENCES_ENABLED', true),

    /**
     * Layout des Einstellungsfensters
     *
     * Legt fest, wie das Fenster für die Cookie-Einstellungen
     * dargestellt wird.
     *
     * @default 'bar'
     * @env COOKIE_CONSENT_PREFERENCES_LAYOUT
     *
     * @option bar - Fenster im Balken-Stil
     * @option box - Fenster im Popup-Stil
     */
    'preferences_modal_layout' => env('COOKIE_CONSENT_PREFERENCES_LAYOUT', 'bar'),

    /**
     * Flip-Animation der Schaltflächen aktivieren
     *
     * Fügt den Einwilligungsschaltflächen einen Flip-Animationseffekt hinzu.
     *
     * @default true
     * @env COOKIE_CONSENT_FLIP_BUTTON
     */
    'flip_button' => env('COOKIE_CONSENT_FLIP_BUTTON', true),

    /**
     * Seiteninteraktion bis zur Einwilligung deaktivieren
     *
     * Wenn aktiviert, muss der Benutzer mit dem Cookie-Banner interagieren,
     * bevor er auf den Seiteninhalt zugreifen kann.
     *
     * @default true
     * @env COOKIE_CONSENT_DISABLE_INTERACTION
     */
    'disable_page_interaction' => env('COOKIE_CONSENT_DISABLE_INTERACTION', true),

    /**
     * Farbschema des Cookie-Banners
     *
     * @default 'default'
     * @env COOKIE_CONSENT_THEME
     *
     * @option default - Standarddesign
     * @option dark    - Dunkles Design
     * @option light   - Helles Design
     * @option custom  - Benutzerdefiniertes Design
     *                  (zusätzliches CSS erforderlich)
     */
    'theme' => env('COOKIE_CONSENT_THEME', 'default'),

    /**
    |--------------------------------------------------------------------------
    | Design-Voreinstellung
    |--------------------------------------------------------------------------
    | basic        - Standardmäßiges neutrales Design
    | modern-blue  - Professionelles blaues Design
    | trust-green  - Datenschutzfreundliches grünes Design
    | soft-neutral - Dezentes hellgraues Design
    | dark         - Dunkles Design
    */
    'theme_preset' => env('COOKIE_CONSENT_THEME_PRESET', 'basic'),

    /**
     * Titel des Cookie-Banners
     *
     * @default "Cookie-Hinweis"
     */
    'cookie_title' => 'Cookie-Hinweis',

    /**
     * Beschreibung des Cookie-Banners
     *
     * @default "Diese Website verwendet Cookies, um Ihr Nutzungserlebnis
     * zu verbessern, den Datenverkehr zu analysieren und Inhalte zu
     * personalisieren. Durch die weitere Nutzung dieser Website stimmen
     * Sie der Verwendung von Cookies zu."
     */
    'cookie_description' => 'Diese Website verwendet Cookies, um Ihr Nutzungserlebnis zu verbessern, den Datenverkehr zu analysieren und Inhalte zu personalisieren. Durch die weitere Nutzung dieser Website stimmen Sie der Verwendung von Cookies zu.',

    /**
     * Text der Schaltfläche zum Akzeptieren aller Cookies
     *
     * @default 'Alle akzeptieren'
     */
    'cookie_accept_btn_text' => 'Alle akzeptieren',

    /**
     * Text der Schaltfläche zum Ablehnen aller Cookies
     *
     * @default 'Alle ablehnen'
     */
    'cookie_reject_btn_text' => 'Alle ablehnen',

    /**
     * Text der Schaltfläche zum Verwalten der Cookie-Einstellungen
     *
     * @default 'Einstellungen verwalten'
     */
    'cookie_preferences_btn_text' => 'Einstellungen verwalten',

    /**
     * Text der Schaltfläche zum Speichern der Cookie-Einstellungen
     *
     * @default 'Einstellungen speichern'
     */
    'cookie_preferences_save_text' => 'Einstellungen speichern',

    /**
     * Titel des Fensters für die Cookie-Einstellungen
     *
     * @default 'Cookie-Einstellungen'
     */
    'cookie_modal_title' => 'Cookie-Einstellungen',

    /**
     * Einleitungstext des Fensters für die Cookie-Einstellungen
     *
     * @default 'Sie können Ihre Cookie-Einstellungen unten anpassen.'
     */
    'cookie_modal_intro' => 'Sie können Ihre Cookie-Einstellungen unten anpassen.',

    /**
     * Konfiguration der Cookie-Kategorien
     *
     * Definiert die verschiedenen Cookie-Arten, die Benutzer verwalten können.
     *
     * @category necessary   - Essenzielle Cookies, die nicht deaktiviert werden können
     * @category analytics   - Cookies für Analyse und Statistik
     * @category marketing   - Cookies für Werbung
     * @category preferences - Cookies zum Speichern von Benutzereinstellungen
     */
    'cookie_categories' => [

        'necessary' => [
            'enabled' => true,
            'locked' => true,
            'title' => 'Essenzielle Cookies',
            'description' => 'Diese Cookies sind für die einwandfreie Funktion der Website erforderlich.',
        ],

        'analytics' => [
            'enabled' => env('COOKIE_CONSENT_ANALYTICS', false),
            'locked' => false,
            'js_action' => 'loadGoogleAnalytics',
            'title' => 'Analyse-Cookies',
            'description' => 'Diese Cookies helfen uns zu verstehen, wie Besucher mit unserer Website interagieren.',
        ],

        'marketing' => [
            'enabled' => env('COOKIE_CONSENT_MARKETING', false),
            'locked' => false,
            'js_action' => 'loadFacebookPixel',
            'title' => 'Marketing-Cookies',
            'description' => 'Diese Cookies werden für Werbe- und Trackingzwecke verwendet.',
        ],

        'preferences' => [
            'enabled' => env('COOKIE_CONSENT_PREFERENCES', false),
            'locked' => false,
            'title' => 'Einstellungs-Cookies',
            'description' => 'Diese Cookies ermöglichen es der Website, sich an Benutzereinstellungen zu erinnern.',
        ],
    ],

    /**
     * Konfiguration der Links zu rechtlichen Dokumenten
     *
     * Links zu rechtlichen Dokumenten, die im Cookie-Banner angezeigt werden.
     *
     * @item text - Angezeigter Text des Links
     * @item link - URL zum jeweiligen Dokument
     */
    'policy_links' => [
        [
            'text' => 'Datenschutzerklärung',
            'link' => env('COOKIE_CONSENT_PRIVACY_POLICY_URL', '') ?? url('privacy-policy')
        ],

        // [
        //     'text' => 'Allgemeine Geschäftsbedingungen',
        //     'link' => env('COOKIE_CONSENT_TERMS_URL', '') ?? url('terms-and-conditions')
        // ],
    ],

];
