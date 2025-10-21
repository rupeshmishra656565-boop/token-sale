// ==============================================================================
// Frontend AJAX Logic (public/assets/main.js)
// ==============================================================================

const CURRENT_FILE = 'index.php'; // The PHP entry point for all AJAX requests

/**
 * Utility for displaying non-blocking toast notifications.
 */
function showToast(message, type) {
    const toastContainer = $('#toast-container');
    const toast = $('<div>', {
        class: 'p-4 mb-2 rounded-xl shadow-lg text-white transition-opacity duration-300 ease-in-out opacity-0 translate-x-full'
    });

    let colorClass = '';
    if (type === 'success') colorClass = 'bg-emerald-600';
    else if (type === 'error') colorClass = 'bg-red-600';
    else if (type === 'info') colorClass = 'bg-blue-600';
    else colorClass = 'bg-yellow-600';

    toast.addClass(colorClass).text(message);
    toastContainer.append(toast);

    // Fade in
    setTimeout(() => toast.removeClass('opacity-0 translate-x-full'), 10);

    // Fade out and remove after 5 seconds
    setTimeout(() => {
        toast.addClass('opacity-0 translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Copies the referral link to the clipboard.
 */
function copyReferralLink() {
    const input = document.getElementById('referral-link-input');
    input.select();
    input.setSelectionRange(0, 99999);
    try {
        document.execCommand('copy');
        showToast('🔗 Referral link copied to clipboard!', 'info');
    } catch (err) {
        showToast('Could not copy link. Please copy manually.', 'error');
    }
}

/**
 * Manages the visual state of a form button (loading/normal).
 */
function setButtonLoading(buttonId, textId, spinnerId, isLoading, defaultText) {
    const button = $(`#${buttonId}`);
    const text = $(`#${textId}`);
    const spinner = $(`#${spinnerId}`);

    if (isLoading) {
        button.prop('disabled', true);
        text.addClass('hidden');
        spinner.removeClass('hidden');
    } else {
        button.prop('disabled', false);
        text.removeClass('hidden').text(defaultText);
        spinner.addClass('hidden');
    }
}

/**
 * Switches between Login and Register forms visually.
 * NOTE: This is defined globally at the bottom of index.php for home page access.
 */
// function showAuthForm(type) { ... } 


/**
 * Fetches user balance and updates dashboard/wallet balance cards.
 */
function fetchBalance() {
    const balanceEl = $('#token-balance');
    balanceEl.text('---').addClass('animate-pulse'); 

    $.ajax({
        url: CURRENT_FILE,
        type: 'POST',
        dataType: 'json',
        data: { action: 'get_balance' },
        success: function(response) {
            if (response.success) {
                balanceEl.text(response.balance);
            } else {
                balanceEl.text('N/A');
                if (response.message.indexOf('missing') === -1) {
                    showToast('Failed to load wallet balance: ' + response.message, 'error');
                }
            }
        },
        error: function(xhr) {
            balanceEl.text('Error');
            showToast('Network error while loading balance.', 'error');
        },
        complete: function() {
            // FIX: ALWAYS remove the pulse class when the request finishes
            balanceEl.removeClass('animate-pulse');
        }
    });
}

/**
 * Fetches user referral statistics for the Dashboard card.
 */
function fetchReferralStats() {
    const referralsEl = $('#dashboard-referral-count');
    const earningsEl = $('#dashboard-referral-earnings');
    
    // Set initial loading state
    earningsEl.text('---').addClass('animate-pulse');
    referralsEl.text('---').addClass('animate-pulse');


    $.ajax({
        url: CURRENT_FILE,
        type: 'POST',
        dataType: 'json',
        data: { action: 'get_referral_history' },
        success: function(response) {
            if (response.success && response.stats) {
                earningsEl.text(response.stats.total_earnings);
                referralsEl.text(response.stats.total_referrals);
            } else {
                // Default to zero if data is not found or error
                earningsEl.text('0.00');
                referralsEl.text('0');
                // showToast('Failed to load referral stats.', 'error'); // Suppress for dashboard
            }
        },
        error: function() {
             earningsEl.text('0.00');
             referralsEl.text('0');
             // showToast('Failed to load referral stats.', 'error'); // Suppress for dashboard
        },
        complete: function() {
            // Ensure pulse is removed regardless of outcome
            earningsEl.removeClass('animate-pulse');
            referralsEl.removeClass('animate-pulse');
        }
    });
}

/**
 * Simulates a token purchase flow and calls the PHP controller.
 */
function simulateTokenPurchase(usdAmount, bonusPercent) {
    // We assume 1 USD = 1000 Tokens as per config.php
    const baseTokens = usdAmount * 1000; 
    const totalTokens = baseTokens + Math.floor(baseTokens * bonusPercent);

    const modal = $('#payment-modal');
    
    // 1. Setup and Show Modal
    $('#mock-payment-amount').text('$' + usdAmount.toFixed(2));
    $('#mock-payment-tokens').text(new Intl.NumberFormat().format(totalTokens) + ' GALAXY');
    $('#payment-spinner').removeClass('hidden');
    $('#payment-status-text').text("Awaiting block confirmation...");
    modal.removeClass('hidden');
    
    // Disable all buying buttons during simulation
    $('.buy-btn').prop('disabled', true).addClass('opacity-50');

    // 2. Simulate Payment Delay (3 seconds)
    setTimeout(function() {
        // 3. AJAX call to backend to credit tokens
        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'buy_tokens',
                usd_amount: usdAmount,
                bonus_percent: bonusPercent
            },
            success: function(response) {
                $('#payment-spinner').addClass('hidden');
                if (response.success) {
                    $('#payment-status-text').html(`✅ Payment Confirmed! <strong>${response.total_tokens} GALAXY</strong> credited to your account.`);
                    showToast(`✅ Purchase Success! +${response.total_tokens} GALAXY.`, 'success');
                    fetchBalance(); // Update dashboard balance
                    fetchReferralStats(); // Update stats in case earnings changed
                    // If Wallet/Profile is open, refresh their history as well (for full app sync)
                    if (window.fetchTransactionHistory) window.fetchTransactionHistory(); 
                    if (window.fetchProfileData) window.fetchProfileData();

                } else {
                    $('#payment-status-text').text(`❌ Transaction Failed: ${response.message}`);
                    showToast(`❌ Purchase Failed: ${response.message}`, 'error');
                }
            },
            error: function(xhr) {
                $('#payment-spinner').addClass('hidden');
                $('#payment-status-text').text("❌ Server Error during transaction.");
                showToast("❌ Server Error.", 'error');
            },
            complete: function() {
                // Re-enable buttons and close modal after a short delay for user to read the status
                setTimeout(() => {
                    $('.buy-btn').prop('disabled', false).removeClass('opacity-50');
                    modal.addClass('hidden');
                }, 4000);
            }
        });
    }, 3000); // 3 second delay simulation
}

// --- Document Ready / Initialization ---
$(document).ready(function() {
    // Export functions to global scope for use in other JS files (e.g., wallet.js, referrals.js)
    window.copyReferralLink = copyReferralLink;
    window.simulateTokenPurchase = simulateTokenPurchase;
    window.fetchBalance = fetchBalance; 
    window.fetchReferralStats = fetchReferralStats; // Exported for use in referrals.js

    // --- Initialization Check (Determine if we need to fetch data) ---
    if ($('#token-balance').length) {
        // Fetch balance immediately on page load for dashboard/wallet/referrals
        fetchBalance();
    }
    
    // Check if we are on the dashboard specifically to load referral stats
    if ($('#dashboard-referral-earnings').length) {
        fetchReferralStats();
    }


    // Logic for home page forms (only needed if we are NOT logged in)
    if ($('.hero-container').length) {
        // Ensure the correct tab is shown if navigating back to home.
        const urlParams = new URLSearchParams(window.location.search);
        const ref = urlParams.get('ref');
        
        if (ref) {
            $('#referrer_id').val(ref);
            // showAuthForm is defined globally at the bottom of index.php
            window.showAuthForm('register'); 
        } else {
            window.showAuthForm('login');
        }
    } 
    
    // --- Form Handlers ---
    
    // 1. Login Form Submission
    $('#form-login').on('submit', function(e) {
        e.preventDefault();
        const defaultText = 'Sign In';
        setButtonLoading('login-btn', 'login-text', 'login-spinner', true, defaultText);
        $('#login-message').text('');

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'login' });

        $.post(CURRENT_FILE, formData, function(response) {
            setButtonLoading('login-btn', 'login-text', 'login-spinner', false, defaultText);

            if (response.success) {
                showToast(response.message, 'success');
                window.location.href = CURRENT_FILE + '?p=dashboard'; 
            } else {
                $('#login-message').text(response.message);
                showToast(response.message, 'error');
            }
        }).fail(function() {
            setButtonLoading('login-btn', 'login-text', 'login-spinner', false, defaultText);
            $('#login-message').text('Network connection error.');
            showToast('Network connection error.', 'error');
        });
    });

    // 2. Register Form Submission
    $('#form-register').on('submit', function(e) {
        e.preventDefault();
        const defaultText = 'Register & Claim Bonus';
        setButtonLoading('register-btn', 'register-text', 'register-spinner', true, defaultText);
        $('#register-message').text('');

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'register' });

        $.post(CURRENT_FILE, formData, function(response) {
            setButtonLoading('register-btn', 'register-text', 'register-spinner', false, defaultText);
            
            if (response.success) {
                showToast(response.message, 'success');
                window.location.href = CURRENT_FILE + '?p=dashboard'; 
            } else {
                $('#register-message').text(response.message);
                showToast(response.message, 'error');
            }
        }).fail(function() {
            setButtonLoading('register-btn', 'register-text', 'register-spinner', false, defaultText);
            $('#register-message').text('Network connection error.');
            showToast('Network connection error.', 'error');
        });
    });

    // 3. Logout Button Handler
    $('#logout-btn').on('click', function(e) {
        e.preventDefault();
        showToast('Logging out...', 'info');
        $.post(CURRENT_FILE, { action: 'logout' }, function(response) {
            if (response.success) {
                window.location.href = CURRENT_FILE; // Reload to show home page
            } else {
                showToast('Logout failed.', 'error');
            }
        });
    });
});
