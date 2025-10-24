// ==============================================================================
// Frontend AJAX Logic (public/assets/main.js) - Email Features Added
// Using REDIRECT METHOD for Payments
// ==============================================================================

const CURRENT_FILE = 'index.php';
// Define TOKEN_RATE globally if needed by JS (get from PHP or hardcode)
const TOKEN_RATE = 1000.00; // Make sure this matches config.php

/** Utility for displaying non-blocking toast notifications. */
function showToast(message, type) {
    const toastContainer = $('#toast-container');
    const toast = $('<div>', { class: 'p-4 mb-2 rounded-xl shadow-lg text-white transition-opacity duration-300 ease-in-out opacity-0 translate-x-full'});
    let colorClass = '';
    if (type === 'success') colorClass = 'bg-emerald-600'; else if (type === 'error') colorClass = 'bg-red-600'; else if (type === 'info') colorClass = 'bg-blue-600'; else colorClass = 'bg-yellow-600';
    toast.addClass(colorClass).text(message); toastContainer.append(toast);
    setTimeout(() => toast.removeClass('opacity-0 translate-x-full'), 10);
    setTimeout(() => { toast.addClass('opacity-0 translate-x-full'); setTimeout(() => toast.remove(), 300); }, 5000);
}

/** Copies text to the clipboard (generalized). */
function copyToClipboard(text, elementId) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('✅ Copied!', 'info');
            if(elementId) { const el = $(`#${elementId}`); const originalHtml = el.html(); el.html('Copied!'); setTimeout(() => el.html(originalHtml), 1500); }
        }).catch(err => { showToast('❌ Copy failed.', 'error'); console.error('Clipboard API error:', err); });
    } else { /* Fallback */ const textArea = document.createElement("textarea"); textArea.value = text; textArea.style.position = "fixed"; document.body.appendChild(textArea); textArea.focus(); textArea.select(); try { document.execCommand('copy'); showToast('✅ Copied!', 'info'); if(elementId) { const el = $(`#${elementId}`); const originalHtml = el.html(); el.html('Copied!'); setTimeout(() => el.html(originalHtml), 1500); }} catch (err) { showToast('❌ Copy failed.', 'error'); } document.body.removeChild(textArea); }
}

/** Copies the referral link. */
function copyReferralLink() { const input = document.getElementById('referral-link-input'); if(input) copyToClipboard(input.value, null); }

/** Manages button loading state. */
function setButtonLoading(buttonId, textId, spinnerId, isLoading, defaultText) {
    const button = $(`#${buttonId}`); const text = $(`#${textId}`); const spinner = $(`#${spinnerId}`);
    if (isLoading) { button.prop('disabled', true).addClass('opacity-70'); text.addClass('hidden'); spinner.removeClass('hidden'); }
    else { button.prop('disabled', false).removeClass('opacity-70'); text.removeClass('hidden').text(defaultText); spinner.addClass('hidden'); }
}

/** Fetches user balance. */
function fetchBalance() {
    const balanceEl = $('#token-balance'); if (!balanceEl.length) return; balanceEl.text('---').addClass('animate-pulse');
    $.ajax({ url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'get_balance' },
        success: function(response) { if (response.success) { balanceEl.text(response.balance); } else { balanceEl.text('N/A'); /* Optional: showToast('Failed: '+response.message, 'error'); */ }},
        error: function(xhr) { balanceEl.text('Error'); showToast('Net err balance.', 'error'); }, complete: function() { balanceEl.removeClass('animate-pulse'); }
    });
}

/** Fetches referral stats. */
function fetchReferralStats() {
    const referralsEl = $('#dashboard-referral-count'); const earningsEl = $('#dashboard-referral-earnings'); if (!earningsEl.length) return;
    earningsEl.text('---').addClass('animate-pulse'); if(referralsEl && referralsEl.length) { referralsEl.text('---').addClass('animate-pulse'); }
    $.ajax({ url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'get_referral_history' },
        success: function(response) { if (response.success && response.stats) { earningsEl.text(response.stats.total_earnings); if(referralsEl && referralsEl.length) referralsEl.text(response.stats.total_referrals); } else { earningsEl.text('0.00'); if(referralsEl && referralsEl.length) referralsEl.text('0'); }},
        error: function() { earningsEl.text('0.00'); if(referralsEl && referralsEl.length) referralsEl.text('0'); },
        complete: function() { earningsEl.removeClass('animate-pulse'); if(referralsEl && referralsEl.length) referralsEl.removeClass('animate-pulse'); }
    });
}


/** [REDIRECT METHOD] Shows modal, calls backend, and redirects to payment URL. */
function confirmTokenPurchase(usdAmount, bonusPercent) {
    const modal = $('#payment-modal');
    // 1. Setup and Show Modal
    $('#mock-payment-amount').text('$' + usdAmount.toFixed(2));
    const baseTokens = usdAmount * TOKEN_RATE; const totalTokens = baseTokens + Math.floor(baseTokens * bonusPercent);
    $('#mock-payment-tokens').text(new Intl.NumberFormat().format(totalTokens) + ' GALAXY');
    $('#payment-options').addClass('hidden'); $('#payment-details-display').addClass('hidden'); $('#payment-processing-status').removeClass('hidden');
    $('#payment-status-text').text("Creating secure payment invoice..."); $('#modal-title').find('span').text('Processing Request...');
    modal.removeClass('hidden');
    // 2. Disable buttons
    const buyButtons = $('button[onclick^="confirmTokenPurchase"]'); buyButtons.prop('disabled', true).addClass('opacity-50');
    // 3. AJAX call (without currency)
    $.ajax({
        url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'create_payment_invoice', usd_amount: usdAmount, bonus_percent: bonusPercent },
        success: function(response) {
            if (response.success && response.payment_url) {
                // 4. Redirect
                $('#payment-status-text').text("✅ Success! Redirecting...");
                setTimeout(() => { window.location.href = response.payment_url; }, 1000);
            } else {
                // Handle error
                $('#payment-spinner').addClass('hidden'); $('#payment-status-text').html(`<span class="text-red-400">❌ Error: ${response.message || 'Could not create invoice.'}</span>`);
                showToast(response.message || 'Could not create invoice.', 'error');
                setTimeout(() => { // Re-enable buttons and hide modal
                    buyButtons.prop('disabled', false).removeClass('opacity-50'); modal.addClass('hidden');
                    $('#payment-processing-status').addClass('hidden'); $('#payment-spinner').removeClass('hidden'); $('#modal-title').find('span').text('Confirm Your Purchase');
                }, 4000);
            }
        },
        error: function(xhr) { /* Network error handling */
             $('#payment-spinner').addClass('hidden'); $('#payment-status-text').html('<span class="text-red-400">❌ Network Error.</span>');
             showToast("Network Error.", 'error'); buyButtons.prop('disabled', false).removeClass('opacity-50');
             $('#payment-processing-status').addClass('hidden'); $('#payment-spinner').removeClass('hidden'); $('#modal-title').find('span').text('Confirm Your Purchase');
        }
    });
}

// Simple close modal function for error state or manual close (redirect method)
function closePaymentModalOnError() {
    $('#payment-modal').addClass('hidden');
    $('button[onclick^="confirmTokenPurchase"]').prop('disabled', false).removeClass('opacity-50');
    // Reset modal state
    $('#payment-processing-status').addClass('hidden').find('#payment-spinner').removeClass('hidden');
    $('#modal-title').find('span').text('Confirm Your Purchase'); // Reset title
}


// --- Document Ready / Initialization ---
$(document).ready(function() {
    // Export functions
    window.copyReferralLink = copyReferralLink;
    window.setButtonLoading = setButtonLoading;
    window.fetchBalance = fetchBalance;
    window.fetchReferralStats = fetchReferralStats;
    window.confirmTokenPurchase = confirmTokenPurchase;
    window.copyToClipboard = copyToClipboard;
    window.closePaymentModalOnError = closePaymentModalOnError;

    // Deprecated pointers
    window.initiateTokenPurchase = confirmTokenPurchase;
    window.simulateTokenPurchase = confirmTokenPurchase;
    window.showPaymentOptions = confirmTokenPurchase;


    // --- Init Checks ---
    if ($('#token-balance').length) { fetchBalance(); }
    if ($('#dashboard-referral-earnings').length) { fetchReferralStats(); }
     // --- Auth Form Initial State ---
     if ($('#form-login').length) {
        if(typeof showAuthForm === 'function') { // Check if showAuthForm exists (defined inline in index.php)
            showAuthForm('login');
            const urlParams = new URLSearchParams(window.location.search);
            const ref = urlParams.get('ref');
            if (ref) { $('#referrer_id').val(ref); showAuthForm('register'); }
        } else { console.error("showAuthForm function not defined globally."); }
     }


    // --- Form Handlers ---

    // 1. Login Form Submission
    $('#form-login').on('submit', function(e) {
        e.preventDefault(); const defaultText = 'Sign In'; setButtonLoading('login-btn', 'login-text', 'login-spinner', true, defaultText); $('#login-message').text('');
        const formData = $(this).serializeArray(); formData.push({ name: 'action', value: 'login' });
        $.post(CURRENT_FILE, formData, function(response) {
            setButtonLoading('login-btn', 'login-text', 'login-spinner', false, defaultText);
            if (response.success) { showToast(response.message, 'success'); window.location.href = CURRENT_FILE + '?p=dashboard'; }
            else { $('#login-message').text(response.message).addClass('text-red-400'); showToast(response.message, 'error'); }
        }).fail(function() { setButtonLoading('login-btn', 'login-text', 'login-spinner', false, defaultText); $('#login-message').text('Network error.').addClass('text-red-400'); showToast('Network error.', 'error'); });
    });

    // 2. Send OTP Button Click
    $('#send-otp-btn').on('click', function(e) {
        e.preventDefault(); const defaultText = 'Send Verification OTP'; const btnId = 'send-otp-btn'; const textId = 'otp-btn-text'; const spinnerId = 'otp-spinner';
        setButtonLoading(btnId, textId, spinnerId, true, defaultText); $('#register-message').text('').removeClass('text-red-400 text-emerald-400');
        const username = $('#register_username').val(); const email = $('#register_email').val();
        if (!username || !email) { $('#register-message').text('Enter username & email.').addClass('text-red-400'); setButtonLoading(btnId, textId, spinnerId, false, defaultText); return; }
        $.post(CURRENT_FILE, { action: 'requestRegisterOtp', username: username, email: email }, function(response) {
            setButtonLoading(btnId, textId, spinnerId, false, defaultText);
            if (response.success) {
                $('#register-message').text(response.message).addClass('text-emerald-400');
                $('#otp-section').slideDown(); $('#send-otp-btn').addClass('hidden'); $('#register-btn').removeClass('hidden').addClass('flex');
            } else { $('#register-message').text(response.message || 'Failed OTP send.').addClass('text-red-400'); showToast(response.message || 'Failed OTP send.', 'error'); }
        }).fail(function() { setButtonLoading(btnId, textId, spinnerId, false, defaultText); $('#register-message').text('Network error sending OTP.').addClass('text-red-400'); showToast('Network error.', 'error'); });
    });

    // 3. Register Form Submission (Verify OTP & Register)
    $('#form-register').on('submit', function(e) {
        e.preventDefault(); const defaultText = 'Verify OTP & Register'; const btnId = 'register-btn'; const textId = 'register-text'; const spinnerId = 'register-spinner';
        setButtonLoading(btnId, textId, spinnerId, true, defaultText); $('#register-message').text('');
        const formData = $(this).serializeArray(); formData.push({ name: 'action', value: 'register' });
        $.post(CURRENT_FILE, formData, function(response) {
            setButtonLoading(btnId, textId, spinnerId, false, defaultText);
            if (response.success) { showToast(response.message, 'success'); window.location.href = CURRENT_FILE + '?p=dashboard'; }
            else { $('#register-message').text(response.message || 'Registration failed.').addClass('text-red-400'); showToast(response.message || 'Registration failed.', 'error'); }
        }).fail(function() { setButtonLoading(btnId, textId, spinnerId, false, defaultText); $('#register-message').text('Network error.').addClass('text-red-400'); showToast('Network error.', 'error'); });
    });

    // 4. Forgot Password Form Submission
     $('#form-forgot').on('submit', function(e) {
        e.preventDefault(); const defaultText = 'Send Reset Link'; const btnId = 'forgot-btn'; const textId = 'forgot-text'; const spinnerId = 'forgot-spinner';
        setButtonLoading(btnId, textId, spinnerId, true, defaultText); $('#forgot-message').text('').removeClass('text-red-400 text-emerald-400');
        $.post(CURRENT_FILE, { action: 'requestPasswordReset', email: $('#forgot_email').val() }, function(response) {
            setButtonLoading(btnId, textId, spinnerId, false, defaultText);
             $('#forgot-message').text(response.message || 'If account exists, email sent.').addClass(response.success ? 'text-emerald-400' : 'text-red-400');
             if (response.success) { showToast(response.message || 'Reset email process initiated.', 'info'); $('#forgot-btn').prop('disabled', true).addClass('opacity-50'); } // Disable button on success
             else { showToast(response.message || 'Failed.', 'error'); } // Should not happen
        }).fail(function() { setButtonLoading(btnId, textId, spinnerId, false, defaultText); $('#forgot-message').text('Network error.').addClass('text-red-400'); showToast('Network error.', 'error'); });
    });

    // 5. Logout Button Handler
    $('#logout-btn, #logout-btn-mobile').on('click', function(e) { // Combined selector
        e.preventDefault(); showToast('Logging out...', 'info');
        $.post(CURRENT_FILE, { action: 'logout' }, function(response) { if (response.success) { window.location.href = CURRENT_FILE; } else { showToast('Logout failed.', 'error'); } });
    });

}); // End document ready