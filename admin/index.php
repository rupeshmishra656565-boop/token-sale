<?php
namespace AdminArea;

// Define admin root safely
define('ADMIN_DIR', __DIR__);
// Define project root relative to admin dir
define('ROOT_DIR', dirname(ADMIN_DIR));

require_once(ROOT_DIR . '/config/config.php');
require_once(ROOT_DIR . '/controllers/AdminController.php');

use Controllers\AdminController;

$adminController = new AdminController(); // Session starts here if needed

// --- Handle AJAX API Requests for Admin Panel ---
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Invalid Admin Request'];

    // Public action: Login
    if ($_POST['action'] === 'admin_login') {
        $response = $adminController->login($_POST);
    }
    // Public action: Logout (Can be POST or GET depending on preference)
    elseif ($_POST['action'] === 'admin_logout') {
         $response = $adminController->logout();
    }
    // Protected actions: Require admin login
    elseif ($adminController->isAdminLoggedIn()) {
        switch ($_POST['action']) {
            case 'getAdminOverview': $response = $adminController->getAdminOverview(); break;
            case 'getPendingWithdrawals': $response = $adminController->getPendingWithdrawals(); break;
            case 'processWithdrawal': $response = $adminController->processWithdrawal($_POST); break;
            case 'getAllUsers': $response = $adminController->getAllUsers(); break;
            case 'adjustUserBalance': $response = $adminController->adjustUserBalance($_POST); break;
            // Add other admin actions here
            default:
                 $response = ['success' => false, 'message' => 'Unknown admin action.'];
                 break;
        }
    }
    // Action requires login but user is not logged in
    else {
         $response = ['success' => false, 'message' => 'Authentication required for this action.'];
    }

    echo json_encode($response);
    exit;
}


// --- Handle Page Views ---

// If admin is not logged in, show the login page
if (!$adminController->isAdminLoggedIn()) {
    require_once(ADMIN_DIR . '/views/login.php');
    exit;
}

// If admin is logged in, show the dashboard (or handle routing for other admin pages)
$admin_page = $_GET['p'] ?? 'dashboard';
$admin_username = $_SESSION['admin_username'] ?? 'Admin';

// Basic structure for admin layout (can be enhanced)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PITHOS Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="assets/admin_styles.css" rel="stylesheet"> <style>
        /* Minimal styles for layout */
        body { background-color: #111827; color: #d1d5db; }
        .glass-card { background: rgba(31, 41, 55, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(75, 85, 99, 0.5); border-radius: 1rem; }
        .spinner { /* ... same spinner style ... */ display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }
        /* Include status badge styles from main.css or add here */
        .status-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; line-height: 1; }
        .status-complete { background-color: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-pending { background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-failed { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .status-processing { background-color: rgba(139, 92, 246, 0.2); color: #a78bfa; }
    </style>
</head>
<body class="min-h-screen">
    <header class="bg-gray-800/80 backdrop-blur-md shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                 <div class="flex items-center">
                    <span class="font-bold text-xl text-purple-400">PITHOS Admin</span>
                 </div>
                 <div class="flex items-center space-x-4">
                     <span class="text-sm text-gray-300">Welcome, <?php echo htmlspecialchars($admin_username); ?></span>
                     <form id="logout-form" method="post" action="index.php">
                         <input type="hidden" name="action" value="admin_logout">
                         <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">Logout</button>
                     </form>
                 </div>
            </div>
        </div>
    </header>

    <main class="py-8 md:py-12">
        <?php
            // Simple routing for now, only dashboard
            switch ($admin_page) {
                case 'dashboard':
                default:
                    require_once(ADMIN_DIR . '/views/dashboard.php'); // Was views/admin.php
                    break;
                // Add cases for other admin pages here if needed
                // case 'users':
                //    require_once(ADMIN_DIR . '/views/users.php');
                //    break;
            }
        ?>
    </main>

    <footer class="text-center py-4 mt-8 border-t border-gray-700">
        <p class="text-xs text-gray-500">&copy; <?php echo date("Y"); ?> PITHOS Admin Panel</p>
    </footer>

    <script src="assets/admin.js"></script>
    <script>
        // Handle logout success/failure from form submission potentially
        $('#logout-form').on('submit', function(e) {
            e.preventDefault();
            $.post('index.php', { action: 'admin_logout' }, function(response) {
                if (response.success) {
                    window.location.href = 'index.php'; // Redirect to login page
                } else {
                    alert('Logout failed: ' + (response.message || 'Unknown error'));
                }
            }).fail(function(){
                alert('Network error during logout.');
            });
        });
        // Global admin helpers (like showToast if needed) can be defined here or in admin.js
         function showToast(message, type) { /* ... basic toast implementation ... */
             const container = $('body'); // Simple append to body
             const toast = $('<div>').css({ position: 'fixed', top: '20px', right: '20px', padding: '10px 20px', borderRadius: '5px', color: 'white', zIndex: 1000, opacity: 0.9 })
                 .addClass(type === 'success' ? 'bg-green-600' : 'bg-red-600')
                 .text(message);
             container.append(toast);
             setTimeout(() => toast.fadeOut(500, () => toast.remove()), 4000);
         }
         window.showToast = showToast; // Make it global for admin.js
    </script>
</body>
</html>