<?php
// Premium Auth Form Component - Used in home.php
?>
<div class="glass-card p-8 relative overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-40 h-40 bg-purple-500/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-0 left-0 w-40 h-40 bg-cyan-500/20 rounded-full blur-3xl"></div>
    
    <div class="relative z-10">
        <h2 class="text-3xl font-bold text-center mb-8">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400">
                Access Your PITHOS Wallet
            </span>
        </h2>
        
        <!-- Tab Buttons with Enhanced Style -->
        <div class="grid grid-cols-2 gap-3 bg-black/30 p-2 rounded-xl mb-8">
            <button id="tab-login" onclick="showAuthForm('login')" 
                    class="py-3 px-6 rounded-lg font-semibold transition-all duration-300 text-gray-400 hover:bg-white/5">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In
                </div>
            </button>
            <button id="tab-register" onclick="showAuthForm('register')" 
                    class="py-3 px-6 rounded-lg font-semibold transition-all duration-300 text-gray-400 hover:bg-white/5">
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Register
                </div>
            </button>
        </div>

        <!-- Login Form -->
        <form id="form-login" class="space-y-6">
            <div class="input-group">
                <input type="text" name="login_id" id="login_id" placeholder=" " required 
                       class="input-field peer">
                <label for="login_id" class="peer-label">Username or Email</label>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="login_password" placeholder=" " required 
                       class="input-field peer">
                <label for="login_password" class="peer-label">Password</label>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-400 cursor-pointer hover:text-white transition-colors">
                    <input type="checkbox" class="w-4 h-4 rounded border-gray-600 text-purple-600 focus:ring-purple-500 focus:ring-offset-gray-900">
                    <span>Remember me</span>
                </label>
                <a href="#" class="text-purple-400 hover:text-purple-300 transition-colors">Forgot password?</a>
            </div>

            <p id="login-message" class="text-center text-sm"></p>

            <button type="submit" id="login-btn" class="btn-primary w-full py-4 flex justify-center items-center gap-3 group">
                <span id="login-text">Sign In</span>
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                <span id="login-spinner" class="spinner hidden"></span>
            </button>

            <div class="text-center text-sm text-gray-400">
                Don't have an account? 
                <button type="button" onclick="showAuthForm('register')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                    Create one now
                </button>
            </div>
        </form>

        <!-- Register Form (Hidden by default) -->
        <form id="form-register" class="space-y-6 hidden">
            <div class="input-group">
                <input type="text" name="username" id="register_username" placeholder=" " required 
                       class="input-field peer">
                <label for="register_username" class="peer-label">Username</label>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>

            <div class="input-group">
                <input type="email" name="email" id="register_email" placeholder=" " required 
                       class="input-field peer">
                <label for="register_email" class="peer-label">Email Address</label>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="register_password" placeholder=" " required 
                       class="input-field peer" minlength="6">
                <label for="register_password" class="peer-label">Password (min 6 characters)</label>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
            </div>

            <div class="input-group">
                <input type="text" name="referrer_id" id="referrer_id" placeholder=" "
                       class="input-field peer">
                <label for="referrer_id" class="peer-label">Referral ID (Optional)</label>
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Bonus Info Box -->
            <div class="bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-500/30 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-400 to-cyan-500 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-emerald-400 mb-1">🎁 Sign-Up Bonus</h4>
                        <p class="text-sm text-gray-300">Get <strong class="text-white"><?php echo number_format(KYC_BONUS); ?> GALAXY tokens</strong> instantly upon registration!</p>
                    </div>
                </div>
            </div>
            
            <p id="register-message" class="text-center text-sm"></p>

            <button type="submit" id="register-btn" class="btn-primary w-full py-4 flex justify-center items-center gap-3 group">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                </svg>
                <span id="register-text">Register & Claim <?php echo number_format(KYC_BONUS); ?> GALAXY</span>
                <span id="register-spinner" class="spinner hidden"></span>
            </button>

            <div class="text-center text-sm text-gray-400">
                Already have an account? 
                <button type="button" onclick="showAuthForm('login')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors">
                    Sign in here
                </button>
            </div>

            <p class="text-xs text-gray-500 text-center">
                By registering, you agree to our Terms of Service and Privacy Policy
            </p>
        </form>
    </div>
</div>

<style>
    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .input-field {
        width: 100%;
        padding: 1.25rem 3rem 1.25rem 1.5rem;
        background: rgba(0, 0, 0, 0.4);
        border: 2px solid rgba(153, 69, 255, 0.3);
        border-radius: 12px;
        color: #fff;
        outline: none;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .input-field:focus {
        border-color: var(--primary);
        box-shadow: 0 0 20px rgba(153, 69, 255, 0.4);
        background: rgba(0, 0, 0, 0.5);
    }

    .peer-label {
        position: absolute;
        left: 1.5rem;
        top: 1.25rem;
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
        pointer-events: none;
        background: transparent;
        padding: 0 0.5rem;
        font-size: 0.95rem;
    }

    .input-field:focus ~ .peer-label,
    .input-field:not(:placeholder-shown) ~ .peer-label {
        transform: translateY(-2.5rem) scale(0.85);
        color: var(--primary);
        background: var(--dark-bg);
    }
</style>
