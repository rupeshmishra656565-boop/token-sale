<?php
// Configuration File for PITHOS Protocol Token Sale

// --- Database Credentials ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'token_sale');

// --- NowPayments Integration ---
define('NOWPAYMENTS_API_KEY', '3ESK0R8-N5X42DC-GT0E6D9-3EY9SG7');
define('NOWPAYMENTS_IPN_SECRET', 'T2EaD90Viy8oQw8af2swj6RzFKc2m6Yq');
define('NOWPAYMENTS_API_URL', 'https://api.nowpayments.io/v1');

// --- Token Constants ---
define('KYC_BONUS', 1000.00);
define('REFERRAL_BONUS', 1000.00);
define('TOKEN_RATE', 1000.00);

// --- Application Constants ---
define('SITE_URL', 'http://localhost/TokenSale/public/'); // Keep localhost for local dev, ngrok for testing IPN
define('IPN_URL', SITE_URL . 'ipn_listener.php');
define('APP_NAME', 'PITHOS Protocol'); // Added for email templates

// --- [NEW] Email Configuration ---
define('SMTP_HOST', 'smtp.gmail.com'); // e.g., smtp.gmail.com or smtp.sendgrid.net
define('SMTP_PORT', 587); // Common ports: 587 (TLS), 465 (SSL)
define('SMTP_USERNAME', 'pithosprotocol@gmail.com'); // Your SMTP login username
define('SMTP_PASSWORD', 'xluv zvdb gvxl memf'); // Your SMTP login password or App Password
define('SMTP_FROM_EMAIL', 'pithosprotocol@gmail.com'); // Email address emails will be sent from
define('SMTP_FROM_NAME', APP_NAME); // Name emails will be sent from
define('SMTP_SECURE', 'tls'); // 'tls' or 'ssl' or '' (none)

?>