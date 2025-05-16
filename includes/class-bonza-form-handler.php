<?php
/**
 * Handles form submission, cost calculation, and email notification
 */
class Bonza_Form_Handler {
    public function __construct() {
        add_action('init', [$this, 'handle_form_submission']);
    }

    /**
     * Processes form submission
     */
    public function handle_form_submission() {
        if (!isset($_POST['bonza_booking_submit']) || !check_admin_referer('bonza_booking_nonce', 'bonza_nonce')) {
            return;
        }

        // Sanitize and validate inputs
        $name = sanitize_text_field($_POST['bonza_name'] ?? '');
        $address = sanitize_textarea_field($_POST['bonza_address'] ?? '');
        $distance = floatval($_POST['bonza_distance'] ?? 0);
        $rooms = intval($_POST['bonza_rooms'] ?? 0);

        // Validate inputs
        if (empty($name) || empty($address) || $distance <= 0 || $rooms < 1 || $rooms > 5) {
            set_transient('bonza_form_error', 'Please fill in all fields correctly. Distance must be positive, and rooms must be between 1 and 5.', 30);
            return;
        }

        // Calculate cost
        $base_fee = 100;
        $cost_per_km = 10;
        $cost_per_room = 50;
        $total_cost = $base_fee + ($distance * $cost_per_km) + ($rooms * $cost_per_room);

        // Prepare thank-you message
        $message = sprintf('Thanks %s! Your estimated booking cost is $%.2f.', esc_html($name), $total_cost);
        set_transient('bonza_form_message', $message, 30);

        // Save to database
        $database = new Bonza_Database();
        $database->save_booking($name, $address, $distance, $rooms, $total_cost);

        // Send email to admin
        $this->send_admin_email($name, $address, $distance, $rooms, $total_cost);
    }

    /**
     * Sends submission details to admin via email
     */
    private function send_admin_email($name, $address, $distance, $rooms, $total_cost) {
        $admin_email = get_option('admin_email');
        $subject = 'New Booking Quote Submission';
        $message = "A new booking quote has been submitted:\n\n";
        $message .= "Name: " . esc_html($name) . "\n";
        $message .= "Address: " . esc_html($address) . "\n";
        $message .= "Distance: " . number_format($distance, 2) . " km\n";
        $message .= "Number of Rooms: " . $rooms . "\n";
        $message .= "Estimated Cost: $" . number_format($total_cost, 2) . "\n";
        $message .= "\nSubmitted on: " . current_time('mysql') . "\n";

        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        wp_mail($admin_email, $subject, $message, $headers);
    }
}