<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | PITHOS Protocol</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111827; } /* bg-gray-900 */
        .login-card { background-color: rgba(31, 41, 55, 0.8); backdrop-filter: blur(10px); } /* bg-gray-800 with opacity */
        .input-field { background-color: rgba(55, 65, 81, 0.9); border-color: #4b5563; } /* bg-gray-700, border-gray-600 */
        .input-field:focus { border-color: #a78bfa; box-shadow: 0 0 0 2px rgba(167, 139, 250, 0.4); } /* border-violet-400 */
        .spinner { /* ... same spinner style ... */ display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="login-card border border-gray-700 p-8 rounded-lg shadow-xl w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center text-white mb-6">Admin Panel Login</h2>

        <form id="admin-login-form" class="space-y-4">
            <div>
                <label for="login_id" class="block text-sm font-medium text-gray-300 mb-1">Username or Email</label>
                <input type="text" name="login_id" id="login_id" required
                       class="input-field w-full px-4 py-2 rounded-md text-white focus:outline-none">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                <input type="password" name="password" id="password" required
                       class="input-field w-full px-4 py-2 rounded-md text-white focus:outline-none">
            </div>

            <p id="login-message" class="text-center text-sm text-red-400 pt-2"></p>

            <button type="submit" id="login-btn"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-md transition duration-200 flex items-center justify-center">
                <span id="login-text">Sign In</span>
                <span id="login-spinner" class="spinner hidden ml-2"></span>
            </button>
        </form>
    </div>

    <script>
        // Inline login script for simplicity
        $(document).ready(function() {
            const CURRENT_ADMIN_FILE = 'index.php'; // Assumes handling is in admin/index.php

            function setAdminButtonLoading(isLoading) {
                const btn = $('#login-btn');
                const text = $('#login-text');
                const spinner = $('#login-spinner');
                if (isLoading) {
                    btn.prop('disabled', true).addClass('opacity-70');
                    text.addClass('hidden'); spinner.removeClass('hidden');
                } else {
                    btn.prop('disabled', false).removeClass('opacity-70');
                    text.removeClass('hidden'); spinner.addClass('hidden');
                }
            }

            $('#admin-login-form').on('submit', function(e) {
                e.preventDefault();
                setAdminButtonLoading(true);
                $('#login-message').text('');

                const formData = $(this).serializeArray();
                formData.push({ name: 'action', value: 'admin_login' }); // Specific action name

                $.post(CURRENT_ADMIN_FILE, formData, function(response) {
                    setAdminButtonLoading(false);
                    if (response.success) {
                        $('#login-message').text(response.message).removeClass('text-red-400').addClass('text-green-400');
                        window.location.href = CURRENT_ADMIN_FILE; // Redirect to admin dashboard on success
                    } else {
                        $('#login-message').text(response.message || 'Login failed.');
                    }
                }).fail(function() {
                    setAdminButtonLoading(false);
                    $('#login-message').text('Network error. Please try again.');
                });
            });
        });
    </script>
</body>
</html>