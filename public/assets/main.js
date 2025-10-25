// ==============================================================================
// Frontend AJAX Logic (public/assets/main.js) - DISPLAY DETAILS & POLLING METHOD
// Email/OTP features REMOVED
// ==============================================================================

const CURRENT_FILE = 'index.php';
let paymentPollingIntervalId = null; // Variable for polling timer
const POLLING_INTERVAL_MS = 5000; // Check every 5 seconds
// Define TOKEN_RATE globally
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
        success: function(response) { if (response.success) { balanceEl.text(response.balance); } else { balanceEl.text('N/A'); /* showToast('Failed: '+response.message, 'error'); */ }},
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
    $('#mock-payment-amount').text('$' + usdAmount.toFixed(2));
    const baseTokens = usdAmount * TOKEN_RATE; const totalTokens = baseTokens + Math.floor(baseTokens * bonusPercent);
    $('#mock-payment-tokens').text(new Intl.NumberFormat().format(totalTokens) + ' GALAXY');
    modal.attr('data-usd-amount', usdAmount); modal.attr('data-bonus-percent', bonusPercent);
    $('#payment-options').removeClass('hidden'); $('#payment-processing-status').addClass('hidden'); $('#payment-details-display').addClass('hidden');
    $('#modal-title').find('span').text('Confirm Your Purchase'); stopPaymentPolling(); modal.removeClass('hidden');
}

/** Step 2: Call backend to get payment details for selected currency. */
function confirmTokenPurchase(selectedCurrency) {
    const modal = $('#payment-modal'); const usdAmount = parseFloat(modal.attr('data-usd-amount')); const bonusPercent = parseFloat(modal.attr('data-bonus-percent'));
    $('#payment-options').addClass('hidden'); $('#payment-details-display').addClass('hidden'); $('#payment-processing-status').removeClass('hidden');
    $('#payment-status-text').text("Creating payment request...").removeClass('text-red-400'); $('#payment-spinner').removeClass('hidden');
    $('#modal-title').find('span').text('Processing Request...');
    const buyButtons = $('button[onclick^="showPaymentOptions"]'); buyButtons.prop('disabled', true).addClass('opacity-50');
    $.ajax({
        url: CURRENT_FILE, type: 'POST', dataType: 'json',
        data: { action: 'create_payment_invoice', usd_amount: usdAmount, bonus_percent: bonusPercent, pay_currency: selectedCurrency },
        success: function(response) {
            if (response.success && response.pay_address && response.pay_amount && response.transaction_id) {
                displayPaymentDetails(response.pay_address, response.pay_amount, response.pay_currency, response.transaction_id);
            } else {
                $('#payment-spinner').addClass('hidden'); $('#payment-status-text').html(`<span class="text-red-400">❌ Error: ${response.message || 'Could not create payment.'}</span>`);
                showToast(response.message || 'Could not create payment.', 'error');
                setTimeout(() => { buyButtons.prop('disabled', false).removeClass('opacity-50'); modal.addClass('hidden'); $('#payment-options').removeClass('hidden'); $('#payment-processing-status').addClass('hidden'); $('#modal-title').find('span').text('Confirm Purchase'); }, 4000);
            }
        },
        error: function(xhr) {
            $('#payment-spinner').addClass('hidden'); $('#payment-status-text').html('<span class="text-red-400">❌ Network Error.</span>'); showToast("Network Error.", 'error'); buyButtons.prop('disabled', false).removeClass('opacity-50');
            $('#payment-options').removeClass('hidden'); $('#payment-processing-status').addClass('hidden'); $('#modal-title').find('span').text('Confirm Purchase');
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
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(qrData)}`;
    $('#payment-qr-code').html(`<img src="${qrCodeUrl}" alt="QR Code" class="mx-auto border-4 border-white/50 rounded-lg shadow-lg">`);
    $('#payment-instructions').html(`<span id="polling-status">Waiting for confirmation...</span>`);
    startPaymentPolling(transactionId);
}

/** Starts polling server for payment status. */
function startPaymentPolling(transactionId) {
    stopPaymentPolling(); if (!transactionId) { console.error("No tx ID for polling."); return; }
    console.log(`Start poll tx: ${transactionId}`);
    paymentPollingIntervalId = setInterval(() => { checkPaymentStatus(transactionId); }, POLLING_INTERVAL_MS);
    $('#payment-modal').attr('data-transaction-id', transactionId);
}

/** Stops payment status polling. */
function stopPaymentPolling() {
    if (paymentPollingIntervalId) { console.log("Stop poll."); clearInterval(paymentPollingIntervalId); paymentPollingIntervalId = null; $('#payment-modal').removeAttr('data-transaction-id'); }
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
                    $('#payment-details-display').addClass('hidden'); $('#payment-processing-status').removeClass('hidden').html(`<div class="text-center py-6"><svg class="w-16 h-16 text-emerald-500 mx-auto mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-2xl font-bold text-emerald-400">Payment Confirmed!</p><p class="text-gray-400 mt-2">Tokens credited.</p></div>`);
                    $('#modal-title').find('span').text('Payment Success'); showToast('✅ Payment Confirmed!', 'success'); fetchBalance(); if (typeof fetchTransactionHistory === 'function') { fetchTransactionHistory(); }
                    setTimeout(() => { modal.addClass('hidden'); buyButtons.prop('disabled', false).removeClass('opacity-50'); $('#payment-options').removeClass('hidden'); $('#payment-processing-status').addClass('hidden').find('#payment-spinner').removeClass('hidden'); $('#payment-details-display').addClass('hidden'); $('#modal-title').find('span').text('Confirm Purchase'); }, 3500);
                } else if (status === 'Failed') {
                    stopPaymentPolling();
                    $('#payment-details-display').addClass('hidden'); $('#payment-processing-status').removeClass('hidden').html(`<div class="text-center py-6"><svg class="w-16 h-16 text-red-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-2xl font-bold text-red-400">Payment Failed</p><p class="text-gray-400 mt-2">Not confirmed or cancelled.</p></div>`);
                    $('#modal-title').find('span').text('Payment Failed'); showToast('❌ Payment Failed.', 'error'); buyButtons.prop('disabled', false).removeClass('opacity-50');
                } else { /* Still Pending */ console.log(`Status: ${status}, polling...`); let txt = $('#polling-status').text(); $('#polling-status').text(txt.endsWith('...') ? 'Waiting...' : txt + '.'); }
            } else { console.error("Error fetching status:", response.message); }
        },
        error: function(xhr) { console.error("Net error checking status."); }
    });
}

/** Function to close the payment modal and stop polling. */
function closePaymentModal() {
    stopPaymentPolling(); $('#payment-modal').addClass('hidden');
    $('button[onclick^="showPaymentOptions"]').prop('disabled', false).removeClass('opacity-50');
    $('#payment-options').removeClass('hidden'); $('#payment-processing-status').addClass('hidden').find('#payment-spinner').removeClass('hidden'); $('#payment-details-display').addClass('hidden'); $('#modal-title').find('span').text('Confirm Purchase');
}

// --- Document Ready / Initialization ---
$(document).ready(function() {
    // Export functions
    window.copyReferralLink = copyReferralLink; window.setButtonLoading = setButtonLoading; window.fetchBalance = fetchBalance; window.fetchReferralStats = fetchReferralStats;
    window.showPaymentOptions = showPaymentOptions; window.confirmTokenPurchase = confirmTokenPurchase; window.displayPaymentDetails = displayPaymentDetails;
    window.copyToClipboard = copyToClipboard; window.closePaymentModal = closePaymentModal;
    // Deprecated pointers
    window.initiateTokenPurchase = showPaymentOptions; window.simulateTokenPurchase = showPaymentOptions;

    // --- Init Checks ---
    if ($('#token-balance').length) { fetchBalance(); } if ($('#dashboard-referral-earnings').length) { fetchReferralStats(); }
     if ($('#form-login').length) { if(typeof showAuthForm === 'function') { showAuthForm('login'); const urlParams = new URLSearchParams(window.location.search); const ref = urlParams.get('ref'); if (ref) { $('#referrer_id').val(ref); showAuthForm('register'); } } else { console.error("showAuthForm missing."); }}

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

    // 2. Register Form Submission (Simple, no OTP)
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
    
    // 3. Forgot Password Form Handler REMOVED

    // 4. Logout Button Handler
    $('#logout-btn, #logout-btn-mobile').on('click', function(e) { // Combined selector
        e.preventDefault(); showToast('Logging out...', 'info');
        $.post(CURRENT_FILE, { action: 'logout' }, function(response) { if (response.success) { window.location.href = CURRENT_FILE; } else { showToast('Logout failed.', 'error'); } });
    });

}); // End document ready