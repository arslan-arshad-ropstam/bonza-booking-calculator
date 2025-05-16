<?php
/**
 * Handles form submission, cost calculation, and email notification
 */
class Bonza_Form_Handler {
    public function __construct() {
        add_action('init', [$this, 'handle_form_submission']);
        // Add filter for multipart email (HTML + plain text)
        add_filter('wp_mail_content_type', [$this, 'set_html_content_type']);
        add_filter('wp_mail', [$this, 'add_plain_text_alternative']);    }

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
     * Sends submission details to admin via HTML email with plain-text alternative
     */
    private function send_admin_email($name, $address, $distance, $rooms, $total_cost) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $site_domain = parse_url(get_site_url(), PHP_URL_HOST);
        $subject = 'New Booking Quote Submission';

        // Store data for plain-text version
        $this->email_data = [
            'name' => $name,
            'address' => $address,
            'distance' => $distance,
            'rooms' => $rooms,
            'total_cost' => $total_cost,
            'submitted_on' => current_time('mysql'),
        ];

        // HTML email template
        $message = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>New Booking Quote</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
                <h1 style="color: #0073aa; text-align: center; margin-bottom: 20px;">New Booking Quote Submission</h1>
                <p style="font-size: 16px; margin-bottom: 20px;">A new booking quote has been submitted. Below are the details:</p>
                <table style="width: 100%; border-collapse: collapse; background-color: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <tr style="background-color: #e3f2fd;">
                        <th style="padding: 12px; text-align: left; color: #0073aa; border-bottom: 1px solid #ddd;">Field</th>
                        <th style="padding: 12px; text-align: left; color: #0073aa; border-bottom: 1px solid #ddd;">Details</th>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">Name</td>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">' . esc_html($name) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">Address</td>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">' . esc_html($address) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">Distance</td>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">' . number_format($distance, 2) . ' km</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">Number of Rooms</td>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">' . esc_html($rooms) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd;">Estimated Cost</td>
                        <td style="padding: 12px; border-bottom: 1px solid #ddd; color: #2e7d32; font-weight: bold;">$' . number_format($total_cost, 2) . '</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px;">Submitted On</td>
                        <td style="padding: 12px;">' . esc_html(current_time('mysql')) . '</td>
                    </tr>
                </table>
                <p style="text-align: center; color: #666; margin-top: 20px; font-size: 14px;">Generated by Bonza Booking Calculator Plugin</p>
            </div>
        </body>
        </html>';

        // Set headers for HTML email
        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . esc_html($site_name) . ' <no-reply@' . $site_domain . '>',
            'Reply-To: ' . $admin_email,
        ];

        // Send email
        wp_mail($admin_email, $subject, $message, $headers);
    }

    /**
     * Sets the content type to HTML
     */
    public function set_html_content_type() {
        return 'multipart/alternative';
    }

    /**
     * Adds a plain-text alternative to the HTML email
     */
    public function add_plain_text_alternative($args) {
        if (!isset($args['content_type']) || $args['content_type'] !== 'multipart/alternative') {
            return $args;
        }

        // Generate plain-text version
        $plain_text = "New Booking Quote Submission\n\n";
        $plain_text .= "A new booking quote has been submitted. Below are the details:\n\n";
        $plain_text .= "Name: " . esc_html($this->email_data['name']) . "\n";
        $plain_text .= "Address: " . esc_html($this->email_data['address']) . "\n";
        $plain_text .= "Distance: " . number_format($this->email_data['distance'], 2) . " km\n";
        $plain_text .= "Number of Rooms: " . esc_html($this->email_data['rooms']) . "\n";
        $plain_text .= "Estimated Cost: $" . number_format($this->email_data['total_cost'], 2) . "\n";
        $plain_text .= "Submitted On: " . esc_html($this->email_data['submitted_on']) . "\n\n";
        $plain_text .= "Generated by Bonza Booking Calculator Plugin\n";

        // Create multipart message
        $boundary = uniqid('wpboundary');
        $args['headers'] = array_map(function($header) use ($boundary) {
            return str_replace('multipart/alternative', 'multipart/alternative; boundary="' . $boundary . '"', $header);
        }, (array) $args['headers']);

        $message = "--" . $boundary . "\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\n";
        $message .= "Content-Transfer-Encoding: 7bit\n\n";
        $message .= $plain_text . "\n";
        $message .= "--" . $boundary . "\n";
        $message .= "Content-Type: text/html; charset=UTF-8\n";
        $message .= "Content-Transfer-Encoding: 7bit\n\n";
        $message .= $args['message'] . "\n";
        $message .= "--" . $boundary . "--";

        $args['message'] = $message;
        return $args;
    }
}