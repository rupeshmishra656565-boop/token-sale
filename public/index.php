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
    <title>PITHOS Protocol | The Immutable Foundation for Digital Assets</title>
    <meta name="description" content="Join PITHOS Protocol - The most secure, immutable token on Solana with permanently locked contracts and zero rug risk.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #9945FF; 
            --primary-light: #b565ff; 
            --primary-dark: #7a35cc;
            --primary-glow: rgba(153, 69, 255, 0.6);
            --secondary: #14F195; 
            --secondary-glow: rgba(20, 241, 149, 0.5);
            --dark-bg: #0a0a0f; 
            --card-bg: rgba(20, 20, 30, 0.85); 
            --font-heading: 'Space Grotesk', sans-serif;
            --font-body: 'Inter', sans-serif;
            --border-color: rgba(153, 69, 255, 0.25); 
            --border-color-light: rgba(255, 255, 255, 0.08);
            --shadow-primary: 0 25px 50px -12px rgba(153, 69, 255, 0.25);
            --shadow-secondary: 0 25px 50px -12px rgba(20, 241, 149, 0.25);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { font-family: var(--font-body); scroll-behavior: smooth; }
        body { 
            font-family: var(--font-body); 
            background: var(--dark-bg); 
            color: #e5e7eb; 
            overflow-x: hidden; 
            position: relative;
            letter-spacing: -0.01em;
        }
        
        .font-heading { font-family: var(--font-heading); letter-spacing: -0.03em; }
        
        .animated-bg { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            z-index: -1; overflow: hidden; pointer-events: none; 
        }
        .gradient-orb { 
            position: absolute; border-radius: 50%; 
            filter: blur(120px); opacity: 0.2; 
            animation: float 30s infinite ease-in-out alternate; 
            will-change: transform;
        }
        .orb-1 { width: 800px; height: 800px; background: radial-gradient(circle, var(--primary), transparent 60%); top: -20%; right: -20%; animation-duration: 35s; }
        .orb-2 { width: 700px; height: 700px; background: radial-gradient(circle, var(--secondary), transparent 60%); bottom: -20%; left: -20%; animation-delay: -15s; animation-duration: 40s; }
        .orb-3 { width: 600px; height: 600px; background: radial-gradient(circle, #6366f1, transparent 60%); top: 40%; left: 50%; animation-delay: -8s; animation-duration: 45s; }
        @keyframes float { 
            0% { transform: translate(0, 0) scale(1) rotate(0deg); } 
            33% { transform: translate(120px, -80px) scale(1.1) rotate(120deg); } 
            66% { transform: translate(-80px, 60px) scale(0.95) rotate(240deg); } 
            100% { transform: translate(40px, -40px) scale(1.05) rotate(360deg); } 
        }
        
        .grid-overlay { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background-image: linear-gradient(var(--border-color) 1px, transparent 1px), linear-gradient(90deg, var(--border-color) 1px, transparent 1px); 
            background-size: 80px 80px; opacity: 0.15; z-index: -1; pointer-events: none; 
            mask-image: radial-gradient(ellipse 80% 70% at 50% 50%, black 40%, transparent 100%);
        }
        
        .content-wrapper { position: relative; z-index: 2; opacity: 1; transition: opacity 0.5s ease-out; }
        .container { max-width: 1400px; margin: 0 auto; padding: 0 1.25rem; }
        @media (min-width: 768px) { .container { padding: 0 2.5rem; } }
        
        header { 
            background: rgba(10, 10, 15, 0.8); backdrop-filter: blur(30px) saturate(150%); 
            border-bottom: 1px solid var(--border-color); 
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(153, 69, 255, 0.1) inset;
            transition: all 0.3s ease;
        }
        header.scrolled {
            background: rgba(10, 10, 15, 0.95);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(153, 69, 255, 0.15) inset;
        }
        .logo-text { 
            font-family: var(--font-heading); font-weight: 800; font-size: 1.85rem; 
            background: linear-gradient(135deg, #fff 0%, var(--primary-light) 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            letter-spacing: -0.02em; text-shadow: 0 0 30px rgba(153, 69, 255, 0.3);
        }
        .nav-link { 
            position: relative; font-size: 0.95rem; font-weight: 500; 
            padding: 0.7rem 1rem; border-radius: 8px; 
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); 
            color: #d1d5db; border-bottom: 2px solid transparent; margin-bottom: -1px;
        }
        .nav-link:hover { 
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.1), rgba(153, 69, 255, 0.05));
            color: #fff; transform: translateY(-1px);
        }
        .nav-link.active-link { 
            color: #fff; font-weight: 600; 
            border-bottom-color: var(--primary); 
            background: rgba(153, 69, 255, 0.08);
        }
        
        #mobile-nav-menu { 
            position: fixed; top: 0; right: 0; width: 100%; max-width: 360px; height: 100%; 
            background: rgba(15, 15, 25, 0.98); backdrop-filter: blur(30px) saturate(150%); 
            z-index: 100; transform: translateX(100%); 
            transition: transform 0.4s cubic-bezier(0.25, 0.8, 0.25, 1); 
            visibility: hidden; box-shadow: -15px 0 40px rgba(0, 0, 0, 0.4); 
            border-left: 1px solid var(--border-color); 
        }
        #mobile-nav-menu.is-open { transform: translateX(0); visibility: visible; }
        .mobile-nav-link { 
            font-size: 1.25rem; font-weight: 600; padding: 1rem 1.5rem; 
            text-align: left; border-radius: 10px; transition: all 0.25s ease; 
            color: #d1d5db; display: block; 
        }
        .mobile-nav-link:hover { 
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.15), rgba(153, 69, 255, 0.08));
            color: #fff; transform: translateX(4px);
        }
        .mobile-nav-link.active-link { 
            color: var(--primary-light); 
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.2), rgba(153, 69, 255, 0.1));
            font-weight: 700; 
        }
        
        .btn-primary, .btn-secondary, .btn-danger { 
            padding: 0.85rem 1.75rem; font-size: 0.95rem; font-weight: 600; 
            border-radius: 12px; cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            text-align: center; display: inline-flex; align-items: center; 
            justify-content: center; gap: 0.5rem; border: 2px solid transparent; 
            position: relative; overflow: hidden;
        }
        .btn-primary::before { 
            content: ''; position: absolute; top: 50%; left: 50%; 
            width: 0; height: 0; border-radius: 50%; 
            background: rgba(255, 255, 255, 0.2); 
            transform: translate(-50%, -50%); transition: width 0.6s, height 0.6s;
        }
        .btn-primary:hover::before { width: 300px; height: 300px; }
        .btn-primary { 
            background: linear-gradient(135deg, var(--secondary) 0%, #0ea770 100%); 
            color: #000; box-shadow: var(--shadow-secondary), 0 0 0 1px rgba(20, 241, 149, 0.3) inset;
            border-color: var(--secondary); 
        }
        .btn-primary:hover { 
            transform: translateY(-3px) scale(1.02); 
            box-shadow: 0 30px 60px -15px rgba(20, 241, 149, 0.4), 0 0 0 1px rgba(20, 241, 149, 0.4) inset;
        }
        .btn-secondary { 
            background: transparent; color: #fff; 
            border-color: rgba(153, 69, 255, 0.6); backdrop-filter: blur(10px);
        }
        .btn-secondary:hover { 
            background: linear-gradient(135deg, rgba(153, 69, 255, 0.15), rgba(153, 69, 255, 0.08));
            border-color: var(--primary-light); transform: translateY(-2px);
            box-shadow: 0 10px 30px -10px rgba(153, 69, 255, 0.4);
        }
        .btn-danger { 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.08));
            border-color: rgba(239, 68, 68, 0.5); color: #ef4444; 
        }
        .btn-danger:hover { 
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(239, 68, 68, 0.15));
            border-color: rgba(239, 68, 68, 0.7); color: #f87171; 
            transform: translateY(-2px);
        }
        
        .hero-title { 
            font-family: var(--font-heading); font-size: clamp(3rem, 8vw, 7rem); 
            font-weight: 800; line-height: 0.95; letter-spacing: -0.04em; 
            margin-bottom: 2rem; 
        }
        .gradient-text { 
            background: linear-gradient(135deg, #ffffff 0%, var(--primary-light) 40%, var(--secondary) 100%); 
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; 
            animation: gradient-shift 8s ease infinite; background-size: 200% auto; 
            display: inline-block; position: relative;
        }
        @keyframes gradient-shift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
        
        .glass-card { 
            background: var(--card-bg); backdrop-filter: blur(25px) saturate(150%); 
            border: 1px solid var(--border-color-light); border-radius: 24px; 
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
            transition: all 0.3s ease;
        }
        .glass-card:hover { 
            border-color: var(--border-color); 
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(153, 69, 255, 0.2) inset;
        }
        
        .input-group { position: relative; margin-bottom: 1.75rem; }
        .input-field { 
            width: 100%; padding: 1.1rem 1.5rem; background: rgba(0, 0, 0, 0.4); 
            border: 2px solid rgba(153, 69, 255, 0.3); border-radius: 14px; 
            color: #fff; outline: none; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-size: 1rem; 
        }
        .peer-label { 
            position: absolute; left: 1.5rem; top: 1.35rem; 
            color: rgba(255, 255, 255, 0.5); 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            pointer-events: none; background: transparent; padding: 0 0.5rem; font-weight: 500;
        }
        .input-field:focus~.peer-label, .input-field:not(:placeholder-shown)~.peer-label { 
            transform: translateY(-2.5rem) scale(0.85); color: var(--primary-light); 
            background: #0a0a0f; padding: 0 0.6rem;
        }
        .input-field:focus { 
            border-color: var(--primary-light); 
            box-shadow: 0 0 0 4px rgba(153, 69, 255, 0.15), 0 0 30px rgba(153, 69, 255, 0.2);
            background: rgba(0, 0, 0, 0.5);
        } 
        
        .spinner { 
            display: inline-block; width: 20px; height: 20px; 
            border: 3px solid rgba(255, 255, 255, 0.2); border-radius: 50%; 
            border-top-color: #fff; animation: spin 0.7s linear infinite; 
        } 
        @keyframes spin { to { transform: rotate(360deg); } }
        
        #toast-container { position: fixed; top: 2rem; right: 2rem; z-index: 1000; }
        .footer-grid { display: grid; grid-template-columns: 1fr; gap: 2.5rem; }
        @media (min-width: 768px) { .footer-grid { grid-template-columns: 2fr 1fr 1fr; } }
        
        #preloader { 
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: linear-gradient(135deg, var(--dark-bg) 0%, #0f0f18 100%);
            z-index: 10000; display: flex; flex-direction: column; 
            align-items: center; justify-content: center; opacity: 1; 
            transition: opacity 0.6s ease-out 0.3s; pointer-events: none; 
        }
        #preloader .spinner { 
            width: 50px; height: 50px; border: 4px solid rgba(153, 69, 255, 0.2); 
            border-top-color: var(--primary-light); margin-bottom: 1.5rem; 
        }
        #preloader p { color: var(--primary-light); font-size: 1rem; font-weight: 600; letter-spacing: 0.05em;}
        body.loaded #preloader { opacity: 0; pointer-events: none; }
        body:not(.loaded) .content-wrapper { opacity: 0; }
        
        #loading-bar-container { 
            position: fixed; top: 0; left: 0; width: 100%; height: 3px; 
            background-color: transparent; z-index: 9999; pointer-events: none; 
            opacity: 0; transition: opacity 0.3s ease; 
        }
        #loading-bar { 
            width: 100%; height: 100%; 
            background: linear-gradient(90deg, transparent, var(--primary-light), var(--secondary), var(--primary-light), transparent); 
            background-size: 200% 100%; 
            box-shadow: 0 0 15px var(--primary-glow), 0 0 8px var(--secondary-glow);
            transition: opacity 0.3s ease-out; border-radius: 0 2px 2px 0; 
            animation: loading-indeterminate 2s ease-in-out infinite; 
            transform-origin: left; transform: scaleX(0); 
            transition: transform 0.3s ease-out; 
        }
        #loading-bar-container.active { opacity: 1; }
        #loading-bar-container.active #loading-bar.indeterminate { transform: scaleX(0.9); animation-play-state: running; }
        #loading-bar-container.finished #loading-bar { 
            transform: scaleX(1); opacity: 0; 
            transition: transform 0.2s ease-out, opacity 0.3s ease-in 0.1s; 
            animation-play-state: paused; 
        }
        #loading-bar.indeterminate-error { 
            background: linear-gradient(90deg, #ef4444, #f87171) !important; 
            animation: none !important; 
        }
        @keyframes loading-indeterminate { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
        
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
        
        .glow-primary { box-shadow: 0 0 20px rgba(153, 69, 255, 0.3), 0 0 40px rgba(153, 69, 255, 0.2); }
        .glow-secondary { box-shadow: 0 0 20px rgba(20, 241, 149, 0.3), 0 0 40px rgba(20, 241, 149, 0.2); }
        
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: rgba(0, 0, 0, 0.3); }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg, var(--primary), var(--secondary)); border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, var(--primary-light), var(--secondary)); }
    </style>
</head>

<body class="min-h-screen antialiased">
    <div id="preloader">
        <div class="spinner"></div>
        <p>Loading PITHOS Protocol...</p>
    </div>

    <div id="loading-bar-container">
        <div id="loading-bar"></div>
    </div>

    <div class="animated-bg">
        <div class="gradient-orb orb-1"></div> 
        <div class="gradient-orb orb-2"></div> 
        <div class="gradient-orb orb-3"></div>
    </div>
    <div class="grid-overlay"></div>
    <div id="toast-container"></div>

    <div class="content-wrapper min-h-screen flex flex-col">
        <header class="sticky top-0 z-50" id="main-header">
            <div class="container">
                <nav class="flex items-center justify-between py-3.5 h-16">
                    <a href="index.php" class="flex items-center gap-2.5 transition-all duration-300 hover:opacity-80 hover:scale-105 flex-shrink-0"> 
                         <svg class="w-9 h-9 text-[var(--primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25L3 7.5m18 0v9l-9 5.25-9-5.25v-9m0 0L12 2.25l9 5.25M3 7.5l9 5.25l9-5.25" />
                        </svg>
                        <span class="logo-text">PITHOS</span>
                    </a>
                    
                    <div class="hidden md:flex items-center justify-end flex-grow gap-1 ml-8">
                        <?php 
                            $base_nav_links = ['home' => 'Home', 'tokenomics' => 'Tokenomics']; 
                            $logged_in_links = ['dashboard' => 'Dashboard', 'wallet' => 'Wallet', 'referrals' => 'Referrals', 'profile' => 'Profile'];
                            
                            $current_nav_links = $is_logged_in ? array_merge($base_nav_links, $logged_in_links) : $base_nav_links;

                            if (!$is_logged_in && $page === 'home') {
                                unset($current_nav_links['home']); 
                            }
                            if ($is_logged_in && isset($current_nav_links['home'])) {
                                $current_nav_links['home'] = ['text' => 'Home', 'url' => 'index.php']; 
                            } else if (isset($current_nav_links['home'])) {
                                 $current_nav_links['home'] = ['text' => 'Home', 'url' => 'index.php'];
                            }
                        
                            foreach ($current_nav_links as $link_page => $link_data) {
                                $link_text = is_array($link_data) ? $link_data['text'] : $link_data;
                                $link_url = is_array($link_data) ? $link_data['url'] : 'index.php?p=' . $link_page;

                                $is_active = ($page === $link_page) || 
                                             ($page === 'dashboard' && $link_page === 'home' && $is_logged_in) || 
                                             ($page === 'home' && $link_page === 'home' && !$is_logged_in) ||
                                             ($page === 'tokenomics' && $link_page === 'tokenomics'); 

                                $class = 'nav-link ' . ($is_active ? 'active-link' : '');
                                echo '<a href="' . $link_url . '" class="' . $class . '">' . $link_text . '</a>';
                            }
                        ?>

                        <?php if ($is_logged_in): ?>
                            <div class="w-px h-6 bg-[var(--border-color)] mx-3"></div>
                            <div class="flex items-center gap-2.5 mr-3">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 via-cyan-500 to-purple-500 p-0.5 shadow-lg glow-primary">
                                    <div class="w-full h-full rounded-full bg-gray-900 flex items-center justify-center">
                                         <svg class="w-4 h-4 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-300">Hey, <span class="font-semibold text-violet-400"><?php echo htmlspecialchars($current_username); ?></span></span>
                            </div>
                            <button id="logout-btn" class="btn-danger !py-2 !px-4 !text-sm">Logout</button>
                        <?php else: ?>
                            <a href="#auth" onclick="showAuthForm('login'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="nav-link ml-4">Sign In</a>
                            <a href="#auth" onclick="showAuthForm('register'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="btn-primary !py-2.5 !px-5 !text-sm ml-3">Get Started</a>
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
                            foreach ($current_nav_links as $link_page => $link_data) {
                                $link_text = is_array($link_data) ? $link_data['text'] : $link_data;
                                $link_url = is_array($link_data) ? $link_data['url'] : 'index.php?p=' . $link_page;
                                $is_active = ($page === $link_page) || 
                                             ($page === 'dashboard' && $link_page === 'home' && $is_logged_in) || 
                                             ($page === 'home' && $link_page === 'home' && !$is_logged_in) ||
                                             ($page === 'tokenomics' && $link_page === 'tokenomics');

                                $class = 'mobile-nav-link ' . ($is_active ? 'active-link' : '');
                                echo '<a href="' . $link_url . '" class="' . $class . '">' . $link_text . '</a>';
                            }
                        ?>
                        <div class="mt-auto pt-6"> 
                            <button id="logout-btn-mobile" class="btn-danger w-full !text-base !py-3">Logout</button>
                        </div>
                     
                     <?php else: ?>
                         <a href="index.php" class="mobile-nav-link <?php echo ($page === 'home' ? 'active-link' : ''); ?>">Home</a>
                         <a href="index.php?p=tokenomics" class="mobile-nav-link <?php echo ($page === 'tokenomics' ? 'active-link' : ''); ?>">Tokenomics</a>
                         <div class="mt-auto pt-6 space-y-4">
                            <a href="#auth" onclick="showAuthForm('login'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="mobile-nav-link text-center border border-purple-500/50">Sign In</a>
                            <a href="#auth" onclick="showAuthForm('register'); event.preventDefault(); document.querySelector('#auth').scrollIntoView({ behavior: 'smooth' });" class="btn-primary w-full !text-base !py-3">Get Started</a>
                         </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <main class="flex-grow">
             <div class="py-10 md:py-16"> 
                <?php
                    switch ($page) {
                        case 'profile': 
                            if ($is_logged_in) require_once(ROOT_DIR . '/views/profile.php'); 
                            else require_once(ROOT_DIR . '/views/home.php'); 
                            break;
                        case 'wallet': 
                             if ($is_logged_in) require_once(ROOT_DIR . '/views/wallet.php'); 
                             else require_once(ROOT_DIR . '/views/home.php');
                             break;
                        case 'referrals': 
                            if ($is_logged_in) require_once(ROOT_DIR . '/views/referrals.php'); 
                            else require_once(ROOT_DIR . '/views/home.php');
                            break;
                        case 'dashboard': 
                            if ($is_logged_in) require_once(ROOT_DIR . '/views/dashboard.php'); 
                            else require_once(ROOT_DIR . '/views/home.php');
                            break;
                        case 'tokenomics': 
                             require_once(ROOT_DIR . '/views/tokenomics.php'); 
                             break;
                        case 'home': 
                        default: 
                            if ($is_logged_in && $page === 'home') {
                                require_once(ROOT_DIR . '/views/dashboard.php');
                            } else {
                                require_once(ROOT_DIR . '/views/home.php'); 
                            }
                            break;
                    }
                ?>
            </div>
        </main>

        <footer class="mt-auto py-12 bg-gradient-to-b from-transparent via-gray-900/30 to-gray-900/60 border-t border-[var(--border-color-light)]"> 
             <div class="container">
                <div class="footer-grid">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-8 h-8 text-[var(--primary)]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25L3 7.5m18 0v9l-9 5.25-9-5.25v-9m0 0L12 2.25l9 5.25M3 7.5l9 5.25l9-5.25" />
                            </svg>
                            <h3 class="font-bold text-xl text-white font-heading">PITHOS Protocol</h3>
                        </div>
                        <p class="text-sm text-gray-400 leading-relaxed mb-4">Building the immutable digital foundation on the Solana ecosystem. Trust guaranteed, yield optimized.</p>
                        <div class="flex gap-3">
                            <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/50 flex items-center justify-center transition-all duration-300 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/50 flex items-center justify-center transition-all duration-300 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.14.18-.357.295-.6.295-.002 0-.003 0-.005 0l.213-3.054 5.56-5.022c.24-.213-.054-.334-.373-.121l-6.869 4.326-2.96-.924c-.64-.203-.658-.64.135-.954l11.566-4.458c.538-.196 1.006.128.832.941z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10 hover:border-purple-500/50 flex items-center justify-center transition-all duration-300 group">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22C6.486 22 2 17.514 2 12S6.486 2 12 2s10 4.486 10 10-4.486 10-10 10zm1-15h-2v6h6v-2h-4V7z"/></svg>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-white font-heading">Resources</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="assets/PITHOS_Protocol_Whitepaper.pdf" download="PITHOS_Protocol_Whitepaper.pdf" target="_blank" class="text-gray-400 hover:text-purple-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-purple-400 transition-colors"></span>Whitepaper</a></li>
                            <li><a href="index.php?p=tokenomics" class="text-gray-400 hover:text-purple-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-purple-400 transition-colors"></span>Tokenomics</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-purple-400 transition-colors"></span>Documentation</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-purple-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-purple-400 transition-colors"></span>FAQs</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg mb-4 text-white font-heading">Community</h4>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="#" class="text-gray-400 hover:text-cyan-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-cyan-400 transition-colors"></span>Twitter (X)</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-cyan-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-cyan-400 transition-colors"></span>Telegram</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-cyan-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-cyan-400 transition-colors"></span>Discord</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-cyan-400 transition-colors flex items-center gap-2 group"><span class="w-1 h-1 bg-gray-600 rounded-full group-hover:bg-cyan-400 transition-colors"></span>GitHub</a></li>
                        </ul>
                    </div>
                </div>
                <div class="text-center pt-8 mt-10 border-t border-[var(--border-color-light)]"> 
                    <p class="text-gray-500 text-sm">&copy; <?php echo date("Y"); ?> PITHOS Protocol. All Rights Reserved. Built on Solana.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="assets/main.js"></script>
    <?php
        if ($is_logged_in) {
            switch($page) {
                case 'dashboard': echo '<script src="assets/dashboard.js"></script>'; break;
                case 'profile': echo '<script src="assetsIS/profile.js"></script>'; break;
                case 'wallet': echo '<script src="assets/wallet.js"></script>'; break;
                case 'referrals': echo '<script src="assets/referrals.js"></script>'; break;
            }
        }
    ?>
    <script>
        $(document).ready(function() {
             $('body').addClass('loaded'); 

            $(window).on('scroll', function() { if ($(window).scrollTop() > 50) { $('#main-header').addClass('scrolled'); } else { $('#main-header').removeClass('scrolled'); } });

            const loadingBarContainer = $('#loading-bar-container');
            const loadingBar = $('#loading-bar');
            let ajaxRequestCount = 0;
            let navigationTimeout; 

            function startLoadingBar() {
                clearTimeout(navigationTimeout); 
                loadingBarContainer.removeClass('finished').addClass('active');
                loadingBar.removeClass('indeterminate-error').addClass('indeterminate'); 
                loadingBar.css({ 'transform': 'scaleX(0)', 'opacity': '1', 'transition': 'transform 0.3s ease-out', 'background': '' }); 
                setTimeout(() => { loadingBar.css('transform', 'scaleX(0.9)'); }, 10); 
            }

            function finishLoadingBar(isError = false) {
                 clearTimeout(navigationTimeout); 
                 loadingBar.removeClass('indeterminate'); 
                 if(isError) {
                    loadingBar.addClass('indeterminate-error'); 
                    loadingBar.css({'transition': 'transform 0.1s linear, opacity 0.3s ease-in 0.1s' });
                 } else {
                     loadingBar.css({'transition': 'transform 0.2s ease-out, opacity 0.3s ease-in 0.1s' });
                 }
                loadingBar.css('transform', 'scaleX(1)'); 
                loadingBarContainer.addClass('finished'); 
                navigationTimeout = setTimeout(() => { 
                    loadingBarContainer.removeClass('active finished');
                    loadingBar.removeClass('indeterminate-error'); 
                    loadingBar.css({ 'transform': 'scaleX(0)', 'opacity': '1'}); 
                }, 500); 
            }
            
            $(document).ajaxStart(function() { ajaxRequestCount++; if (ajaxRequestCount === 1) startLoadingBar(); });
            $(document).ajaxStop(function() { ajaxRequestCount--; if (ajaxRequestCount <= 0) { ajaxRequestCount = 0; finishLoadingBar(); } });
            $(document).ajaxError(function(event, jqxhr, settings, thrownError) { console.error("AJAX Error:", thrownError, settings.url); ajaxRequestCount--; if (ajaxRequestCount <= 0) { ajaxRequestCount = 0; finishLoadingBar(true); } });

            $('header a[href^="index.php?p="]:not([href*="#"]):not([target="_blank"]), header a[href="index.php"], #mobile-nav-menu a[href^="index.php?p="]:not([href*="#"]):not([target="_blank"])').on('click', function(e) {
                if ($(this).hasClass('active-link')) return; 
                if (ajaxRequestCount === 0) startLoadingBar();
            });
             $(window).on('pageshow', function(event) {
                if (event.originalEvent.persisted) {
                     if(ajaxRequestCount <= 0) { 
                        ajaxRequestCount = 0; clearTimeout(navigationTimeout); 
                        loadingBarContainer.removeClass('active finished');
                        loadingBar.removeClass('indeterminate indeterminate-error').css({ 'transform': 'scaleX(0)', 'opacity': '1'});
                    }
                }
            });

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
                if (!isLogout && !isHashLink) { /* Nav */ } else if (isLogout) { closeMenu(); } else { closeMenu(); }
            });

            $('#logout-btn-mobile').on('click', function(e) { e.preventDefault(); closeMenu(); if (ajaxRequestCount === 0) startLoadingBar(); $('#logout-btn').click(); });

            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                 anchor.addEventListener('click', function(e) {
                     const targetId = this.getAttribute('href');
                     if (targetId.startsWith('#') && targetId.length > 1) { 
                         const targetElement = document.querySelector(targetId);
                         if (targetElement) { e.preventDefault(); closeMenu(); setTimeout(() => { targetElement.scrollIntoView({ behavior: 'smooth' }); }, 50); }
                     }
                 });
             });

            window.showAuthForm = function(type) {
                const loginTab = $('#tab-login'); const registerTab = $('#tab-register');
                const loginForm = $('#form-login'); const registerForm = $('#form-register');
                const forgotForm = $('#form-forgot'); 
                loginForm.addClass('hidden'); registerForm.addClass('hidden'); if(forgotForm.length) forgotForm.addClass('hidden'); 
                const activeClasses = 'bg-gradient-to-r from-purple-600 to-cyan-600 text-white shadow-md';
                const inactiveClasses = 'text-gray-400 hover:text-white hover:bg-white/5';
                loginTab.removeClass(activeClasses).addClass(inactiveClasses);
                registerTab.removeClass(activeClasses).addClass(inactiveClasses);
                $('#login-message').text(''); $('#register-message').text(''); if ($('#forgot-message').length) $('#forgot-message').text('');
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

            if ($('#form-login').length) { 
                 const urlParams = new URLSearchParams(window.location.search); 
                 const ref = urlParams.get('ref'); 
                 const loginParam = urlParams.get('login'); 
                 if (ref) { 
                    $('#referrer_id').val(ref); 
                    showAuthForm('register'); 
                 } else if (loginParam) { 
                    showAuthForm('login'); 
                 } else { 
                    showAuthForm('login'); 
                 }
            }

        }); 
    </script>
</body>
</html>