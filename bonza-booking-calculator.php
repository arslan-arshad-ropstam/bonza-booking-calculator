<?php
/*
Plugin Name: Bonza Booking Calculator
Description: A WordPress plugin that provides a booking cost calculator via the [booking_quote] shortcode.
Version: 1.0.0
Author: Ropstam Solutions Pvt. Ltd.
License: GPL-2.0+
*/

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

// Include necessary classes
require_once plugin_dir_path(__FILE__) . 'includes/class-bonza-shortcode.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-bonza-form-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-bonza-database.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-bonza-assets.php';

/**
 * Initialize the plugin
 */
class Bonza_Booking_Calculator {
    public function __construct() {
        // Instantiate classes
        new Bonza_Shortcode();
        new Bonza_Form_Handler();
        new Bonza_Assets();

        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);
    }

    /**
     * Activation hook: Create database table
     */
    public function activate() {
        $database = new Bonza_Database();
        $database->create_table();
    }

    /**
     * Deactivation hook: Placeholder for cleanup (table retained for data persistence)
     */
    public function deactivate() {
        // Optionally drop table or perform cleanup
        // Left empty to retain data, as dropping table may cause data loss
    }
}

// Initialize the plugin
new Bonza_Booking_Calculator();