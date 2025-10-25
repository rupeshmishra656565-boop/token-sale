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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <link href="assets/admin_styles.css" rel="stylesheet"> 
    <style>
        :root {
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --bg-dark: #0a0a0f;
            --bg-card: rgba(20, 20, 30, 0.7);
            --border-color: rgba(153, 69, 255, 0.2);
            --primary: #9945FF;
            --primary-glow: rgba(153, 69, 255, 0.5);
        }
        body { 
            background-color: var(--bg-dark); 
            color: #d1d5db; 
            font-family: var(--font-body);
        }
        .font-heading { font-family: var(--font-heading); }
        .glass-card { 
            background: var(--bg-card); 
            backdrop-filter: blur(15px); 
            border: 1px solid var(--border-color); 
            border-radius: 1.25rem; /* 20px */
        }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } 
        @keyframes spin { to { transform: rotate(360deg); } }
        
        /* Status Badges */
        .status-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 99px; font-size: 0.75rem; font-weight: 600; line-height: 1; text-transform: uppercase; letter-spacing: 0.05em; }
        .status-complete { background-color: rgba(16, 185, 129, 0.2); color: #10b981; }
        .status-pending { background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .status-failed { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .status-processing { background-color: rgba(139, 92, 246, 0.2); color: #a78bfa; }
        .status-unknown { background-color: rgba(107, 114, 128, 0.2); color: #9ca3af; }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    </style>
</head>
<body class="min-h-screen antialiased">
    <header class="bg-gray-900/80 backdrop-blur-md shadow-md sticky top-0 z-50 border-b border-[var(--border-color)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                 <div class="flex items-center gap-2">
                    <svg class="w-8 h-8 text-[var(--primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0112 2.964z" />
                    </svg>
                    <span class="font-heading font-bold text-xl text-purple-400">PITHOS Admin</span>
                 </div>
                 <div class="flex items-center space-x-4">
                     <span class="text-sm text-gray-300 hidden sm:inline">Welcome, <span class="font-semibold text-purple-400"><?php echo htmlspecialchars($admin_username); ?></span></span>
                     <form id="logout-form" method="post" action="index.php">
                         <input type="hidden" name="action" value="admin_logout">
                         <button type="submit" class="text-sm font-medium text-red-500 hover:text-red-400 transition-colors duration-200 bg-red-500/10 hover:bg-red-500/20 px-3 py-1.5 rounded-lg">Logout</button>
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

    <footer class="text-center py-6 mt-8 border-t border-[var(--border-color)]">
        <p class="text-xs text-gray-500">&copy; <?php echo date("Y"); ?> PITHOS Admin Panel. All Rights Reserved.</p>
    </footer>

    <div id="toast-container" class="fixed top-6 right-6 z-[100] space-y-3"></div>

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

        // Global admin toast function
         function showToast(message, type) {
             const container = $('#toast-container');
             let icon;
             let styles;

             if (type === 'success') {
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                styles = 'bg-emerald-600 border-emerald-500/50';
             } else {
                icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                styles = 'bg-red-600 border-red-500/50';
             }

             const toast = $(`
                <div class="flex items-center gap-3 p-4 rounded-xl shadow-lg text-white border ${styles} glass-card animate-fadeIn" style="animation: fadeIn 0.3s ease-out; backdrop-filter: blur(10px);">
                    ${icon}
                    <span class="text-sm font-medium">${message}</span>
                </div>
             `);
             
             container.append(toast);
             setTimeout(() => {
                toast.fadeOut(500, () => toast.remove());
             }, 4000);
         }
         window.showToast = showToast; // Make it global for admin.js
    </script>
</body>
</html>