<?php
/**
 * Handles the [booking_quote] shortcode and form rendering
 */
class Bonza_Shortcode {
    public function __construct() {
        add_shortcode('booking_quote', [$this, 'render_booking_form']);
    }

    /**
     * Renders the booking form
     */
    public function render_booking_form() {
        ob_start();

        // Get message or error from form handler
        $message = get_transient('bonza_form_message');
        $error = get_transient('bonza_form_error');
        if ($message) {
            delete_transient('bonza_form_message');
        }
        if ($error) {
            delete_transient('bonza_form_error');
        }

        ?>
        <div class="bonza-booking-calculator">
            <?php if ($error): ?>
                <p class="bonza-error"><?php echo esc_html($error); ?></p>
            <?php endif; ?>
            <?php if ($message): ?>
                <p class="bonza-message"><?php echo esc_html($message); ?></p>
            <?php endif; ?>
            <form method="post" id="bonza-booking-form">
                <?php wp_nonce_field('bonza_booking_nonce', 'bonza_nonce'); ?>
                <div class="form-field">
                    <label for="bonza_name">Name</label>
                    <input type="text" id="bonza_name" name="bonza_name" required>
                </div>
                <div class="form-field">
                    <label for="bonza_address">Address</label>
                    <textarea id="bonza_address" name="bonza_address" required></textarea>
                </div>
                <div class="form-field">
                    <label for="bonza_distance">Distance (km)</label>
                    <input type="number" id="bonza_distance" name="bonza_distance" min="0.1" step="0.1" required>
                </div>
                <div class="form-field">
                    <label for="bonza_rooms">Number of Rooms</label>
                    <select id="bonza_rooms" name="bonza_rooms" required>
                        <option value="">Select</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" name="bonza_booking_submit">Get Quote</button>
            </form>
        </div>
        <?php

        return ob_get_clean();
    }
}