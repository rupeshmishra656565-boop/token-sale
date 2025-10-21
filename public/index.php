<?php
// ==============================================================================
// 1. PHP Configuration & Dependencies
// ==============================================================================
namespace PublicArea;

// Define the root directory safely
define('ROOT_DIR', dirname(__DIR__));

require_once(ROOT_DIR . '/config/config.php');
require_once(ROOT_DIR . '/controllers/AuthController.php');

use Controllers\AuthController;
$authController = new AuthController();

// ==============================================================================
// 2. Handle AJAX API Requests
// ==============================================================================
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Invalid Request'];

    switch ($_POST['action']) {
        case 'register': $response = $authController->register($_POST); break;
        case 'login': $response = $authController->login($_POST); break;
        case 'buy_tokens': $response = $authController->buyTokens($_POST); break;
        case 'withdraw_tokens': $response = $authController->withdrawTokens($_POST); break; 
        case 'get_balance': $response = $authController->getBalance(); break;
        case 'get_referral_history': $response = $authController->getReferralHistory(); break;
        case 'get_transaction_history': $response = $authController->getTransactionHistory(); break; 
        case 'get_user_details': $response = $authController->getUserDetails(); break;
        case 'updatePassword': $response = $authController->updatePassword($_POST); break;
        
        // ADMIN ROUTES
        case 'getAdminOverview': $response = $authController->getAdminOverview(); break;
        case 'getPendingWithdrawals': $response = $authController->getPendingWithdrawals(); break;
        case 'processWithdrawal': $response = $authController->processWithdrawal($_POST); break;
        case 'getAllUsers': $response = $authController->getAllUsers(); break;
        case 'adjustUserBalance': $response = $authController->adjustUserBalance($_POST); break;

        case 'logout': $response = $authController->logout(); break;
    }
    echo json_encode($response);
    exit;
}

// ==============================================================================
// 3. Prepare Variables for the View
//==============================================================================
$is_logged_in = isset($_SESSION['user_id']);
$current_username = $_SESSION['username'] ?? 'Guest';
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true; // Check admin status
// Set default page based on login status
$page = $_GET['p'] ?? ($is_logged_in ? 'dashboard' : 'home'); 
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PITHOS Protocol | The Immutable Token of the Solana Ecosystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #9945FF; --primary-glow: rgba(153, 69, 255, 0.5);
            --secondary: #14F195; --secondary-glow: rgba(20, 241, 149, 0.4);
            --dark-bg: #0a0a0f; 
            --card-bg: rgba(20, 20, 30, 0.7); 
            --font-heading: 'Space Grotesk', sans-serif;
        }
        /* Base styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--dark-bg); color: #ffffff; overflow-x: hidden; position: relative; }
        .animated-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; overflow: hidden; pointer-events: none; }
        .gradient-orb { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.3; animation: float 20s infinite ease-in-out; }
        .orb-1 { width: 500px; height: 500px; background: radial-gradient(circle, var(--primary), transparent); top: -10%; right: -10%; }
        .orb-2 { width: 400px; height: 400px; background: radial-gradient(circle, var(--secondary), transparent); bottom: -10%; left: -10%; animation-delay: -10s; }
        .orb-3 { width: 350px; height: 350px; background: radial-gradient(circle, #6366f1, transparent); top: 50%; left: 50%; animation-delay: -5s; }
        @keyframes float { 0%, 100% { transform: translate(0, 0) scale(1); } 33% { transform: translate(50px, -50px) scale(1.1); } 66% { transform: translate(-50px, 50px) scale(0.9); } }
        .grid-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(rgba(153, 69, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(153, 69, 255, 0.03) 1px, transparent 1px); background-size: 50px 50px; z-index: 1; pointer-events: none; }
        .content-wrapper { position: relative; z-index: 2; }
        /* Advanced Navbar Styling */
        header { background: rgba(10, 10, 15, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(153, 69, 255, 0.2); }
        .logo { font-family: var(--font-heading); font-weight: 700; font-size: 1.75rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }
        @media (min-width: 768px) { .container { padding: 0 2rem; } }
        .hero-title { font-family: var(--font-heading); font-size: clamp(3rem, 8vw, 6.5rem); font-weight: 700; line-height: 1; letter-spacing: -3px; margin-bottom: 2rem; }
        .gradient-text { background: linear-gradient(135deg, #ffffff 0%, var(--primary) 50%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradient-shift 8s ease infinite; background-size: 200% auto; }
        @keyframes gradient-shift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .btn-primary, .btn-secondary { padding: 1.25rem 3rem; font-size: 1.125rem; font-weight: 600; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; }
        .btn-primary { background: linear-gradient(135deg, var(--secondary), #0ea770); color: #000; box-shadow: 0 0 30px var(--secondary-glow); }
        .btn-secondary { background: transparent; color: #fff; border: 2px solid rgba(153, 69, 255, 0.5); }
        
        /* Glass Card Base Style */
        .glass-card {
            background: var(--card-bg);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
        }

        .input-group { position: relative; margin-bottom: 1.5rem; }
        .input-field { width: 100%; padding: 1rem 1.5rem; background: rgba(0, 0, 0, 0.4); border: 2px solid rgba(153, 69, 255, 0.3); border-radius: 12px; color: #fff; outline: none; transition: all 0.3s ease; font-size: 1rem; }
        .peer-label { position: absolute; left: 1.5rem; top: 1.25rem; color: rgba(255, 255, 255, 0.5); transition: all 0.3s ease; pointer-events: none; background: transparent; padding: 0 0.5rem; }
        .input-field:focus~.peer-label, .input-field:not(:placeholder-shown)~.peer-label { transform: translateY(-2.5rem) scale(0.85); color: var(--primary); background: var(--dark-bg); }
        
        .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        #toast-container { position: fixed; top: 2rem; right: 2rem; z-index: 1000; }
        .stat-card-value { font-family: var(--font-heading); font-size: 2rem; font-weight: 700; line-height: 1; }
        @media (min-width: 768px) { .stat-card-value { font-size: 2.5rem; } }
        .transaction-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        .transaction-table th, .transaction-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid rgba(153, 69, 255, 0.1); }
        .status-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.5rem; border-radius: 50px; font-size: 0.7rem; font-weight: 600; }
        .status-complete { background-color: rgba(20, 241, 149, 0.2); color: #14F195; }
        .status-pending { background-color: rgba(251, 191, 36, 0.2); color: #facc15; }
        .status-failed { background-color: rgba(239, 68, 68, 0.2); color: #f87171; }
        .status-processing { background-color: rgba(153, 69, 255, 0.2); color: #a78bfa; }
        .status-badge-dot { width: 8px; height: 8px; border-radius: 50%; margin-right: 0.5rem; }
        .footer-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 2rem; }
        @media (min-width: 768px) { .footer-grid { grid-template-columns: 2fr 1fr 1fr; } }
        .mobile-nav-toggle { padding: 0.5rem; border-radius: 8px; transition: background-color 0.3s; }
        .mobile-nav-toggle:hover { background-color: rgba(255, 255, 255, 0.1); }
        
        /* Menu Styles */
        .nav-link {
            transition: all 0.2s ease-in-out;
            padding: 0.5rem 0.75rem;
            border-radius: 8px;
        }
        .nav-link:hover {
            background-color: rgba(153, 69, 255, 0.1);
            color: #fff;
        }
        .nav-link.active-link {
            color: #fff;
            background-color: rgba(153, 69, 255, 0.3);
            font-weight: 600;
        }
    </style>
</head>

<body class="min-h-screen">
    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div>
        <div class="gradient-orb orb-2"></div>
        <div class="gradient-orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>
    <div id="toast-container"></div>

    <div class="content-wrapper min-h-screen flex flex-col">
        <header class="sticky top-0 z-50">
            <div class="container">
                <nav class="flex items-center justify-between py-4 md:py-6">
                    <a href="index.php" class="logo">PITHOS</a>
                    
                    <!-- Desktop Navigation Links -->
                    <div class="hidden md:flex items-center gap-4 md:gap-6">
                        <?php if ($is_logged_in): ?>
                            <span class="text-sm hidden lg:inline text-gray-400">Hello, <span class="font-semibold text-violet-400"><?php echo htmlspecialchars($current_username); ?></span></span>
                            
                            <?php 
                                $nav_links = [
                                    'dashboard' => 'Dashboard',
                                    'wallet' => 'Wallet',
                                    'referrals' => 'Referrals',
                                    'profile' => 'Profile'
                                ];
                                // Add Admin link only if user is admin
                                if ($isAdmin) {
                                    $nav_links['admin'] = 'Admin';
                                }
                                
                                foreach ($nav_links as $link_page => $link_text) {
                                    $is_active = ($page === $link_page);
                                    $class = 'text-gray-300 hover:text-white nav-link transition ' . ($is_active ? 'active-link' : '');
                                    echo '<a href="index.php?p=' . $link_page . '" class="' . $class . '">' . $link_text . '</a>';
                                }
                            ?>
                            
                            <button id="logout-btn" class="btn-secondary py-2 px-4 text-sm hover:bg-red-700 hover:border-red-500">Logout</button>
                        <?php else: ?>
                            <a href="#auth" onclick="showAuthForm('login')" class="text-sm text-gray-300 hover:text-violet-400 transition nav-link">Sign In</a>
                            <a href="#auth" onclick="showAuthForm('register')" class="btn-primary py-2 px-4 text-sm">Get Started</a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button class="md:hidden text-white mobile-nav-toggle" onclick="$('#mobile-nav-menu').toggleClass('hidden')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </nav>
            </div>
             <!-- Mobile Menu Content -->
            <div id="mobile-nav-menu" class="hidden md:hidden bg-gray-900/95 border-t border-white/10 transition-all duration-300">
                <div class="container py-4 flex flex-col space-y-3">
                     <?php if ($is_logged_in): ?>
                        <span class="text-sm text-violet-400 font-semibold border-b border-white/10 pb-2 mb-2">User: <?php echo htmlspecialchars($current_username); ?></span>
                        
                        <?php 
                            $nav_links = [
                                'dashboard' => 'Dashboard',
                                'wallet' => 'Wallet',
                                'referrals' => 'Referrals',
                                'profile' => 'Profile'
                            ];
                            if ($isAdmin) {
                                $nav_links['admin'] = 'Admin';
                            }
                            
                            foreach ($nav_links as $link_page => $link_text) {
                                $is_active = ($page === $link_page);
                                $class = 'text-sm text-gray-300 hover:text-white transition py-2 px-3 rounded-lg ' . ($is_active ? 'active-link' : '');
                                echo '<a href="index.php?p=' . $link_page . '" class="' . $class . '">' . $link_text . '</a>';
                            }
                        ?>
                        
                        <button id="logout-btn-mobile" class="btn-secondary py-2 px-4 text-sm mt-3">Logout</button>
                     <?php else: ?>
                        <a href="#auth" onclick="showAuthForm('login')" class="text-sm text-gray-300 hover:text-violet-400 transition nav-link py-2 px-3">Sign In</a>
                        <a href="#auth" onclick="showAuthForm('register')" class="btn-primary py-2 px-4 text-sm">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <?php if ($is_logged_in): ?>
                <div class="py-8 md:py-16">
                <?php
                    // --- PAGE ROUTING LOGIC ---
                    switch ($page) {
                        case 'admin':
                            require_once(ROOT_DIR . '/views/admin.php');
                            break;
                        case 'profile': 
                            require_once(ROOT_DIR . '/views/profile.php');
                            break;
                        case 'wallet': 
                            require_once(ROOT_DIR . '/views/wallet.php');
                            break;
                        case 'referrals':
                            require_once(ROOT_DIR . '/views/referrals.php');
                            break;
                        case 'dashboard':
                        default:
                            require_once(ROOT_DIR . '/views/dashboard.php');
                            break;
                    }
                ?>
                </div>
            <?php else: ?>
                <?php require_once(ROOT_DIR . '/views/home.php'); ?>
            <?php endif; ?>
        </main>

        <footer class="mt-auto">
            <div class="container">
                <div class="footer-grid">
                    <div>
                        <h3 class="font-bold text-lg mb-4 text-violet-400">PITHOS Protocol</h3>
                        <p class="text-sm text-gray-400">Building the immutable digital foundation on the Solana ecosystem.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-violet-400">Resources</h4>
                        <ul class="space-y-2 text-sm"><li><a href="#" class="text-gray-400 hover:text-white">Whitepaper</a></li><li><a href="#" class="text-gray-400 hover:text-white">Tokenomics</a></li></ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-violet-400">Community</h4>
                        <ul class="space-y-2 text-sm"><li><a href="#" class="text-gray-400 hover:text-white">Twitter (X)</a></li><li><a href="#" class="text-gray-400 hover:text-white">Telegram</a></li></ul>
                    </div>
                </div>
                <div class="text-center py-4 mt-4 md:py-8 md:mt-8 border-t border-white/10">
                    <p class="text-gray-500 text-sm">&copy; <?php echo date("Y"); ?> PITHOS Protocol. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/main.js"></script>
    <?php
        // Load page-specific JS
        if ($is_logged_in && $page === 'profile') {
            echo '<script src="assets/profile.js"></script>';
        } else if ($is_logged_in && $page === 'wallet') { 
            echo '<script src="assets/wallet.js"></script>';
        } else if ($is_logged_in && $page === 'referrals') {
            echo '<script src="assets/referrals.js"></script>';
        } else if ($is_logged_in && $page === 'admin') { // Admin JS loader
            echo '<script src="assets/admin.js"></script>';
        }
    ?>
    <script>
        // Ensure logout works for the mobile button too
         $('#logout-btn-mobile').on('click', function(e) {
            e.preventDefault();
            $('#logout-btn').click(); // Trigger the desktop handler
        });
        
        // Inline JS for smooth scroll and auth form tabs
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({ behavior: 'smooth' });
            });
        });
        window.showAuthForm = function(type) {
            const loginTab = $('#tab-login');
            const registerTab = $('#tab-register');
            const loginForm = $('#form-login');
            const registerForm = $('#form-register');
            
            // Standard CSS classes for active state
            const activeClasses = 'active bg-violet-600 text-white shadow-md';
            const inactiveClasses = 'text-gray-300 hover:text-white';

            if (type === 'login') {
                loginForm.removeClass('hidden');
                registerForm.addClass('hidden');
                loginTab.addClass(activeClasses).removeClass(inactiveClasses);
                registerTab.removeClass(activeClasses).addClass(inactiveClasses);
            } else {
                loginForm.addClass('hidden');
                registerForm.removeClass('hidden');
                registerTab.addClass(activeClasses).removeClass(inactiveClasses);
                loginTab.removeClass(activeClasses).addClass(inactiveClasses);
            }
        }
    </script>
</body>
</html>
