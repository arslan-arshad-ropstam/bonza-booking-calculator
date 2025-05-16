<?php
/**
 * Handles enqueuing of CSS and JavaScript assets
 */
class Bonza_Assets {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Enqueues CSS and JavaScript
     */
    public function enqueue_assets() {
        // Enqueue CSS
        wp_enqueue_style(
            'bonza-booking-calculator-css',
            plugin_dir_url(__DIR__) . 'assets/css/style.css',
            [],
            '1.0.0'
        );
        // Enqueue JavaScript
        wp_enqueue_script(
            'bonza-booking-calculator-js',
            plugin_dir_url(__DIR__) . 'assets/js/script.js',
            ['jquery'],
            '1.0.0',
            true
        );
    }
}