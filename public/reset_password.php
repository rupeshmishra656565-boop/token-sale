<?php
// Page to handle password reset using OTP
namespace PublicArea;

define('ROOT_DIR', dirname(__DIR__));

require_once(ROOT_DIR . '/config/config.php');
require_once(ROOT_DIR . '/controllers/AuthController.php'); // Need controller

use Controllers\AuthController;
$authController = new AuthController(true); // Start session if needed

$email_from_url = filter_var($_GET['email'] ?? '', FILTER_SANITIZE_EMAIL); // Get email from URL (safer)
$error_message = '';
$success_message = '';
$show_form = true; // Assume showing form unless success

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to reset the password
    // Use the renamed function resetPasswordWithOtp
    $result = $authController->resetPasswordWithOtp($_POST);
    if ($result['success']) {
        $success_message = $result['message'];
        $show_form = false; // Hide form on success
    } else {
        $error_message = $result['message'];
        $email_from_url = filter_var($_POST['email'] ?? $email_from_url, FILTER_SANITIZE_EMAIL); // Keep email in form if error
    }
}
// Note: No token verification needed here anymore on GET request

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
         /* Reusing styles from forgot_password.php */
         :root {
            --primary: #9945FF; --primary-light: #b565ff; --secondary: #14F195;
            --dark-bg: #0a0a0f; --card-bg: rgba(20, 20, 30, 0.85);
            --font-heading: 'Space Grotesk', sans-serif; --font-body: 'Inter', sans-serif;
            --border-color: rgba(153, 69, 255, 0.25); --border-color-light: rgba(255, 255, 255, 0.08);
         }
         body { font-family: var(--font-body); background-color: var(--dark-bg); color: #e5e7eb;}
         .font-heading { font-family: var(--font-heading); }
         .card { background-color: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--border-color); }
         .input-field { background: rgba(0, 0, 0, 0.4); border: 2px solid var(--border-color); }
         .input-field:focus { border-color: var(--primary-light); box-shadow: 0 0 10px rgba(153, 69, 255, 0.3); }
         .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; }
         .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="card p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center font-heading text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-6">Set Your New Password</h2>

        <?php if ($success_message): ?>
            <div class="bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 p-4 rounded-md text-center">
                <?php echo htmlspecialchars($success_message); ?>
                <p class="mt-2"><a href="index.php?login=1" class="font-semibold underline hover:text-white">Click here to Login</a></p>
            </div>
        <?php endif; ?>

        <?php if ($error_message && !$success_message): // Show error only if no success message ?>
             <div class="bg-red-500/20 border border-red-500/30 text-red-300 p-4 rounded-md text-center mb-4">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($show_form): ?>
            <form id="reset-password-form" method="post" action="reset_password.php" class="space-y-4">
                 <div>
                    <label for="email" class="block text-sm font-medium text-gray-400 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" required readonly
                           value="<?php echo htmlspecialchars($email_from_url); ?>"
                           class="input-field w-full px-4 py-3 rounded-xl text-gray-300 focus:outline-none bg-black/30 cursor-not-allowed border-gray-600">
                </div>
                 <div>
                    <label for="otp_code" class="block text-sm font-medium text-gray-300 mb-1">OTP Code</label>
                    <input type="text" name="otp_code" id="otp_code" required inputmode="numeric" pattern="\d{6}" maxlength="6" placeholder="Enter 6-digit code"
                           class="input-field w-full px-4 py-3 rounded-xl text-white focus:outline-none">
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-300 mb-1">New Password</label>
                    <input type="password" name="new_password" id="new_password" required minlength="6" placeholder="Minimum 6 characters"
                           class="input-field w-full px-4 py-3 rounded-xl text-white focus:outline-none">
                </div>
                 <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-300 mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required minlength="6" placeholder="Repeat new password"
                           class="input-field w-full px-4 py-3 rounded-xl text-white focus:outline-none">
                </div>

                <button type="submit" id="reset-btn"
                        class="btn-primary w-full !py-3 !text-base font-semibold flex justify-center items-center gap-2 group mt-2 rounded-xl transition duration-200 hover:opacity-90">
                    <span id="reset-text">Reset Password</span>
                    <span id="reset-spinner" class="spinner hidden"></span>
                </button>
                 <div class="text-center pt-3">
                    <a href="forgot_password.php" class="text-sm font-medium text-purple-400 hover:text-purple-300 transition-colors">Request new OTP?</a>
                </div>
            </form>

            <script>
                // Basic loading state for reset button
                 $('#reset-password-form').on('submit', function() {
                     const btn = $('#reset-btn');
                     const text = $('#reset-text');
                     const spinner = $('#reset-spinner');
                     btn.prop('disabled', true);
                     text.addClass('opacity-0'); // Hide text smoothly
                     spinner.removeClass('hidden');
                     // PHP handles success/error on page reload
                 });
            </script>
         <?php endif; ?>

    </div>
</body>
</html>