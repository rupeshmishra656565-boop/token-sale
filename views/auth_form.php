<?php
// Premium Auth Form Component
?>
<div class="glass-card p-6 md:p-8 relative overflow-hidden shadow-xl shadow-purple-900/30 border border-purple-500/20">
    <div class="absolute -top-16 -right-16 w-40 h-40 bg-purple-600/20 rounded-full blur-3xl opacity-60"></div>
    <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-cyan-600/20 rounded-full blur-3xl opacity-60"></div>
    
    <div class="relative z-10">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-6 font-heading">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400">
                Access Your PITHOS Wallet
            </span>
        </h2>
        
        <div class="grid grid-cols-2 gap-2 bg-black/40 p-1.5 rounded-lg mb-8 border border-white/10">
            <button id="tab-login" onclick="showAuthForm('login')" 
                    class="py-2.5 px-4 rounded-md font-semibold transition-all duration-300 text-sm">
                Sign In
            </button>
            <button id="tab-register" onclick="showAuthForm('register')" 
                    class="py-2.5 px-4 rounded-md font-semibold transition-all duration-300 text-sm">
                Register
            </button>
        </div>

        <form id="form-login" class="space-y-5 hidden">
            <div class="input-group"> 
                <input type="text" name="login_id" id="login_id" placeholder=" " required 
                       class="input-field peer" autocomplete="username email">
                <label for="login_id" class="peer-label">Username or Email</label>
            </div>

            <div class="input-group"> 
                <input type="password" name="password" id="login_password" placeholder=" " required 
                       class="input-field peer" autocomplete="current-password">
                <label for="login_password" class="peer-label">Password</label>
            </div>

            <div class="flex items-center justify-between text-xs sm:text-sm -mt-2 mb-4"> 
                <label class="flex items-center gap-2 text-gray-400 cursor-pointer hover:text-white transition-colors">
                    <input type="checkbox" class="w-4 h-4 rounded border-gray-600 text-purple-600 focus:ring-purple-500 focus:ring-offset-gray-900 bg-gray-700 accent-purple-600">
                    <span>Remember me</span>
                </label>
            </div>

            <p id="login-message" class="text-center text-sm min-h-[1.25rem] text-red-400"></p> 
            
            <button type="submit" id="login-btn" class="btn-primary w-full !py-3 !text-base group">
                 <span id="login-text">Sign In</span>
                 <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                 <span id="login-spinner" class="spinner hidden"></span>
            </button>
            
            <div class="text-center text-sm text-gray-400 pt-5">
                 Don't have an account? 
                 <button type="button" onclick="showAuthForm('register')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors underline underline-offset-2"> 
                    Create one 
                 </button> 
            </div>
        </form>

        <form id="form-register" class="space-y-5 hidden">
             <div class="input-group"> 
                <input type="text" name="username" id="register_username" placeholder=" " required class="input-field peer" autocomplete="username">
                <label for="register_username" class="peer-label">Username</label>
            </div>
             <div class="input-group"> 
                <input type="email" name="email" id="register_email" placeholder=" " required class="input-field peer" autocomplete="email">
                <label for="register_email" class="peer-label">Email Address</label>
            </div>
             <div class="input-group"> 
                <input type="password" name="password" id="register_password" placeholder=" " required class="input-field peer" minlength="6" autocomplete="new-password">
                <label for="register_password" class="peer-label">Password (min 6 chars)</label>
            </div>
             <div class="input-group"> 
                <input type="text" name="referrer_id" id="referrer_id" placeholder=" " class="input-field peer">
                <label for="referrer_id" class="peer-label">Referral ID (Optional)</label>
            </div>

            <p id="register-message" class="text-center text-sm min-h-[1.25rem] text-red-400"></p>
            
            <button type="submit" id="register-btn" class="btn-primary w-full !py-3 !text-base group">
                 <span id="register-text">Register & Claim Bonus</span>
                 <span id="register-spinner" class="spinner hidden"></span>
            </button>
            
             <div class="bg-gradient-to-r from-emerald-900/30 to-cyan-900/30 border border-emerald-500/30 rounded-lg p-3 text-center text-xs mt-5">
                 <span class="text-emerald-300">🎁 Sign up to get <strong class="text-white"><?php echo number_format(KYC_BONUS); ?> <?php echo TOKEN_SYMBOL; ?></strong> instantly!</span>
             </div>
             
            <div class="text-center text-sm text-gray-400 pt-5">
                Already have an account? 
                <button type="button" onclick="showAuthForm('login')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors underline underline-offset-2"> 
                    Sign in 
                </button> 
            </div>
        </form>
        
    </div>
</div>