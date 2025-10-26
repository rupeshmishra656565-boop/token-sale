<?php
// Configuration File for PITHOS Protocol Token Sale

// --- Database Credentials ---
define('DB_HOST', 'db5018880173.hosting-data.io');
define('DB_USER', 'dbu173646');
define('DB_PASS', 'Rupesh@6508'); // <-- Make sure this is your correct XAMPP MySQL password
define('DB_NAME', 'dbs14896832');

// --- NowPayments Integration ---
define('NOWPAYMENTS_API_KEY', '3ESK0R8-N5X42DC-GT0E6D9-3EY9SG7'); // Use your real key
define('NOWPAYMENTS_IPN_SECRET', 'T2EaD90Viy8oQw8af2swj6RzFKc2m6Yq'); // Use your real secret
define('NOWPAYMENTS_API_URL', 'https://api.nowpayments.io/v1');

// --- Token Constants ---
define('KYC_BONUS', 1000.00);
define('REFERRAL_BONUS', 1000.00);
define('TOKEN_RATE', 1000.00); // 1000 PITH per 1 USD
define('TOKEN_SYMBOL', 'PITH'); // Define token symbol
define('TOKEN_NAME', 'PITHOS'); // Define token name

// --- Application Constants ---
define('SITE_URL', 'http://localhost/TokenSale/public/'); // Keep localhost for local dev, ngrok for testing IPN
define('IPN_URL', SITE_URL . 'ipn_listener.php');
define('APP_NAME', 'PITHOS Protocol');

?>