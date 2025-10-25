<?php
namespace PublicArea;

define('ROOT_DIR', dirname(__DIR__));

require_once(ROOT_DIR . '/config/config.php');
require_once(ROOT_DIR . '/controllers/AuthController.php'); 

use Controllers\AuthController;
$authController = new AuthController(true); 

if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => 'Invalid Request']; 

    switch ($_POST['action']) {
        case 'register': $response = $authController->register($_POST); break;
        case 'login': $response = $authController->login($_POST); break;
        case 'logout': $response = $authController->logout(); break;
        case 'updatePassword': $response = $authController->updatePassword($_POST); break;
        case 'create_payment_invoice': $response = $authController->createNowPaymentsInvoice($_POST); break;
        case 'get_payment_status': $response = $authController->getPaymentStatus($_POST); break; 
        case 'cancel_payment': $response = $authController->cancelPayment($_POST); break; 
        case 'withdraw_tokens': $response = $authController->withdrawTokens($_POST); break;
        case 'get_balance': $response = $authController->getBalance(); break; 
        case 'get_detailed_balance': $response = $authController->getDetailedBalance(); break; 
        case 'get_referral_history': $response = $authController->getReferralHistory(); break;
        case 'get_transaction_history': $response = $authController->getTransactionHistory(); break;
        case 'get_user_details': $response = $authController->getUserDetails(); break;
        default:
            error_log("Unknown user AJAX Action: " . ($_POST['action'] ?? 'N/A'));
            break;
    }
    echo json_encode($response);
    exit;
}

$is_logged_in = isset($_SESSION['user_id']);
$current_username = $_SESSION['username'] ?? 'Guest';
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
            --primary: #9945FF; --primary-light: #a855f7; 
            --primary-glow: rgba(153, 69, 255, 0.5);
            --secondary: #14F195; --secondary-glow: rgba(20, 241, 149, 0.4);
            --dark-bg: #0a0a0f; 
            --card-bg: rgba(20, 20, 30, 0.7); 
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --border-color: rgba(153, 69, 255, 0.2); 
            --border-color-light: rgba(255, 255, 255, 0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-family: var(--font-body); }
        body { font-family: var(--font-body); background: var(--dark-bg); color: #e5e7eb; overflow-x: hidden; position: relative; }
        .font-heading { font-family: var(--font-heading); }
        .animated-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; pointer-events: none; }
        .gradient-orb { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.15; animation: float 25s infinite ease-in-out alternate; }
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, var(--primary), transparent 70%); top: -15%; right: -15%; animation-duration: 30s; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, var(--secondary), transparent 70%); bottom: -15%; left: -15%; animation-delay: -12s; }
        .orb-3 { width: 400px; height: 400px; background: radial-gradient(circle, #6366f1, transparent 70%); top: 40%; left: 45%; animation-delay: -7s; animation-duration: 35s; }
        @keyframes float { 0% { transform: translate(0, 0) scale(1); } 50% { transform: translate(80px, -60px) scale(1.15); } 100% { transform: translate(-40px, 40px) scale(0.9); } }
        .grid-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-image: linear-gradient(var(--border-color) 1px, transparent 1px), linear-gradient(90deg, var(--border-color) 1px, transparent 1px); background-size: 60px 60px; opacity: 0.2; z-index: -1; pointer-events: none; }
        .content-wrapper { position: relative; z-index: 2; opacity: 1; transition: opacity 0.5s ease-out; } 
        .container { max-width: 1280px; margin: 0 auto; padding: 0 1rem; }
        @media (min-width: 768px) { .container { padding: 0 2rem; } }
        header { background: rgba(10, 10, 15, 0.7); backdrop-filter: blur(25px); border-bottom: 1px solid var(--border-color); box-shadow: 0 2px 10px rgba(153, 69, 255, 0.05); }
        .logo-text { font-family: var(--font-heading); font-weight: 700; font-size: 1.75rem; color: #fff; }
        .nav-link { position: relative; font-size: 0.9rem; font-weight: 500; padding: 0.6rem 0.75rem; border-radius: 6px; transition: all 0.2s ease-in-out; color: #d1d5db; border-bottom: 2px solid transparent; margin-bottom: -1px; }
        .nav-link:hover { background-color: rgba(153, 69, 255, 0.08); color: #fff; }
        .nav-link.active-link { color: #fff; font-weight: 600; border-bottom-color: var(--primary); background-color: transparent; }
        #mobile-nav-menu { position: fixed; top: 0; right: 0; width: 100%; max-width: 320px; height: 100%; background: rgba(15, 15, 25, 0.98); backdrop-filter: blur(20px); z-index: 100; transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1); visibility: hidden; box-shadow: -10px 0 30px rgba(0,0,0,0.2); border-left: 1px solid var(--border-color); }
        #mobile-nav-menu.is-open { transform: translateX(0); visibility: visible; }
        .mobile-nav-link { font-size: 1.25rem; font-weight: 600; padding: 0.75rem 1.5rem; text-align: left; border-radius: 8px; transition: all 0.2s ease; color: #d1d5db; display: block; }
        .mobile-nav-link:hover { background-color: rgba(153, 69, 255, 0.1); color: #fff; }
        .mobile-nav-link.active-link { color: var(--primary-light); background-color: rgba(153, 69, 255, 0.15); font-weight: 700; }
        .btn-primary, .btn-secondary, .btn-danger { padding: 0.75rem 1.5rem; font-size: 0.9rem; font-weight: 600; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; text-align: center; display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; border: 2px solid transparent; }
        .btn-primary { background: linear-gradient(135deg, var(--secondary), #0ea770); color: #000; box-shadow: 0 4px 15px var(--secondary-glow); border-color: var(--secondary); }
        .btn-primary:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 6px 20px var(--secondary-glow);}
        .btn-secondary { background: transparent; color: #fff; border-color: rgba(153, 69, 255, 0.5); }
        .btn-secondary:hover { background-color: rgba(153, 69, 255, 0.1); border-color: var(--primary-light); }
        .btn-danger { background-color: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.4); color: #ef4444; }
        .btn-danger:hover { background-color: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.6); color: #f87171; }
        .hero-title { font-family: var(--font-heading); font-size: clamp(3rem, 8vw, 6.5rem); font-weight: 700; line-height: 1; letter-spacing: -3px; margin-bottom: 2rem; }
        .gradient-text { background: linear-gradient(135deg, #ffffff 0%, var(--primary) 50%, var(--secondary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: gradient-shift 8s ease infinite; background-size: 200% auto; }
        @keyframes gradient-shift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        .glass-card { background: var(--card-bg); backdrop-filter: blur(20px); border: 1px solid var(--border-color-light); border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0, 0.1); } 
        .input-group { position: relative; margin-bottom: 1.5rem; }
        .input-field { width: 100%; padding: 1rem 1.5rem; background: rgba(0, 0, 0, 0.3); border: 1px solid var(--border-color); border-radius: 12px; color: #fff; outline: none; transition: all 0.3s ease; font-size: 1rem; }
        .peer-label { position: absolute; left: 1.5rem; top: 1.25rem; color: rgba(255, 255, 255, 0.5); transition: all 0.3s ease; pointer-events: none; background: transparent; padding: 0 0.5rem; }
        .input-field:focus~.peer-label, .input-field:not(:placeholder-shown)~.peer-label { transform: translateY(-2.2rem) scale(0.85); color: var(--primary-light); background: #111827; padding: 0 0.5rem;}
        .input-field:focus { border-color: var(--primary-light); box-shadow: 0 0 0 3px rgba(153, 69, 255, 0.2); } 
        .spinner { display: inline-block; width: 20px; height: 20px; border: 3px solid rgba(255, 255, 255, 0.3); border-radius: 50%; border-top-color: #fff; animation: spin 0.8s linear infinite; } 
        @keyframes spin { to { transform: rotate(360deg); } }
        #toast-container { position: fixed; top: 2rem; right: 2rem; z-index: 1000; }
        .footer-grid { display: grid; grid-template-columns: 1fr; gap: 2rem; }
        @media (min-width: 768px) { .footer-grid { grid-template-columns: 2fr 1fr 1fr; } }
        #preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: var(--dark-bg); z-index: 10000; display: flex; flex-direction: column; align-items: center; justify-content: center; opacity: 1; transition: opacity 0.5s ease-out 0.2s; pointer-events: none; }
        #preloader .spinner { width: 40px; height: 40px; border: 4px solid rgba(153, 69, 255, 0.3); border-top-color: var(--primary-light); margin-bottom: 1rem; }
        #preloader p { color: var(--primary-light); font-size: 0.9rem; font-weight: 500; }
        body.loaded #preloader { opacity: 0; pointer-events: none; }
        body:not(.loaded) .content-wrapper { opacity: 0; }
        #loading-bar-container { position: fixed; top: 0; left: 0; width: 100%; height: 4px; background-color: transparent; z-index: 9999; pointer-events: none; opacity: 0; transition: opacity 0.3s ease; }
        #loading-bar { width: 100%; height: 100%; background: linear-gradient(90deg, transparent, var(--primary-light), var(--secondary), var(--primary-light), transparent); background-size: 200% 100%; box-shadow: 0 0 10px var(--primary-glow), 0 0 5px var(--secondary-glow); transition: opacity 0.3s ease-out; border-radius: 0 2px 2px 0; animation: loading-indeterminate 2s ease-in-out infinite; transform-origin: left; transform: scaleX(0); transition: transform 0.3s ease-out; }
        #loading-bar-container.active { opacity: 1; }
        #loading-bar-container.active #loading-bar.indeterminate { transform: scaleX(0.9); animation-play-state: running; }
        #loading-bar-container.finished #loading-bar { transform: scaleX(1); opacity: 0; transition: transform 0.2s ease-out, opacity 0.3s ease-in 0.1s; animation-play-state: paused; }
        #loading-bar.indeterminate-error { background: linear-gradient(90deg, #ef4444, #f87171) !important; animation: none !important; } /* Error style */
        @keyframes loading-indeterminate { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    </style>
</head>

<body class="min-h-screen antialiased">
    <div id="preloader">
        <div class="spinner"></div>
        <p>Loading PITHOS...</p>
    </div>

    <div id="loading-bar-container">
        <div id="loading-bar"></div>
    </div>

    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div> <div class="gradient-orb orb-2"></div> <div class="gradient-orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>
    <div id="toast-container"></div>

    <div class="content-wrapper min-h-screen flex flex-col">
        <header class="sticky top-0 z-50">
            <div class="container">
                <nav class="flex items-center justify-between py-3 h-16">
                    <a href="index.php" class="flex items-center gap-2 transition-opacity hover:opacity-80 flex-shrink-0">
                         <svg class="w-8 h-8 text-[var(--primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25L3 7.5m18 0v9l-9 5.25-9-5.25v-9m0 0L12 2.25l9 5.25M3 7.5l9 5.25l9-5.25" />
                        </svg>
                        <span class="logo-text">PITHOS</span>
                    </a>

                    <div class="hidden md:flex items-center justify-end flex-grow gap-2 ml-6">
                        <?php if ($is_logged_in): ?>
                            <?php
                                $nav_links = ['dashboard' => 'Dashboard', 'wallet' => 'Wallet', 'referrals' => 'Referrals', 'profile' => 'Profile'];
                                foreach ($nav_links as $link_page => $link_text) {
                                    $is_active = ($page === $link_page);
                                    $class = 'nav-link ' . ($is_active ? 'active-link' : '');
                                    echo '<a href="index.php?p=' . $link_page . '" class="' . $class . '">' . $link_text . '</a>';
                                }
                            ?>
                            <div class="w-px h-6 bg-[var(--border-color)] mx-3"></div>
                            <div class="flex items-center gap-2 mr-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 via-cyan-500 to-purple-500 p-0.5">
                                    <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center">
                                         <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-300">Hello, <span class="font-semibold text-violet-400"><?php echo htmlspecialchars($current_username); ?></span></span>
                            </div>
                            <button id="logout-btn" class="btn-danger !py-2 !px-3 !text-sm">Logout</button>
                        
                        <?php else: ?>
                            <a href="#auth" onclick="showAuthForm('login'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="nav-link">Sign In</a>
                            <a href="#auth" onclick="showAuthForm('register'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="btn-primary !py-2 !px-4 !text-sm ml-2">Get Started</a>
                        <?php endif; ?>
                    </div>

                    <button id="mobile-nav-toggle" class="md:hidden text-gray-300 hover:text-white p-2 rounded-lg transition-colors hover:bg-white/10">
                         <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                    </button>
                </nav>
            </div>
        </header>

        <div id="mobile-nav-menu">
            <div class="h-full flex flex-col pt-16 relative">
                 <button id="mobile-nav-close" class="absolute top-4 right-4 text-gray-400 hover:text-white p-2 rounded-lg transition-colors hover:bg-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <div class="px-6 py-8 flex flex-col space-y-3 flex-grow">
                     <?php if ($is_logged_in): ?>
                        <div class="flex items-center gap-3 border-b border-[var(--border-color)] pb-4 mb-4">
                             <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 via-cyan-500 to-purple-500 p-0.5">
                                <div class="w-full h-full rounded-full bg-gray-800 flex items-center justify-center">
                                     <svg class="w-5 h-5 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            </div>
                            <span class="text-base text-violet-400 font-semibold">Hello, <?php echo htmlspecialchars($current_username); ?></span>
                        </div>
                        <?php
                            $nav_links = ['dashboard' => 'Dashboard', 'wallet' => 'Wallet', 'referrals' => 'Referrals', 'profile' => 'Profile'];
                            foreach ($nav_links as $link_page => $link_text) {
                                $is_active = ($page === $link_page);
                                $class = 'mobile-nav-link ' . ($is_active ? 'active-link' : '');
                                echo '<a href="index.php?p=' . $link_page . '" class="' . $class . '">' . $link_text . '</a>';
                            }
                        ?>
                        <div class="mt-auto pt-6"> 
                            <button id="logout-btn-mobile" class="btn-danger w-full !text-base !py-3">Logout</button>
                        </div>
                     
                     <?php else: ?>
                        <a href="#auth" onclick="showAuthForm('login'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="mobile-nav-link">Sign In</a>
                        <a href="#auth" onclick="showAuthForm('register'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="btn-primary w-full !text-base !py-3 mt-4">Get Started</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <main class="flex-grow">
            <?php if ($is_logged_in): ?>
                <div class="py-10 md:py-16"> 
                <?php
                    switch ($page) {
                        case 'profile': require_once(ROOT_DIR . '/views/profile.php'); break;
                        case 'wallet': require_once(ROOT_DIR . '/views/wallet.php'); break;
                        case 'referrals': require_once(ROOT_DIR . '/views/referrals.php'); break;
                        case 'dashboard': default: require_once(ROOT_DIR . '/views/dashboard.php'); break;
                    }
                ?>
                </div>
            <?php else: ?>
                <?php require_once(ROOT_DIR . '/views/home.php'); ?>
            <?php endif; ?>
        </main>

        <footer class="mt-auto py-10 bg-gray-900/50 border-t border-[var(--border-color-light)]"> 
             <div class="container">
                <div class="footer-grid">
                    <div>
                        <h3 class="font-bold text-lg mb-4 text-violet-400 font-heading">PITHOS Protocol</h3>
                        <p class="text-sm text-gray-400">Building the immutable digital foundation on the Solana ecosystem.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-violet-400 font-heading">Resources</h4>
                        <ul class="space-y-2 text-sm"><li><a href="#" class="text-gray-400 hover:text-white transition-colors">Whitepaper</a></li><li><a href="#" class="text-gray-400 hover:text-white transition-colors">Tokenomics</a></li></ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-violet-400 font-heading">Community</h4>
                        <ul class="space-y-2 text-sm"><li><a href="#" class="text-gray-400 hover:text-white transition-colors">Twitter (X)</a></li><li><a href="#" class="text-gray-400 hover:text-white transition-colors">Telegram</a></li></ul>
                    </div>
                </div>
                <div class="text-center pt-8 mt-8 border-t border-[var(--border-color-light)]"> 
                    <p class="text-gray-500 text-sm">&copy; <?php echo date("Y"); ?> PITHOS Protocol. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/main.js"></script>
    <?php
        if ($is_logged_in) {
            switch($page) {
                case 'dashboard': echo '<script src="assets/dashboard.js"></script>'; break;
                case 'profile': echo '<script src="assets/profile.js"></script>'; break;
                case 'wallet': echo '<script src="assets/wallet.js"></script>'; break;
                case 'referrals': echo '<script src="assets/referrals.js"></script>'; break;
            }
        }
    ?>
    <script>
        $(document).ready(function() {
            // --- Hide Pre-loader ---
             $('body').addClass('loaded'); 

            // --- AJAX & Navigation Loading Bar ---
            const loadingBarContainer = $('#loading-bar-container');
            const loadingBar = $('#loading-bar');
            let ajaxRequestCount = 0;
            let navigationTimeout; 

            function startLoadingBar() {
                clearTimeout(navigationTimeout); 
                loadingBarContainer.removeClass('finished').addClass('active');
                loadingBar.removeClass('indeterminate-error').addClass('indeterminate'); // Use indeterminate class
                loadingBar.css({ 'transform': 'scaleX(0)', 'opacity': '1', 'transition': 'transform 0.3s ease-out' }); 
                setTimeout(() => { loadingBar.css('transform', 'scaleX(0.9)'); }, 10); 
            }

            function finishLoadingBar(isError = false) {
                 clearTimeout(navigationTimeout); 
                 loadingBar.removeClass('indeterminate'); 

                 if(isError) {
                    loadingBar.addClass('indeterminate-error'); // Use CSS for error style
                    loadingBar.css({
                        'transition': 'transform 0.1s linear, opacity 0.3s ease-in 0.1s' 
                    });
                 } else {
                     loadingBar.css({
                        'transition': 'transform 0.2s ease-out, opacity 0.3s ease-in 0.1s' 
                    });
                 }
                
                loadingBar.css('transform', 'scaleX(1)'); 
                loadingBarContainer.addClass('finished'); 

                navigationTimeout = setTimeout(() => { 
                    loadingBarContainer.removeClass('active finished');
                    loadingBar.removeClass('indeterminate-error'); 
                    loadingBar.css({ 'transform': 'scaleX(0)', 'opacity': '1'}); 
                }, 500); 
            }
            
            // --- Global AJAX Handlers ---
            $(document).ajaxStart(function() {
                ajaxRequestCount++;
                if (ajaxRequestCount === 1) { 
                    startLoadingBar();
                }
            });
            $(document).ajaxStop(function() {
                ajaxRequestCount--;
                if (ajaxRequestCount <= 0) { 
                    ajaxRequestCount = 0; 
                    finishLoadingBar();
                }
            });
            $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
                 console.error("AJAX Error:", thrownError, settings.url);
                 ajaxRequestCount--; 
                 if (ajaxRequestCount <= 0) { 
                     ajaxRequestCount = 0;
                     finishLoadingBar(true); 
                 }
            });

            // --- Page Navigation Loading Bar Trigger ---
            $('header a[href^="index.php?p="]:not([href*="#"]), header a[href="index.php"], #mobile-nav-menu a[href^="index.php?p="]:not([href*="#"])').on('click', function(e) {
                if ($(this).hasClass('active-link') || $(this).attr('target') === '_blank') {
                    return; 
                }
                if (ajaxRequestCount === 0) { 
                    startLoadingBar();
                }
            });
             $(window).on('pageshow', function(event) {
                if (event.originalEvent.persisted) {
                     if(ajaxRequestCount <= 0) { 
                        ajaxRequestCount = 0;
                        clearTimeout(navigationTimeout); 
                        loadingBarContainer.removeClass('active finished');
                        loadingBar.removeClass('indeterminate indeterminate-error').css({ 'transform': 'scaleX(0)', 'opacity': '1'});
                    }
                }
            });

            // --- Mobile Menu Toggle ---
            const mobileMenu = $('#mobile-nav-menu');
            const openBtn = $('#mobile-nav-toggle');
            const closeBtn = $('#mobile-nav-close');
            const body = $('body');
            function openMenu() { mobileMenu.addClass('is-open'); body.addClass('overflow-hidden'); }
            function closeMenu() { mobileMenu.removeClass('is-open'); body.removeClass('overflow-hidden');}
            openBtn.on('click', openMenu);
            closeBtn.on('click', closeMenu);
            $('#mobile-nav-menu a, #mobile-nav-menu button').on('click', function(e) {
                const href = $(this).attr('href');
                const isLogout = this.id === 'logout-btn-mobile';
                const isHashLink = href && href.startsWith('#') && href.length > 1;
                if (!isLogout && !isHashLink) { /* Let navigation proceed */ } 
                else if (isLogout) { closeMenu(); } 
                else { closeMenu(); }
            });

            // --- Logout Button Sync ---
            $('#logout-btn-mobile').on('click', function(e) {
                e.preventDefault();
                closeMenu(); 
                 if (ajaxRequestCount === 0) startLoadingBar(); 
                $('#logout-btn').click(); 
            });

            // --- Smooth Scroll for Anchors ---
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                 anchor.addEventListener('click', function(e) {
                     const targetId = this.getAttribute('href');
                     if (targetId.startsWith('#') && targetId.length > 1) { 
                         const targetElement = document.querySelector(targetId);
                         if (targetElement) {
                             e.preventDefault();
                             closeMenu(); 
                             setTimeout(() => { targetElement.scrollIntoView({ behavior: 'smooth' }); }, 50); 
                         }
                     }
                 });
             });

            // --- Auth Form Toggle ---
            window.showAuthForm = function(type) {
                const loginTab = $('#tab-login');
                const registerTab = $('#tab-register');
                const loginForm = $('#form-login');
                const registerForm = $('#form-register');
                const forgotForm = $('#form-forgot'); 
                loginForm.addClass('hidden');
                registerForm.addClass('hidden');
                if(forgotForm.length) forgotForm.addClass('hidden'); 
                const activeClasses = 'bg-gradient-to-r from-purple-600 to-cyan-600 text-white shadow-md';
                const inactiveClasses = 'text-gray-400 hover:text-white hover:bg-white/5';
                loginTab.removeClass(activeClasses).addClass(inactiveClasses);
                registerTab.removeClass(activeClasses).addClass(inactiveClasses);
                $('#login-message').text(''); $('#register-message').text('');
                if ($('#forgot-message').length) $('#forgot-message').text('');
                if (type === 'login') {
                    loginForm.removeClass('hidden');
                    loginTab.removeClass(inactiveClasses).addClass(activeClasses);
                } else if (type === 'register') {
                    registerForm.removeClass('hidden');
                    registerTab.removeClass(activeClasses).addClass(inactiveClasses);
                    if($('#otp-section').length) $('#otp-section').addClass('hidden').hide(); 
                    if($('#send-otp-btn').length) $('#send-otp-btn').removeClass('hidden');
                    if($('#register-btn').length) {
                         if ($('#send-otp-btn').length) { $('#register-btn').addClass('hidden').removeClass('flex'); } 
                         else { $('#register-btn').removeClass('hidden').addClass('flex'); }
                    }
                }
            }
        }); // End Document Ready
    </script>
</body>
</html>

