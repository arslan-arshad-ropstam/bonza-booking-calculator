# Bonza Booking Calculator Plugin

A WordPress plugin that provides a booking cost calculator via the `[booking_quote]` shortcode. Users can input their name, address, distance (in km), and number of rooms (1–5) to get an estimated booking cost based on the formula: $100 base + $10 per km + $50 per room. Form submissions are saved to a custom database table.

## Installation

1. Download the plugin as a ZIP file or clone the repository.
2. Upload the `bonza-booking-calculator` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.
4. Add the `[booking_quote]` shortcode to any page or post to display the booking form.

## Features

- **Booking Form**: Collects user inputs (Name, Address, Distance, Number of Rooms).
- **Cost Calculation**: Calculates cost based on $100 base + $10/km + $50/room.
- **Result Display**: Shows a thank-you message with the estimated cost.
- **Data Storage**: Saves submissions to a custom database table (`wp_bonza_bookings`).
- **Security**: Includes input sanitization, validation, and nonce verification.
- **Responsive Design**: Basic CSS styling for usability across devices.
- **Modular Code**: Functionality split into separate classes for maintainability.

## Folder Structure

- `bonza-booking-calculator.php`: Main plugin file and initialization.
- `includes/`
  - `class-bonza-shortcode.php`: Handles shortcode and form rendering.
  - `class-bonza-form-handler.php`: Processes form submissions and calculations.
  - `class-bonza-database.php`: Manages database operations.
  - `class-bonza-assets.php`: Enqueues CSS and JavaScript.
- `assets/`
  - `css/style.css`: Styles for the booking form.
  - `js/script.js`: Client-side validation script.
- `README.md`: Plugin documentation.

## Requirements

- WordPress 5.0 or higher.
- PHP 7.4 or higher.
- MySQL 5.7 or higher.

## Usage

1. Place the `[booking_quote]` shortcode in a page or post.
2. Fill out the form and submit to see the estimated cost.
3. Form submissions are stored in the `wp_bonza_bookings` table.

## Notes

- Uses server-side form processing for simplicity.
- Client-side validation prevents invalid submissions.
- Custom database table is created on plugin activation and retained on deactivation.
- Follows WordPress coding standards for compatibility and security.
- Modular structure allows easy extension (e.g., AJAX, email notifications).