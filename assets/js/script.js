// assets/js/script.js
jQuery(document).ready(function($) {
    $('#bonza-booking-form').on('submit', function(e) {
        // Basic client-side validation
        var name = $('#bonza_name').val().trim();
        var address = $('#bonza_address').val().trim();
        var distance = parseFloat($('#bonza_distance').val());
        var rooms = parseInt($('#bonza_rooms').val());

        if (!name || !address || isNaN(distance) || distance <= 0 || isNaN(rooms) || rooms < 1 || rooms > 5) {
            alert('Please fill in all fields correctly. Distance must be positive, and rooms must be between 1 and 5.');
            e.preventDefault();
        }
    });
});