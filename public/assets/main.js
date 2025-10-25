// ==============================================================================
// Frontend AJAX Logic (public/assets/main.js) - DISPLAY DETAILS & POLLING METHOD
// ==============================================================================

const CURRENT_FILE = 'index.php';
let paymentPollingIntervalId = null; // Variable for polling timer
const POLLING_INTERVAL_MS = 5000; // Check every 5 seconds
// TOKEN_RATE is loaded from config.php in views/dashboard.php
// const TOKEN_RATE = 1000.00; 

/** Utility for displaying non-blocking toast notifications. */
function showToast(message, type) {
    const toastContainer = $('#toast-container');
    const toast = $('<div>', { class: 'p-4 mb-2 rounded-xl shadow-lg text-white transition-all duration-300 ease-in-out opacity-0 translate-x-full'});
    let colorClass = '';
    let iconSvg = '';
    
    if (type === 'success') {
        colorClass = 'bg-emerald-600 border border-emerald-500/50';
        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    } else if (type === 'error') {
        colorClass = 'bg-red-600 border border-red-500/50';
        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    } else {
        colorClass = 'bg-blue-600 border border-blue-500/50';
        iconSvg = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    }
    
    toast.addClass(colorClass).html(`
        <div class="flex items-center gap-3">
            ${iconSvg}
            <span class="text-sm font-medium">${message}</span>
        </div>
    `);
    
    toastContainer.append(toast);
    setTimeout(() => toast.removeClass('opacity-0 translate-x-full'), 10);
    setTimeout(() => { toast.addClass('opacity-0 translate-x-full'); setTimeout(() => toast.remove(), 300); }, 5000);
}


/** Copies text to the clipboard (generalized). */
function copyToClipboard(text, elementId) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('✅ Copied!', 'success');
            if(elementId) { const el = $(`#${elementId}`); const originalHtml = el.html(); el.html('Copied!'); setTimeout(() => el.html(originalHtml), 1500); }
        }).catch(err => { showToast('❌ Copy failed.', 'error'); console.error('Clipboard API error:', err); });
    } else { /* Fallback */ const textArea = document.createElement("textarea"); textArea.value = text; textArea.style.position = "fixed"; document.body.appendChild(textArea); textArea.focus(); textArea.select(); try { document.execCommand('copy'); showToast('✅ Copied!', 'success'); if(elementId) { const el = $(`#${elementId}`); const originalHtml = el.html(); el.html('Copied!'); setTimeout(() => el.html(originalHtml), 1500); }} catch (err) { showToast('❌ Copy failed.', 'error'); } document.body.removeChild(textArea); }
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
        success: function(response) { if (response.success) { balanceEl.text(response.balance); } else { balanceEl.text('N/A'); }},
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

/** Step 1: Show payment modal with currency options. */
function showPaymentOptions(usdAmount, bonusPercent) {
    const modal = $('#payment-modal');
    // Ensure TOKEN_RATE is available globally (it's set in dashboard.php)
    const TOKEN_RATE = window.TOKEN_RATE || 1000.00; 
    
    $('#mock-payment-amount').text('$' + usdAmount.toFixed(2));
    const baseTokens = usdAmount * TOKEN_RATE; const totalTokens = baseTokens + Math.floor(baseTokens * bonusPercent);
    $('#mock-payment-tokens').text(new Intl.NumberFormat().format(totalTokens) + ' <?php echo TOKEN_SYMBOL; ?>');
    modal.attr('data-usd-amount', usdAmount); modal.attr('data-bonus-percent', bonusPercent);
    $('#payment-options').removeClass('hidden'); $('#payment-processing-status').addClass('hidden'); $('#payment-details-display').addClass('hidden');
    $('#modal-title').find('span').text('Confirm Your Purchase'); stopPaymentPolling(); 
    
    // Show modal using classes for animation
    modal.removeClass('hidden').addClass('flex');
    setTimeout(() => modal.find('.glass-card').removeClass('scale-95'), 10); // Animate in
}


/** Step 2: Call backend to get payment details for selected currency. */
function confirmTokenPurchase(selectedCurrency) {
    const modal = $('#payment-modal'); const usdAmount = parseFloat(modal.attr('data-usd-amount')); const bonusPercent = parseFloat(modal.attr('data-bonus-percent'));
    $('#payment-options').addClass('hidden'); $('#payment-details-display').addClass('hidden'); $('#payment-processing-status').removeClass('hidden');
    $('#payment-status-text').text("Creating payment request...").removeClass('text-red-400 text-emerald-400'); // Reset color
    $('#payment-spinner').removeClass('hidden');
    $('#modal-title').find('span').text('Processing Request...');
    const buyButtons = $('button[onclick^="showPaymentOptions"]'); buyButtons.prop('disabled', true).addClass('opacity-50');
    $.ajax({
        url: CURRENT_FILE, type: 'POST', dataType: 'json',
        data: { action: 'create_payment_invoice', usd_amount: usdAmount, bonus_percent: bonusPercent, pay_currency: selectedCurrency },
        success: function(response) {
            if (response.success && response.pay_address && response.pay_amount && response.transaction_id) {
                // Store transaction_id on the modal
                modal.attr('data-transaction-id', response.transaction_id); 
                displayPaymentDetails(response.pay_address, response.pay_amount, response.pay_currency, response.transaction_id);
            } else {
                $('#payment-spinner').addClass('hidden'); $('#payment-status-text').html(`<span class="text-red-400">❌ Error: ${response.message || 'Could not create payment.'}</span>`);
                showToast(response.message || 'Could not create payment.', 'error');
                setTimeout(() => { buyButtons.prop('disabled', false).removeClass('opacity-50'); closePaymentModal(false); }, 4000); // Pass false to not try cancelling
            }
        },
        error: function(xhr) {
            $('#payment-spinner').addClass('hidden'); $('#payment-status-text').html('<span class="text-red-400">❌ Network Error.</span>'); showToast("Network Error.", 'error'); buyButtons.prop('disabled', false).removeClass('opacity-50');
            closePaymentModal(false); // Pass false to not try cancelling
        }
    });
}

/** Step 3: Display payment details and START POLLING. */
function displayPaymentDetails(address, amount, currency, transactionId) {
    $('#payment-processing-status').addClass('hidden'); $('#payment-details-display').removeClass('hidden'); $('#modal-title').find('span').text('Make Your Payment');
    const currencyUpper = currency ? currency.toUpperCase() : 'UNKNOWN';
    $('#payment-details-currency').text(currencyUpper); $('#payment-details-amount').text(amount); $('#payment-details-address').text(address);
    const copyIconSvg = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>';
    $('#copy-amount-btn').html(copyIconSvg).attr('onclick', `copyToClipboard('${amount}', 'copy-amount-btn')`);
    $('#copy-address-btn').html(copyIconSvg).attr('onclick', `copyToClipboard('${address}', 'copy-address-btn')`);
    let qrData = address;
    if (currencyUpper === 'TRX' || currencyUpper === 'USDTTRC20') { qrData = `tron:${address}?amount=${amount}`; }
    else if (currencyUpper === 'USDTBSC') { qrData = `bep20:${address}?amount=${amount}`; }
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(qrData)}`;
    $('#payment-qr-code').html(`<img src="${qrCodeUrl}" alt="QR Code" class="mx-auto border-4 border-white/50 rounded-lg shadow-lg">`);
    $('#payment-instructions').html(`<span id="polling-status">Waiting for confirmation...</span><span class="inline-block animate-pulse delay-100">.</span><span class="inline-block animate-pulse delay-200">.</span><span class="inline-block animate-pulse delay-300">.</span>`);
    startPaymentPolling(transactionId);
}

/** Starts polling server for payment status. */
function startPaymentPolling(transactionId) {
    stopPaymentPolling(); if (!transactionId) { console.error("No tx ID for polling."); return; }
    console.log(`Start poll tx: ${transactionId}`);
    paymentPollingIntervalId = setInterval(() => { checkPaymentStatus(transactionId); }, POLLING_INTERVAL_MS);
}

/** Stops payment status polling. */
function stopPaymentPolling() {
    if (paymentPollingIntervalId) { console.log("Stop poll."); clearInterval(paymentPollingIntervalId); paymentPollingIntervalId = null; }
}

/** Makes AJAX call to check payment status via our backend. */
function checkPaymentStatus(transactionId) {
    if (!transactionId) return; console.log(`Check status tx: ${transactionId}`);
    $.ajax({
        url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'get_payment_status', transaction_id: transactionId },
        success: function(response) {
            console.log("Status check:", response);
            if (response.success) {
                const status = response.status; const modal = $('#payment-modal'); const buyButtons = $('button[onclick^="showPaymentOptions"]');
                if (status === 'Complete') {
                    stopPaymentPolling();
                    modal.removeAttr('data-transaction-id'); // Clear tx_id from modal
                    $('#payment-details-display').addClass('hidden'); 
                    $('#payment-processing-status').removeClass('hidden').html(`<div class="text-center py-6"><svg class="w-16 h-16 text-emerald-500 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-2xl font-bold text-emerald-400">Payment Confirmed!</p><p class="text-gray-400 mt-2">Tokens credited.</p></div>`);
                    $('#modal-title').find('span').text('Payment Success'); 
                    showToast('✅ Payment Confirmed!', 'success'); 
                    if (typeof fetchBalance === 'function') fetchBalance(); 
                    if (typeof fetchDashboardActivity === 'function') fetchDashboardActivity(); // Refresh dashboard activity too
                    if (typeof fetchTransactionHistory === 'function') fetchTransactionHistory(); 
                    setTimeout(() => { 
                        closePaymentModal(false); // Pass false to not try cancelling
                        buyButtons.prop('disabled', false).removeClass('opacity-50'); 
                    }, 3500);
                } else if (status === 'Failed') {
                    stopPaymentPolling();
                    modal.removeAttr('data-transaction-id'); // Clear tx_id from modal
                    $('#payment-details-display').addClass('hidden'); 
                    $('#payment-processing-status').removeClass('hidden').html(`<div class="text-center py-6"><svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-2xl font-bold text-red-400">Payment Failed</p><p class="text-gray-400 mt-2">Not confirmed, cancelled, or expired.</p></div>`);
                    $('#modal-title').find('span').text('Payment Failed'); 
                    showToast('❌ Payment Failed.', 'error'); 
                    buyButtons.prop('disabled', false).removeClass('opacity-50');
                    setTimeout(() => {
                        closePaymentModal(false); // Pass false to not try cancelling
                    }, 4000); 
                } else { /* Still Processing */ 
                    console.log(`Status: ${status}, polling...`); 
                    let txt = $('#polling-status').text(); 
                    $('#polling-status').text(txt.endsWith('...') ? 'Waiting for confirmation.' : txt + '.'); 
                }
            } else { console.error("Error fetching status:", response.message); }
        },
        error: function(xhr) { console.error("Net error checking status."); }
    });
}

/** Function to close the payment modal and potentially cancel the payment. */
function closePaymentModal(tryCancel = true) {
    const modal = $('#payment-modal');
    const transactionId = modal.attr('data-transaction-id');
    
    stopPaymentPolling(); // Always stop polling

    if (tryCancel && transactionId) {
        console.log(`User closed modal for TxID: ${transactionId}. Attempting cancellation.`);
        // Make AJAX call to backend to mark as Failed IF still Processing
        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { 
                action: 'cancel_payment', 
                transaction_id: transactionId 
            },
            success: function(response) {
                if (response.success) {
                    console.log(`Cancellation status for TxID ${transactionId}: ${response.message}`);
                     if (response.message.includes('cancelled')) {
                         showToast('ℹ️ Payment cancelled.', 'info');
                     }
                    // Refresh history to show 'Failed' status
                    if (typeof fetchDashboardActivity === 'function') fetchDashboardActivity();
                    if (typeof fetchTransactionHistory === 'function') fetchTransactionHistory();
                } else {
                    console.error(`Cancellation failed for TxID ${transactionId}: ${response.message}`);
                }
            },
            error: function() {
                console.error(`Network error during cancellation attempt for TxID ${transactionId}.`);
            },
            complete: function() {
                hideAndResetModal();
            }
        });
    } else {
         // If no transaction ID or tryCancel is false, just hide
        hideAndResetModal();
    }
}

// --- NEW HELPER: Hides and resets the modal UI ---
function hideAndResetModal() {
    const modal = $('#payment-modal');
    
    // Animate out
    modal.find('.glass-card').addClass('scale-95');
    modal.addClass('hidden').removeClass('flex'); // Hide it
    
    modal.removeAttr('data-transaction-id'); // Clear tx_id
    $('button[onclick^="showPaymentOptions"]').prop('disabled', false).removeClass('opacity-50');
    
    // Reset modal content to initial state after a short delay
    setTimeout(() => {
        $('#payment-options').removeClass('hidden'); 
        $('#payment-processing-status').addClass('hidden').html(`
            <div id="payment-spinner" class="animate-spin inline-block w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full mb-4"></div>
            <p id="payment-status-text" class="text-gray-400 text-sm">Creating payment request...</p>
        `); // Restore spinner and text
        $('#payment-details-display').addClass('hidden'); 
        $('#modal-title').find('span').text('Confirm Purchase');
    }, 300); // Wait for fade out
}


// --- Document Ready / Initialization ---
$(document).ready(function() {
    // Export functions
    window.copyReferralLink = copyReferralLink; 
    window.setButtonLoading = setButtonLoading; 
    window.fetchBalance = fetchBalance; 
    window.fetchReferralStats = fetchReferralStats;
    window.showPaymentOptions = showPaymentOptions; 
    window.confirmTokenPurchase = confirmTokenPurchase; 
    window.displayPaymentDetails = displayPaymentDetails;
    window.copyToClipboard = copyToClipboard; 
    window.closePaymentModal = closePaymentModal;

    // --- Init Checks ---
    if ($('#token-balance').length) { fetchBalance(); } 
    if ($('#dashboard-referral-earnings').length) { fetchReferralStats(); }
    
    // This logic is now handled in index.php's inline script
    // if ($('#form-login').length) { ... }

    // --- Form Handlers ---

    // Login Form
    $('#form-login').on('submit', function(e) {
        e.preventDefault(); const defaultText = 'Sign In'; setButtonLoading('login-btn', 'login-text', 'login-spinner', true, defaultText); $('#login-message').text('');
        const formData = $(this).serializeArray(); formData.push({ name: 'action', value: 'login' });
        $.post(CURRENT_FILE, formData, function(response) {
            setButtonLoading('login-btn', 'login-text', 'login-spinner', false, defaultText);
            if (response.success) { showToast(response.message, 'success'); window.location.href = CURRENT_FILE + '?p=dashboard'; }
            else { $('#login-message').text(response.message).addClass('text-red-400'); showToast(response.message, 'error'); }
        }).fail(function() { setButtonLoading('login-btn', 'login-text', 'login-spinner', false, defaultText); $('#login-message').text('Network error.').addClass('text-red-400'); showToast('Network error.', 'error'); });
    });

    // Register Form
    $('#form-register').on('submit', function(e) {
        e.preventDefault(); const defaultText = 'Register & Claim Bonus'; const btnId = 'register-btn'; const textId = 'register-text'; const spinnerId = 'register-spinner';
        setButtonLoading(btnId, textId, spinnerId, true, defaultText); $('#register-message').text('');
        const formData = $(this).serializeArray(); formData.push({ name: 'action', value: 'register' });
        $.post(CURRENT_FILE, formData, function(response) {
            setButtonLoading(btnId, textId, spinnerId, false, defaultText);
            if (response.success) { showToast(response.message, 'success'); window.location.href = CURRENT_FILE + '?p=dashboard'; }
            else { $('#register-message').text(response.message || 'Registration failed.').addClass('text-red-400'); showToast(response.message || 'Registration failed.', 'error'); }
        }).fail(function() { setButtonLoading(btnId, textId, spinnerId, false, defaultText); $('#register-message').text('Network error.').addClass('text-red-400'); showToast('Network error.', 'error'); });
    });
    
    // Logout Button
    $('#logout-btn, #logout-btn-mobile').on('click', function(e) { 
        e.preventDefault(); showToast('Logging out...', 'info');
        $.post(CURRENT_FILE, { action: 'logout' }, function(response) { if (response.success) { window.location.href = CURRENT_FILE; } else { showToast('Logout failed.', 'error'); } });
    });

}); // End document ready