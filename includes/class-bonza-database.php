<?php
/**
 * Handles database operations for booking data
 */
class Bonza_Database {
    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'bonza_bookings';
    }

    /**
     * Creates the custom database table
     */
    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            address TEXT NOT NULL,
            distance DECIMAL(10,2) NOT NULL,
            rooms INT NOT NULL,
            cost DECIMAL(10,2) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    /**
     * Saves booking data to the database
     */
    public function save_booking($name, $address, $distance, $rooms, $cost) {
        global $wpdb;
        $wpdb->insert(
            $this->table_name,
            [
                'name' => $name,
                'address' => $address,
                'distance' => $distance,
                'rooms' => $rooms,
                'cost' => $cost,
            ],
            ['%s', '%s', '%f', '%d', '%f']
        );
    }
}