<?php
// ==============================================================================
// 1. PHP Configuration & Dependencies
// ==============================================================================
namespace PublicArea;

// Define the root directory safely
define('ROOT_DIR', dirname(__DIR__));

require_once(ROOT_DIR . '/config/config.php');
require_once(ROOT_DIR . '/controllers/AuthController.php'); // Only AuthController needed

use Controllers\AuthController;
$authController = new AuthController(true); // Explicitly start session

// ==============================================================================
// 2. Handle AJAX API Requests (User Actions + Polling)
// ==============================================================================
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Invalid Request']; // Default response

    switch ($_POST['action']) {
        // User Authentication
        case 'register': $response = $authController->register($_POST); break;
        case 'login': $response = $authController->login($_POST); break;
        case 'logout': $response = $authController->logout(); break;
        case 'updatePassword': $response = $authController->updatePassword($_POST); break;
        // OTP/Forgot Password actions removed

        // Payment Flow
        case 'create_payment_invoice': $response = $authController->createNowPaymentsInvoice($_POST); break;
        case 'get_payment_status': $response = $authController->getPaymentStatus($_POST); break; // For polling

        // User Data / Actions
        case 'withdraw_tokens': $response = $authController->withdrawTokens($_POST); break;
        case 'get_balance': $response = $authController->getBalance(); break;
        case 'get_referral_history': $response = $authController->getReferralHistory(); break;
        case 'get_transaction_history': $response = $authController->getTransactionHistory(); break;
        case 'get_user_details': $response = $authController->getUserDetails(); break;
        
        // Admin actions removed

        default:
            error_log("Unknown user AJAX Action: " . ($_POST['action'] ?? 'N/A'));
            break;
    }
    echo json_encode($response);
    exit;
}

// ==============================================================================
// 3. Prepare Variables for the View (User Area Only)
//==============================================================================
$is_logged_in = isset($_SESSION['user_id']);
$current_username = $_SESSION['username'] ?? 'Guest';
// $isAdmin variable removed
// Set default page based on login status
$page = $_GET['p'] ?? ($is_logged_in ? 'dashboard' : 'home');
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PITHOS Protocol | Token Sale</title>
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
        header { background: rgba(10, 10, 15, 0.9); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(153, 69, 255, 0.2); }
        .logo { font-family: var(--font-heading); font-weight: 700; font-size: 1.75rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }
        @media (min-width: 768px) { .container { padding: 0 2rem; } }
        .hero-title { font-family: var(--font-heading); font-size: clamp(3rem, 8vw, 6.5rem); font-weight: 700; line-height: 1; letter-spacing: -3px; margin-bottom: 2rem; }
        .gradient-text { background: linear-gradient(135deg, #ffffff 0%, var(--primary) 50%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradient-shift 8s ease infinite; background-size: 200% auto; }
        @keyframes gradient-shift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .btn-primary, .btn-secondary { padding: 1rem 2rem; font-size: 1rem; font-weight: 600; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-align: center;}
        .btn-primary { background: linear-gradient(135deg, var(--secondary), #0ea770); color: #000; box-shadow: 0 0 20px rgba(20, 241, 149, 0.3); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 25px rgba(20, 241, 149, 0.4);}
        .btn-secondary { background: transparent; color: #fff; border: 2px solid rgba(153, 69, 255, 0.5); }
        .btn-secondary:hover { background-color: rgba(153, 69, 255, 0.1); border-color: rgba(153, 69, 255, 0.8); }
        .glass-card { background: var(--card-bg); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; }
        .input-group { position: relative; margin-bottom: 1.5rem; }
        .input-field { width: 100%; padding: 1rem 1.5rem; background: rgba(0, 0, 0, 0.4); border: 2px solid rgba(153, 69, 255, 0.3); border-radius: 12px; color: #fff; outline: none; transition: all 0.3s ease; font-size: 1rem; }
        .peer-label { position: absolute; left: 1.5rem; top: 1.25rem; color: rgba(255, 255, 255, 0.5); transition: all 0.3s ease; pointer-events: none; background: transparent; padding: 0 0.5rem; }
        .input-field:focus~.peer-label, .input-field:not(:placeholder-shown)~.peer-label { transform: translateY(-2.2rem) scale(0.85); color: var(--primary); background: #111827; padding: 0 0.5rem;}
        .input-field:focus { border-color: var(--primary); box-shadow: 0 0 15px rgba(153, 69, 255, 0.3); }
        .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } @keyframes spin { to { transform: rotate(360deg); } }
        #toast-container { position: fixed; top: 2rem; right: 2rem; z-index: 1000; }
        .nav-link { transition: all 0.2s ease-in-out; padding: 0.5rem 0.75rem; border-radius: 8px; }
        .nav-link:hover { background-color: rgba(153, 69, 255, 0.1); color: #fff; }
        .nav-link.active-link { color: #fff; background-color: rgba(153, 69, 255, 0.3); font-weight: 600; }
        .footer-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        @media (min-width: 768px) { .footer-grid { grid-template-columns: 2fr 1fr 1fr; } }
        .mobile-nav-toggle { padding: 0.5rem; border-radius: 8px; transition: background-color 0.3s; }
        .mobile-nav-toggle:hover { background-color: rgba(255, 255, 255, 0.1); }
        .font-heading { font-family: 'Space Grotesk', sans-serif; }
    </style>
</head>

<body class="min-h-screen">
    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div> <div class="gradient-orb orb-2"></div> <div class="gradient-orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>
    <div id="toast-container"></div>

    <div class="content-wrapper min-h-screen flex flex-col">
        <header class="sticky top-0 z-50">
            <div class="container">
                <nav class="flex items-center justify-between py-4 md:py-6">
                    <a href="index.php" class="logo">PITHOS</a>

                    <div class="hidden md:flex items-center gap-4 md:gap-6">
                        <?php if ($is_logged_in): ?>
                            <span class="text-sm hidden lg:inline text-gray-400">Hello, <span class="font-semibold text-violet-400"><?php echo htmlspecialchars($current_username); ?></span></span>
                            <?php
                                $nav_links = ['dashboard' => 'Dashboard', 'wallet' => 'Wallet', 'referrals' => 'Referrals', 'profile' => 'Profile'];
                                foreach ($nav_links as $link_page => $link_text) {
                                    $is_active = ($page === $link_page);
                                    $class = 'text-gray-300 hover:text-white nav-link transition ' . ($is_active ? 'active-link' : '');
                                    echo '<a href="index.php?p=' . $link_page . '" class="' . $class . '">' . $link_text . '</a>';
                                }
                            ?>
                            <button id="logout-btn" class="btn-secondary !py-2 !px-4 !text-sm hover:bg-red-700 hover:border-red-500">Logout</button>
                        <?php else: ?>
                            <a href="#auth" onclick="showAuthForm('login'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="text-sm text-gray-300 hover:text-violet-400 transition nav-link">Sign In</a>
                            <a href="#auth" onclick="showAuthForm('register'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="btn-primary !py-2 !px-4 !text-sm">Get Started</a>
                        <?php endif; ?>
                    </div>

                    <button class="md:hidden text-white mobile-nav-toggle" onclick="$('#mobile-nav-menu').toggleClass('hidden')">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                </nav>
            </div>
             <div id="mobile-nav-menu" class="hidden md:hidden bg-gray-900/95 border-t border-white/10 transition-all duration-300">
                <div class="container py-4 flex flex-col space-y-3">
                     <?php if ($is_logged_in): ?>
                        <span class="text-sm text-violet-400 font-semibold border-b border-white/10 pb-2 mb-2">User: <?php echo htmlspecialchars($current_username); ?></span>
                        <?php
                            $nav_links = ['dashboard' => 'Dashboard', 'wallet' => 'Wallet', 'referrals' => 'Referrals', 'profile' => 'Profile'];
                            foreach ($nav_links as $link_page => $link_text) {
                                $is_active = ($page === $link_page);
                                $class = 'text-sm text-gray-300 hover:text-white transition py-2 px-3 rounded-lg ' . ($is_active ? 'active-link' : '');
                                echo '<a href="index.php?p=' . $link_page . '" class="' . $class . '">' . $link_text . '</a>';
                            }
                        ?>
                        <button id="logout-btn-mobile" class="btn-secondary !py-2 !px-4 !text-sm mt-3">Logout</button>
                     <?php else: ?>
                        <a href="#auth" onclick="showAuthForm('login'); $('#mobile-nav-menu').addClass('hidden'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="text-sm text-gray-300 hover:text-violet-400 transition nav-link py-2 px-3">Sign In</a>
                        <a href="#auth" onclick="showAuthForm('register'); $('#mobile-nav-menu').addClass('hidden'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="btn-primary !py-2 !px-4 !text-sm">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <main class="flex-grow">
            <?php if ($is_logged_in): ?>
                <div class="py-8 md:py-16">
                <?php
                    // --- PAGE ROUTING LOGIC (Admin route removed) ---
                    switch ($page) {
                        case 'profile': require_once(ROOT_DIR . '/views/profile.php'); break;
                        case 'wallet': require_once(ROOT_DIR . '/views/wallet.php'); break;
                        case 'referrals': require_once(ROOT_DIR . '/views/referrals.php'); break;
                        case 'dashboard': default: require_once(ROOT_DIR . '/views/dashboard.php'); break;
                    }
                ?>
                </div>
            <?php else: ?>
                <?php require_once(ROOT_DIR . '/views/home.php'); // Home page includes auth form ?>
            <?php endif; ?>
        </main>

        <footer class="mt-auto py-8 bg-gray-900/50 border-t border-white/10">
             <div class="container">
                <div class="footer-grid">
                    <div>
                        <h3 class="font-bold text-lg mb-4 text-violet-400 font-heading">PITHOS Protocol</h3>
                        <p class="text-sm text-gray-400">Building the immutable digital foundation on the Solana ecosystem.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-violet-400 font-heading">Resources</h4>
                        <ul class="space-y-2 text-sm"><li><a href="#" class="text-gray-400 hover:text-white">Whitepaper</a></li><li><a href="#" class="text-gray-400 hover:text-white">Tokenomics</a></li></ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-violet-400 font-heading">Community</h4>
                        <ul class="space-y-2 text-sm"><li><a href="#" class="text-gray-400 hover:text-white">Twitter (X)</a></li><li><a href="#" class="text-gray-400 hover:text-white">Telegram</a></li></ul>
                    </div>
                </div>
                <div class="text-center py-6 mt-6 border-t border-white/10">
                    <p class="text-gray-500 text-sm">&copy; <?php echo date("Y"); ?> PITHOS Protocol. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/main.js"></script>
    <?php
        // Load page-specific JS (No admin.js case)
        if ($is_logged_in) {
            switch($page) {
                case 'profile': echo '<script src="assets/profile.js"></script>'; break;
                case 'wallet': echo '<script src="assets/wallet.js"></script>'; break;
                case 'referrals': echo '<script src="assets/referrals.js"></script>'; break;
            }
        }
    ?>
    <script>
        // --- [REVERTED] Inline Script ---
        $('#logout-btn-mobile').on('click', function(e) {
            e.preventDefault();
            $('#logout-btn').click();
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
             anchor.addEventListener('click', function(e) {
                 const targetId = this.getAttribute('href');
                 if (targetId.startsWith('#')) {
                     const targetElement = document.querySelector(targetId);
                     if (targetElement) {
                         e.preventDefault();
                         $('#mobile-nav-menu').addClass('hidden');
                         targetElement.scrollIntoView({ behavior: 'smooth' });
                     }
                 }
             });
         });

        // REVERTED showAuthForm function (handles ONLY login/register)
        window.showAuthForm = function(type) {
            const loginTab = $('#tab-login');
            const registerTab = $('#tab-register');
            const loginForm = $('#form-login');
            const registerForm = $('#form-register');
            const forgotForm = $('#form-forgot'); // Get forgot form (if it exists)

            loginForm.addClass('hidden');
            registerForm.addClass('hidden');
            if(forgotForm.length) forgotForm.addClass('hidden'); // Hide forgot form
            
            const activeClasses = 'bg-gradient-to-r from-purple-600 to-cyan-600 text-white shadow-md';
            const inactiveClasses = 'text-gray-400 hover:text-white hover:bg-white/5';
            loginTab.removeClass(activeClasses).addClass(inactiveClasses);
            registerTab.removeClass(activeClasses).addClass(inactiveClasses);
            
            $('#login-message').text('');
            $('#register-message').text('');
            if ($('#forgot-message').length) $('#forgot-message').text('');

            if (type === 'login') {
                loginForm.removeClass('hidden');
                loginTab.removeClass(inactiveClasses).addClass(activeClasses);
            } else if (type === 'register') {
                registerForm.removeClass('hidden');
                registerTab.removeClass(activeClasses).addClass(inactiveClasses);
                // Reset OTP state (if it exists)
                if($('#otp-section').length) $('#otp-section').addClass('hidden').hide(); 
                if($('#send-otp-btn').length) $('#send-otp-btn').removeClass('hidden');
                if($('#register-btn').length) {
                     // Check if this is the OTP version or simple
                     if ($('#send-otp-btn').length) {
                         $('#register-btn').addClass('hidden').removeClass('flex'); // Hide if OTP btn exists
                     } else {
                         $('#register-btn').removeClass('hidden').addClass('flex'); // Show if no OTP btn
                     }
                }
            }
            // 'forgot' type handling removed from this simple version
        }
    </script>
</body>
</html>