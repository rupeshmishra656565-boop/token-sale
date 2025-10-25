<?php
// Ultra-Premium Auth Form Component - Next-Level Design with OTP
?>
<div class="relative group">
    <div class="absolute -inset-0.5 bg-gradient-to-r from-purple-600 via-cyan-500 to-purple-600 rounded-3xl blur-lg opacity-30 group-hover:opacity-50 transition duration-1000 animate-pulse"></div>

    <div class="relative glass-card p-8 md:p-10 rounded-3xl shadow-2xl border-2 border-purple-500/30 overflow-hidden" style="--card-bg: rgba(15, 15, 25, 0.95);">
        <div class="absolute -top-20 -right-20 w-48 h-48 bg-gradient-to-br from-purple-600/30 to-transparent rounded-full blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 w-48 h-48 bg-gradient-to-tr from-cyan-600/30 to-transparent rounded-full blur-3xl"></div>

        <div class="relative z-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-cyan-500 shadow-xl shadow-purple-500/40 mb-5">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>

                <h2 class="text-2xl md:text-3xl lg:text-4xl font-black font-heading mb-3">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-white via-purple-300 to-cyan-300">
                        Welcome to <?php echo TOKEN_NAME; ?>
                    </span>
                </h2>
                <p class="text-gray-400 text-sm md:text-base">
                    Secure your digital assets on Solana
                </p>
            </div>

            <div class="relative mb-8">
                <div class="grid grid-cols-2 gap-3 p-1.5 bg-black/60 rounded-xl border border-white/10 backdrop-blur-sm">
                    <button id="tab-login" onclick="showAuthForm('login')"
                            class="relative py-3.5 px-6 rounded-lg font-bold transition-all duration-300 text-sm md:text-base overflow-hidden group">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Sign In
                        </span>
                    </button>
                    <button id="tab-register" onclick="showAuthForm('register')"
                            class="relative py-3.5 px-6 rounded-lg font-bold transition-all duration-300 text-sm md:text-base overflow-hidden group">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Register
                        </span>
                    </button>
                </div>
            </div>

            <form id="form-login" class="space-y-6 hidden">
                <div class="input-group">
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input type="text" name="login_id" id="login_id" placeholder=" " required
                               class="input-field peer !pl-12" autocomplete="username email">
                        <label for="login_id" class="peer-label !left-12">Username or Email</label>
                    </div>
                </div>

                <div class="input-group">
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="login_password" placeholder=" " required
                               class="input-field peer !pl-12" autocomplete="current-password">
                        <label for="login_password" class="peer-label !left-12">Password</label>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 text-gray-400 cursor-pointer hover:text-white transition-colors group">
                        <div class="relative">
                            <input type="checkbox" class="peer sr-only">
                            <div class="w-5 h-5 rounded border-2 border-gray-600 peer-checked:border-purple-500 peer-checked:bg-purple-500 transition-all duration-200"></div>
                            <svg class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium">Remember me</span>
                    </label>
                     <a href="forgot_password.php" class="text-sm font-medium text-purple-400 hover:text-purple-300 transition-colors">Forgot Password?</a>
                </div>

                <p id="login-message" class="text-center text-sm min-h-[1.25rem] text-red-400 font-medium"></p>

                <button type="submit" id="login-btn" class="relative w-full py-4 px-6 rounded-xl font-bold text-base md:text-lg overflow-hidden bg-gradient-to-r from-purple-600 via-purple-500 to-cyan-500 hover:from-purple-500 hover:via-purple-600 hover:to-cyan-600 shadow-xl shadow-purple-500/40 hover:shadow-purple-500/60 transition-all duration-300 group border-2 border-purple-400/50 hover:border-purple-300">
                    <span class="relative z-10 flex items-center justify-center gap-3 text-white">
                        <span id="login-text">Sign In to Dashboard</span>
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <span id="login-spinner" class="spinner hidden"></span>
                    </span>
                </button>

                <div class="text-center pt-4">
                    <span class="text-gray-400 text-sm">New to <?php echo TOKEN_NAME; ?>?</span>
                    <button type="button" onclick="showAuthForm('register')" class="ml-2 text-purple-400 hover:text-purple-300 font-bold transition-colors text-sm">
                        Create Account →
                    </button>
                </div>
            </form>

            <form id="form-register" class="space-y-6 hidden">
                <div id="register-step-1" class="space-y-6">
                    <div class="input-group">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="username" id="register_username" placeholder=" " required
                                   class="input-field peer !pl-12" autocomplete="username">
                            <label for="register_username" class="peer-label !left-12">Choose Username</label>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" id="register_email" placeholder=" " required
                                   class="input-field peer !pl-12" autocomplete="email">
                            <label for="register_email" class="peer-label !left-12">Email Address</label>
                        </div>
                    </div>

                    <div class="input-group">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password" name="password" id="register_password" placeholder=" " required
                                   class="input-field peer !pl-12" minlength="6" autocomplete="new-password">
                            <label for="register_password" class="peer-label !left-12">Create Password</label>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 ml-1">Minimum 6 characters required</p>
                    </div>

                    <div class="input-group">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="referrer_id" id="referrer_id" placeholder=" "
                                   class="input-field peer !pl-12">
                            <label for="referrer_id" class="peer-label !left-12">Referral Code (Optional)</label>
                        </div>
                    </div>
                </div>

                <div id="register-step-2" class="space-y-6 hidden">
                    <p class="text-center text-sm text-cyan-300">An OTP has been sent to <strong id="otp-email-display">your-email@example.com</strong>. Please enter it below.</p>
                    <div class="input-group">
                        <div class="relative">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 z-10">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v3m-6-3h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2z"></path></svg>
                            </div>
                            <input type="text" name="otp_code" id="register_otp" placeholder=" " required
                                   class="input-field peer !pl-12" autocomplete="one-time-code" inputmode="numeric" pattern="\d{6}">
                            <label for="register_otp" class="peer-label !left-12">6-Digit OTP Code</label>
                        </div>
                    </div>
                     <button type="button" id="back-to-details-btn" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1 mx-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        Back to details
                    </button>
                </div>

                <p id="register-message" class="text-center text-sm min-h-[1.25rem] text-red-400 font-medium"></p>

                <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-emerald-900/40 via-cyan-900/40 to-emerald-900/40 border-2 border-emerald-500/50 p-4 shadow-xl shadow-emerald-500/20">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent animate-shimmer"></div>
                    <div class="relative z-10 flex items-center justify-center gap-3">
                         <div class="w-10 h-10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-center">
                            <div class="text-white font-bold text-lg">🎁 Welcome Bonus</div>
                            <div class="text-emerald-300 text-sm font-semibold">
                                Get <span class="text-white text-base"><?php echo number_format(KYC_BONUS, 0); ?> <?php echo TOKEN_SYMBOL; ?></span> instantly upon registration!
                            </div>
                        </div>
                    </div>
                </div>

                <div id="register-button-container">
                    <button type="button" id="send-otp-btn" class="relative w-full py-4 px-6 rounded-xl font-bold text-base md:text-lg overflow-hidden bg-gradient-to-r from-purple-600 via-purple-500 to-cyan-500 hover:from-purple-500 hover:via-purple-600 hover:to-cyan-500 shadow-xl shadow-purple-500/40 hover:shadow-purple-500/60 transition-all duration-300 group border-2 border-purple-400/50 hover:border-purple-300">
                        <span class="relative z-10 flex items-center justify-center gap-3 text-white">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span id="send-otp-text">Send Verification Code</span>
                            <span id="send-otp-spinner" class="spinner hidden"></span>
                        </span>
                    </button>

                    <button type="submit" id="register-btn" class="relative w-full py-4 px-6 rounded-xl font-bold text-base md:text-lg overflow-hidden bg-gradient-to-r from-emerald-500 via-cyan-500 to-emerald-500 hover:from-emerald-600 hover:via-cyan-600 hover:to-emerald-600 shadow-xl shadow-emerald-500/40 hover:shadow-emerald-500/60 transition-all duration-300 group border-2 border-emerald-400/50 hover:border-emerald-300 hidden">
                        <span class="relative z-10 flex items-center justify-center gap-3 text-black">
                            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span id="register-text">Create Account & Claim Bonus</span>
                            <span id="register-spinner" class="spinner !border-black/30 !border-t-black hidden"></span>
                        </span>
                    </button>
                </div>

                <div class="text-center pt-4">
                    <span class="text-gray-400 text-sm">Already have an account?</span>
                    <button type="button" onclick="showAuthForm('login')" class="ml-2 text-purple-400 hover:text-purple-300 font-bold transition-colors text-sm">
                        Sign In →
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-white/10">
                <div class="flex items-center justify-center gap-6 flex-wrap">
                    <div class="flex items-center gap-2 text-gray-400 text-xs">
                        <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">256-bit Encryption</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 text-xs">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.964Z"/>
                        </svg>
                        <span class="font-medium">Solana Secured</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-400 text-xs">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span class="font-medium">GDPR Compliant</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }
.animate-shimmer { animation: shimmer 3s infinite; }
#tab-login, #tab-register { color: #9ca3af; background: transparent; }
#tab-login.active-link, #tab-register.active-link { background: linear-gradient(135deg, rgba(147, 51, 234, 0.8), rgba(6, 182, 212, 0.8)) !important; color: white !important; box-shadow: 0 8px 20px rgba(147, 51, 234, 0.4); }
#tab-login:not(.active-link):hover, #tab-register:not(.active-link):hover { background: rgba(255, 255, 255, 0.05); color: white; }
</style>

<script>
$(document).ready(function() {
    // Keep reference to the main form elements
    const registerForm = $('#form-register');
    const registerStep1 = $('#register-step-1');
    const registerStep2 = $('#register-step-2');
    const otpEmailDisplay = $('#otp-email-display');
    const registerMessage = $('#register-message');
    const sendOtpButton = $('#send-otp-btn');
    const registerButton = $('#register-btn');
    const buttonContainer = $('#register-button-container');

    // Email validation regex (simple version)
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Function to show/hide registration steps
    function showRegisterStep(step) {
        registerMessage.text('').removeClass('text-emerald-400 text-red-400'); // Clear message
        if (step === 1) {
            registerStep1.removeClass('hidden');
            registerStep2.addClass('hidden');
            sendOtpButton.removeClass('hidden');
            registerButton.addClass('hidden');
            // Re-enable detail fields
            $('#register_username, #register_email, #register_password, #referrer_id').prop('disabled', false);
        } else if (step === 2) {
            registerStep1.addClass('hidden');
            registerStep2.removeClass('hidden');
            sendOtpButton.addClass('hidden');
            registerButton.removeClass('hidden');
            // Disable detail fields to prevent changes
            $('#register_username, #register_email, #register_password, #referrer_id').prop('disabled', true);
             $('#register_otp').focus(); // Focus OTP field
        }
    }

    // --- Send OTP Button Handler ---
    $('#send-otp-btn').on('click', function() {
        const username = $('#register_username').val().trim();
        const email = $('#register_email').val().trim();
        const password = $('#register_password').val(); // Don't trim password

        // --- Frontend Validation ---
        registerMessage.text('').removeClass('text-emerald-400 text-red-400'); // Clear previous messages
        if (!username) {
            registerMessage.text('Username is required.').addClass('text-red-400');
            $('#register_username').focus();
            return;
        }
        if (!email || !emailRegex.test(email)) {
            registerMessage.text('Please enter a valid email address.').addClass('text-red-400');
             $('#register_email').focus();
            return;
        }
        if (!password || password.length < 6) {
             registerMessage.text('Password must be at least 6 characters.').addClass('text-red-400');
             $('#register_password').focus();
             return;
        }
        // --- End Frontend Validation ---

        const defaultText = 'Send Verification Code';
        setButtonLoading('send-otp-btn', 'send-otp-text', 'send-otp-spinner', true, defaultText);
        
        // Send AJAX request to the new backend action
        $.ajax({
            url: CURRENT_FILE, // Assuming CURRENT_FILE is defined globally or adjust
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'send_registration_otp',
                username: username,
                email: email
                // No need to send password or ref ID yet
            },
            success: function(response) {
                setButtonLoading('send-otp-btn', 'send-otp-text', 'send-otp-spinner', false, defaultText);
                if (response.success) {
                    showToast(response.message, 'success'); // Use global toast
                    otpEmailDisplay.text(email); // Show the email address
                    showRegisterStep(2); // Go to step 2 (OTP input)
                } else {
                    registerMessage.text(response.message || 'Failed to send OTP.').addClass('text-red-400');
                    showToast(response.message || 'Failed to send OTP.', 'error');
                }
            },
            error: function(xhr) {
                setButtonLoading('send-otp-btn', 'send-otp-text', 'send-otp-spinner', false, defaultText);
                registerMessage.text('Network error sending OTP. Please try again.').addClass('text-red-400');
                showToast('Network error sending OTP.', 'error');
            }
        });
    });

    // --- Back Button Handler ---
     $('#back-to-details-btn').on('click', function() {
        showRegisterStep(1); // Go back to step 1
    });

    // --- Final Registration Form Submit Handler ---
    registerForm.on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Check which step is active (should only submit from step 2 now)
        if (registerStep2.hasClass('hidden')) {
             console.log("Attempted to submit from step 1, ignoring.");
             // Optionally trigger the send OTP logic again or show a message
             $('#send-otp-btn').click(); 
            return; 
        }

        const otpCode = $('#register_otp').val().trim();
        if (!otpCode || otpCode.length !== 6 || !/^\d+$/.test(otpCode)) {
            registerMessage.text('Please enter a valid 6-digit OTP.').addClass('text-red-400');
            $('#register_otp').focus();
            return;
        }

        const defaultText = 'Create Account & Claim Bonus';
        setButtonLoading('register-btn', 'register-text', 'register-spinner', true, defaultText);
        registerMessage.text(''); // Clear message

        // Collect ALL form data including disabled fields (important!)
        const formData = {
             action: 'register',
             username: $('#register_username').val(),
             email: $('#register_email').val(),
             password: $('#register_password').val(),
             referrer_id: $('#referrer_id').val(),
             otp_code: otpCode // Include the OTP code
        };

        // Submit the full data including OTP to the existing 'register' action
        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function(response) {
                setButtonLoading('register-btn', 'register-text', 'register-spinner', false, defaultText);
                if (response.success) {
                    showToast(response.message, 'success');
                    window.location.href = CURRENT_FILE + '?p=dashboard'; // Redirect on success
                } else {
                    registerMessage.text(response.message || 'Registration failed.').addClass('text-red-400');
                    showToast(response.message || 'Registration failed.', 'error');
                     // If OTP was invalid, maybe stay on step 2? Otherwise, consider going back to step 1.
                    if (response.message && (response.message.includes('Invalid OTP') || response.message.includes('OTP has expired'))) {
                        $('#register_otp').focus().select();
                    } else if (response.message && response.message.includes('OTP not found')) {
                         // Might mean OTP was cleaned up or never sent - go back
                         showRegisterStep(1);
                    } else {
                        // For other errors like DB error or user exists (shouldn't happen often now), maybe go back
                         showRegisterStep(1);
                    }
                }
            },
            error: function(xhr) {
                setButtonLoading('register-btn', 'register-text', 'register-spinner', false, defaultText);
                registerMessage.text('Network error during registration. Please try again.').addClass('text-red-400');
                showToast('Network error during registration.', 'error');
            }
        });
    });

    // Make showRegisterStep globally accessible if needed, or handle tab clicks
    window.showRegisterStep = showRegisterStep;

     // Override the global showAuthForm slightly to reset the OTP steps
    const originalShowAuthForm = window.showAuthForm;
    window.showAuthForm = function(type) {
        originalShowAuthForm(type); // Call the original function
        if (type === 'register') {
            showRegisterStep(1); // Ensure register form starts at step 1
        }
    };

});
</script>