<?php
// Page for requesting a password reset OTP
namespace PublicArea;

define('ROOT_DIR', dirname(__DIR__));
require_once(ROOT_DIR . '/config/config.php');
// No need for AuthController here initially, handled via AJAX

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | <?php echo APP_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
         :root { /* Using variables from index.php for consistency */
            --primary: #9945FF;
            --primary-light: #b565ff;
            --secondary: #14F195;
            --dark-bg: #0a0a0f;
            --card-bg: rgba(20, 20, 30, 0.85);
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --border-color: rgba(153, 69, 255, 0.25);
            --border-color-light: rgba(255, 255, 255, 0.08);
         }
         body { font-family: var(--font-body); background-color: var(--dark-bg); color: #e5e7eb;}
         .font-heading { font-family: var(--font-heading); }
         .card { background-color: var(--card-bg); backdrop-filter: blur(10px); border: 1px solid var(--border-color); }
         .input-field { background: rgba(0, 0, 0, 0.4); border: 2px solid var(--border-color); }
         .input-field:focus { border-color: var(--primary-light); box-shadow: 0 0 10px rgba(153, 69, 255, 0.3); }
         .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; }
         .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(3deg); } }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="card p-8 rounded-2xl shadow-xl w-full max-w-md">
        <h2 class="text-2xl font-bold text-center font-heading text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-2">Forgot Your Password?</h2>
        <p class="text-center text-gray-400 mb-6 text-sm">Enter your email address below, and we'll send you an OTP to reset your password.</p>

        <form id="forgot-password-form" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                <input type="email" name="email" id="email" required placeholder="you@example.com"
                       class="input-field w-full px-4 py-3 rounded-xl text-white focus:outline-none">
            </div>

            <p id="message" class="text-center text-sm min-h-[1.25rem] py-1"></p>

            <button type="submit" id="submit-btn"
                    class="btn-primary w-full !py-3 !text-base font-semibold flex justify-center items-center gap-2 group mt-2 rounded-xl transition duration-200 hover:opacity-90">
                <span id="submit-text">Send Reset OTP</span>
                <span id="submit-spinner" class="spinner hidden"></span>
            </button>
             <div class="text-center pt-3">
                <a href="index.php?login=1" class="text-sm font-medium text-purple-400 hover:text-purple-300 transition-colors">← Back to Login</a>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            $('#forgot-password-form').on('submit', function(e) {
                e.preventDefault();
                const email = $('#email').val();
                const messageEl = $('#message');
                const btn = $('#submit-btn');
                const text = $('#submit-text');
                const spinner = $('#submit-spinner');

                messageEl.text('').removeClass('text-emerald-400 text-red-400');
                btn.prop('disabled', true);
                text.addClass('opacity-0'); spinner.removeClass('hidden');

                $.ajax({
                    url: 'index.php', // Post to the main index handler
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'send_password_reset_otp',
                        email: email
                    },
                    success: function(response) {
                        if (response.success) {
                            messageEl.text(response.message + " Redirecting...").addClass('text-emerald-400');
                            // Redirect to the reset page where they enter OTP
                            setTimeout(function() {
                                window.location.href = 'reset_password.php?email=' + encodeURIComponent(email);
                            }, 2000);
                        } else {
                            messageEl.text(response.message || 'Failed to send OTP.').addClass('text-red-400');
                            btn.prop('disabled', false);
                            text.removeClass('opacity-0'); spinner.addClass('hidden');
                        }
                    },
                    error: function() {
                        messageEl.text('Network error. Please try again.').addClass('text-red-400');
                        btn.prop('disabled', false);
                        text.removeClass('opacity-0'); spinner.addClass('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>