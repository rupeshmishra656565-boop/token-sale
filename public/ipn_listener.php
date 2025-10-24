<?php
// ==============================================================================
// NOWPAYMENTS IPN (Instant Payment Notification) Listener
// ==============================================================================
namespace PublicArea;

// Set ROOT_DIR without starting the session
define('ROOT_DIR', dirname(__DIR__));

// We need the config for the secret key
require_once(ROOT_DIR . '/config/config.php');
// We need the Database and AuthController to handle the logic
require_once(ROOT_DIR . '/core/Database.php');
require_once(ROOT_DIR . '/controllers/AuthController.php');

use Controllers\AuthController;

// Instantiate AuthController WITHOUT starting a session
// This is a server-to-server request, not a user request
$authController = new AuthController(false);

// Handle the IPN request
$authController->handleNowPaymentsIPN();

?>