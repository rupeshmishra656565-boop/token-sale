<?php // Premium Auth Form Component - Used in home.php ?>
<div class="glass-card p-6 md:p-8 relative overflow-hidden">
    <!-- ... decorative elements ... -->
    <div class="relative z-10">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-6 font-heading">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400">
                Access Your PITHOS Wallet
            </span>
        </h2>
        <!-- Tab Buttons -->
        <div class="grid grid-cols-2 gap-2 bg-black/30 p-1.5 rounded-lg mb-6">
            <button id="tab-login" onclick="showAuthForm('login')" class="py-2.5 px-4 rounded font-semibold transition-all duration-300 text-sm"> Sign In </button>
            <button id="tab-register" onclick="showAuthForm('register')" class="py-2.5 px-4 rounded font-semibold transition-all duration-300 text-sm"> Register </button>
        </div>

        <!-- Login Form -->
        <form id="form-login" class="space-y-4">
            <!-- ... (Username/Email and Password fields remain the same) ... -->
             <div class="input-group">
                <input type="text" name="login_id" id="login_id" placeholder=" " required class="input-field peer">
                <label for="login_id" class="peer-label">Username or Email</label>
                <!-- Icon -->
            </div>
             <div class="input-group">
                <input type="password" name="password" id="login_password" placeholder=" " required class="input-field peer">
                <label for="login_password" class="peer-label">Password</label>
                 <!-- Icon -->
            </div>

            <div class="flex items-center justify-between text-xs sm:text-sm">
                <label class="flex items-center gap-2 text-gray-400 cursor-pointer hover:text-white transition-colors">
                    <input type="checkbox" class="w-4 h-4 rounded border-gray-600 text-purple-600 focus:ring-purple-500 focus:ring-offset-gray-900 bg-gray-700">
                    <span>Remember me</span>
                </label>
                <!-- [NEW] Forgot Password Link -->
                <a href="#forgot-password" onclick="showAuthForm('forgot')" class="text-purple-400 hover:text-purple-300 transition-colors">Forgot password?</a>
            </div>

            <p id="login-message" class="text-center text-sm min-h-[1.25rem]"></p> <!-- Min height to prevent layout shift -->

            <button type="submit" id="login-btn" class="btn-primary w-full !py-3 !text-base flex justify-center items-center gap-2 group">
                 <span id="login-text">Sign In</span>
                 <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform hidden sm:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                 <span id="login-spinner" class="spinner hidden"></span>
            </button>
            <div class="text-center text-sm text-gray-400 pt-2"> Don't have an account? <button type="button" onclick="showAuthForm('register')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors"> Create one </button> </div>
        </form>

        <!-- Register Form (Modified for OTP) -->
        <form id="form-register" class="space-y-4 hidden">
             <!-- Username -->
             <div class="input-group">
                <input type="text" name="username" id="register_username" placeholder=" " required class="input-field peer">
                <label for="register_username" class="peer-label">Username</label>
                 <!-- Icon -->
            </div>
             <!-- Email -->
             <div class="input-group">
                <input type="email" name="email" id="register_email" placeholder=" " required class="input-field peer">
                <label for="register_email" class="peer-label">Email Address</label>
                 <!-- Icon -->
            </div>
             <!-- Password -->
             <div class="input-group">
                <input type="password" name="password" id="register_password" placeholder=" " required class="input-field peer" minlength="6">
                <label for="register_password" class="peer-label">Password (min 6 chars)</label>
                 <!-- Icon -->
            </div>
             <!-- Referral -->
             <div class="input-group">
                <input type="text" name="referrer_id" id="referrer_id" placeholder=" " class="input-field peer">
                <label for="referrer_id" class="peer-label">Referral ID (Optional)</label>
                 <!-- Icon -->
            </div>

            <!-- [NEW] OTP Section (Initially Hidden) -->
            <div id="otp-section" class="hidden space-y-4 pt-2">
                 <div class="input-group">
                    <input type="text" name="otp" id="register_otp" placeholder=" " required class="input-field peer" maxlength="6" pattern="\d{6}">
                    <label for="register_otp" class="peer-label">6-Digit OTP</label>
                     <!-- Icon -->
                 </div>
            </div>
            <!-- End OTP Section -->

            <p id="register-message" class="text-center text-sm min-h-[1.25rem]"></p>

             <!-- [NEW] OTP Button -->
            <button type="button" id="send-otp-btn" class="w-full !py-3 !text-base bg-cyan-600 hover:bg-cyan-700 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center gap-2">
                <span id="otp-btn-text">Send Verification OTP</span>
                <span id="otp-spinner" class="spinner hidden"></span>
            </button>

            <!-- [MODIFIED] Register Button (Initially Hidden/Disabled) -->
            <button type="submit" id="register-btn" class="btn-primary w-full !py-3 !text-base hidden justify-center items-center gap-2 group">
                 <span id="register-text">Verify OTP & Register</span>
                 <span id="register-spinner" class="spinner hidden"></span>
            </button>

             <!-- Bonus Info -->
             <div class="bg-gradient-to-r from-emerald-500/10 to-cyan-500/10 border border-emerald-500/30 rounded-lg p-3 text-center text-xs">
                 🎁 Sign up & verify to get <strong class="text-white"><?php echo number_format(KYC_BONUS); ?> GALAXY</strong> instantly!
             </div>

            <div class="text-center text-sm text-gray-400 pt-2"> Already have an account? <button type="button" onclick="showAuthForm('login')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors"> Sign in </button> </div>
            <p class="text-xs text-gray-500 text-center pt-1"> By registering, you agree to our Terms. </p>
        </form>

         <!-- [NEW] Forgot Password Form -->
        <form id="form-forgot" class="space-y-4 hidden">
             <p class="text-center text-gray-400 text-sm">Enter your email address to receive a password reset link.</p>
             <div class="input-group">
                <input type="email" name="email" id="forgot_email" placeholder=" " required class="input-field peer">
                <label for="forgot_email" class="peer-label">Email Address</label>
                 <!-- Icon -->
            </div>
            <p id="forgot-message" class="text-center text-sm min-h-[1.25rem]"></p>
            <button type="submit" id="forgot-btn" class="btn-primary w-full !py-3 !text-base flex justify-center items-center gap-2 group">
                 <span id="forgot-text">Send Reset Link</span>
                 <span id="forgot-spinner" class="spinner hidden"></span>
            </button>
            <div class="text-center text-sm text-gray-400 pt-2"> Remembered password? <button type="button" onclick="showAuthForm('login')" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors"> Sign in </button> </div>
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