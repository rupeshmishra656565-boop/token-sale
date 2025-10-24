<?php
// Page to handle password reset link
namespace PublicArea;

define('ROOT_DIR', dirname(__DIR__));

require_once(ROOT_DIR . '/config/config.php');
require_once(ROOT_DIR . '/controllers/AuthController.php'); // Need controller

use Controllers\AuthController;
$authController = new AuthController(true); // Start session if needed

$token = $_GET['token'] ?? null;
$error_message = '';
$success_message = '';
$show_form = false;
$verification = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to reset the password
    $result = $authController->resetPasswordWithToken($_POST);
    if ($result['success']) {
        $success_message = $result['message'];
        $show_form = false; // Hide form on success
    } else {
        $error_message = $result['message'];
        $token = $_POST['token'] ?? null; // Keep token in form if error occurs
        if ($token) {
            $verification = $authController->verifyResetToken($token); // Re-verify token
            $show_form = ($verification !== null && $verification !== false); // Show form only if token still valid
        }
    }

} else {
    // Handle page load (GET request)
    if ($token) {
        $verification = $authController->verifyResetToken($token);
        if ($verification) {
            $show_form = true; // Token is valid, show the form
        } else {
            $error_message = "Invalid or expired password reset link.";
        }
    } else {
        $error_message = "No reset token provided.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
         body { font-family: 'Inter', sans-serif; background-color: #0a0a0f; color: #ffffff;}
         .card { background-color: rgba(30, 30, 45, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(153, 69, 255, 0.2); }
         .input-field { background: rgba(0, 0, 0, 0.4); border: 2px solid rgba(153, 69, 255, 0.3); }
         .input-field:focus { border-color: #9945FF; box-shadow: 0 0 10px rgba(153, 69, 255, 0.3); }
         .btn-primary { background: linear-gradient(135deg, #14F195, #0ea770); color: #000; }
         .spinner { /* ... same spinner style ... */ display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="card p-8 rounded-lg shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-6">Reset Your Password</h2>

        <?php if ($success_message): ?>
            <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 p-4 rounded-md text-center">
                <?php echo htmlspecialchars($success_message); ?>
                <p class="mt-2"><a href="index.php" class="font-semibold underline hover:text-white">Click here to Login</a></p>
            </div>
        <?php elseif ($error_message && !$show_form): ?>
             <div class="bg-red-500/20 border border-red-500/30 text-red-300 p-4 rounded-md text-center">
                <?php echo htmlspecialchars($error_message); ?>
                 <p class="mt-2"><a href="index.php#forgot-password" class="font-semibold underline hover:text-white">Request a new link?</a></p>
            </div>
        <?php elseif ($show_form && $token): ?>
            <form id="reset-password-form" method="post" action="reset_password.php" class="space-y-4">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6"
                           class="input-field w-full px-4 py-2 rounded-md text-white focus:outline-none">
                </div>
                 <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6"
                           class="input-field w-full px-4 py-2 rounded-md text-white focus:outline-none">
                </div>

                <?php if ($error_message): // Show error message above button if form submission failed ?>
                    <p class="text-center text-sm text-red-400 pt-1"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>

                <button type="submit" id="reset-btn"
                        class="btn-primary w-full !py-3 !text-base flex justify-center items-center gap-2 group mt-2">
                    <span id="reset-text">Reset Password</span>
                    <span id="reset-spinner" class="spinner hidden"></span>
                </button>
            </form>

            <script>
                // Basic loading state for reset button
                 $('#reset-password-form').on('submit', function() {
                     const btn = $('#reset-btn');
                     const text = $('#reset-text');
                     const spinner = $('#reset-spinner');
                     btn.prop('disabled', true);
                     text.addClass('hidden');
                     spinner.removeClass('hidden');
                     // No need to handle success/error here, PHP does it on page reload
                 });
            </script>

        <?php else: // Should not happen if logic is correct ?>
             <div class="bg-gray-700 p-4 rounded-md text-center text-gray-400">
                An unexpected error occurred.
            </div>
        <?php endif; ?>

    </div>
</body>
</html>