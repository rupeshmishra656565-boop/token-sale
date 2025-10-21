<?php
// Configuration File for PITHOS Protocol Token Sale

// --- Database Credentials (UPDATE THESE) ---
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // <-- SET YOUR MYSQL PASSWORD
define('DB_NAME', 'token_sale');

// --- NowPayments Integration (UPDATE THESE SECRETS) ---
// IMPORTANT: Get your key from your NowPayments account settings
define('NOWPAYMENTS_API_KEY', 'YOUR_NOWPAYMENTS_API_KEY_HERE'); 
// This should be a strong, random string you set in your NowPayments IPN settings
define('NOWPAYMENTS_IPN_SECRET', 'YOUR_IPN_SECRET_KEY_HERE'); 
define('NOWPAYMENTS_API_URL', 'https://api.nowpayments.io/v1');

// --- Token Constants ---
define('KYC_BONUS', 1000.00);       // Tokens given on sign-up
define('REFERRAL_BONUS', 1000.00);  // Tokens given to the referrer for each new user
define('TOKEN_RATE', 1000.00);      // Base rate: 1 USD = 1000 Tokens

// --- Application Constants ---
// Important: This should point to your public directory
define('SITE_URL', 'http://localhost/TokenSale/public/');

// Set the IPN URL where NowPayments will send payment confirmations
// **CHANGE 'localhost' to your live domain when deployed**
define('IPN_URL', SITE_URL . 'ipn_listener.php'); 


?>
