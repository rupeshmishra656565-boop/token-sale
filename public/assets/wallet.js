$(document).ready(function() {
    const CURRENT_FILE = 'index.php';

    // --- Access Global Helpers ---
    const setButtonLoading = window.setButtonLoading;
    // We will fetch detailed balance here, not just the total from main.js
    // const fetchBalance = window.fetchBalance; 
    const showToast = window.showToast;

    // --- State Elements ---
    const withdrawMessage = $('#withdrawal-message');
    const historyBody = $('#transaction-history-body');
    const historyLoader = $('#history-loader');
    const historyEmpty = $('#history-empty');

    // Balance elements
    const totalBalanceEl = $('#total-balance');
    const purchasedBalanceEl = $('#purchased-balance');
    const bonusBalanceEl = $('#bonus-balance');
    const referralBalanceEl = $('#referral-balance');
    const withdrawableBalanceEl = $('#withdrawable-balance');
    const balanceElements = [totalBalanceEl, purchasedBalanceEl, bonusBalanceEl, referralBalanceEl, withdrawableBalanceEl];


    /**
     * Helper to render status badge HTML
     */
    function getStatusBadge(status) {
        let badgeClass = '';
        let statusText = status || 'Unknown';
        switch (status ? status.toLowerCase() : '') {
            case 'complete': badgeClass = 'status-complete'; break;
            case 'processing': badgeClass = 'status-processing'; break;
            case 'failed': badgeClass = 'status-failed'; break;
            case 'pending': badgeClass = 'status-pending'; break;
            default: badgeClass = 'status-unknown'; statusText = 'Unknown'; break;
        }
        return `<span class="status-badge ${badgeClass}">${statusText}</span>`;
    }

    /**
     * Helper to get transaction type icon SVG
     */
    function getTypeIcon(type) {
        let iconSvg = '<svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.79 4 4s-1.79 4-4 4c-1.742 0-3.223-.835-3.772-2M12 12h.01M12 12L12 6m0 6l0 6"></path></svg>'; // Default icon
        let typeText = type || 'Unknown';

        switch (type ? type.toLowerCase() : '') {
            case 'purchase':
                iconSvg = '<svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>';
                typeText = 'Purchase';
                break;
            case 'withdrawal':
                iconSvg = '<svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>';
                typeText = 'Withdrawal';
                break;
            case 'referral':
                iconSvg = '<svg class="w-4 h-4 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>';
                typeText = 'Referral';
                break;
            case 'signup':
                iconSvg = '<svg class="w-4 h-4 text-cyan-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>';
                typeText = 'Signup';
                break;
             case 'adjust_in':
             case 'adjust_out': // Group adjustments
                iconSvg = '<svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>';
                typeText = 'Adjustment';
                break;
        }
        return { icon: iconSvg, text: typeText };
    }

    /**
     * Fetches detailed balances and updates the UI.
     */
    function fetchDetailedBalance() {
        balanceElements.forEach(el => el.text('---').addClass('animate-pulse'));

        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'get_detailed_balance' }, // Use the new action
            success: function(response) {
                if (response.success && response.balances) {
                    const bal = response.balances;
                    totalBalanceEl.text(bal.total || '0.00');
                    purchasedBalanceEl.text(bal.purchased || '0.00');
                    bonusBalanceEl.text(bal.bonus || '0.00');
                    referralBalanceEl.text(bal.referral || '0.00');
                    withdrawableBalanceEl.text(bal.withdrawable || '0.00');
                } else {
                    balanceElements.forEach(el => el.text('N/A'));
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Failed to load balances.', 'error');
                    }
                }
            },
            error: function() {
                balanceElements.forEach(el => el.text('Error'));
                if (typeof showToast === 'function') {
                    showToast('Network error loading balances.', 'error');
                }
            },
            complete: function() {
                balanceElements.forEach(el => el.removeClass('animate-pulse'));
                // Trigger USD update after balances load (handled by observer/interval in HTML)
            }
        });
    }


    /**
     * Fetches user transaction history and updates the table.
     */
    function fetchTransactionHistory() {
        historyLoader.removeClass('hidden'); // Show loader
        historyEmpty.addClass('hidden');
        historyBody.empty();

        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'get_transaction_history' },
            success: function(response) {
                historyLoader.addClass('hidden'); // Hide loader

                if (response.success && response.history && response.history.length > 0) {
                    response.history.forEach(function(tx) {
                        const amount = parseFloat(tx.amount);
                        const isPositive = amount >= 0;
                        const amountClass = isPositive ? 'text-emerald-400' : 'text-red-400';
                        const sign = isPositive ? '+' : '';
                        const formattedAmount = sign + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(Math.abs(amount));

                        const date = new Date(tx.created_at.replace(' ', 'T')).toLocaleDateString("en-US", { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                        const typeInfo = getTypeIcon(tx.type);

                        const row = `
                            <tr class="animate-fadeIn">
                                <td class="py-4 px-4 text-sm font-medium text-gray-200">
                                    <div class="flex items-center gap-2"> ${typeInfo.icon} <span>${typeInfo.text}</span></div>
                                </td>
                                <td class="py-4 px-4 text-right ${amountClass} font-mono">${formattedAmount}</td>
                                <td class="py-4 px-4 text-center">${getStatusBadge(tx.status)}</td>
                                <td class="py-4 px-4 text-xs text-gray-400 whitespace-nowrap">${date}</td>
                                <td class="py-4 px-4 text-xs text-gray-500 max-w-[150px] truncate" title="${tx.details || ''}">${tx.details || 'N/A'}</td>
                            </tr>
                        `;
                        historyBody.append(row);
                    });
                } else {
                    historyEmpty.removeClass('hidden'); // Show empty state
                    if (!response.success && response.message) {
                        historyEmpty.html(`<p class="text-red-400">${response.message}</p>`);
                    }
                }
            },
            error: function() {
                historyLoader.addClass('hidden'); // Hide loader on error too
                historyEmpty.removeClass('hidden').html('<p class="text-red-400">Network error loading history.</p>');
            }
        });
    }

    // --- Form Submission Handler ---
    $('#withdrawal-form').on('submit', function(e) {
        e.preventDefault();
        const defaultText = 'Initiate Withdrawal';

        if (typeof setButtonLoading === 'function') {
            setButtonLoading('withdraw-btn', 'withdraw-text', 'withdraw-spinner', true, defaultText);
        }

        withdrawMessage.text('').removeClass('text-emerald-400 text-red-400');
        
        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'withdraw_tokens' });

        $.post(CURRENT_FILE, formData, function(response) {
            if (typeof setButtonLoading === 'function') {
                setButtonLoading('withdraw-btn', 'withdraw-text', 'withdraw-spinner', false, defaultText);
            }
            
            if (response.success) {
                withdrawMessage.text(response.message).addClass('text-emerald-400');
                if (typeof showToast === 'function') showToast(response.message, 'success');

                // Refresh balances and history on success
                fetchDetailedBalance(); // Refresh the detailed balances
                fetchTransactionHistory(); // Refresh the history table
                $('#withdrawal-form')[0].reset(); // Clear form

            } else {
                withdrawMessage.text(response.message || 'Withdrawal failed.').addClass('text-red-400');
                if (typeof showToast === 'function') showToast(response.message || 'Withdrawal failed.', 'error');
            }
        }).fail(function() {
            if (typeof setButtonLoading === 'function') {
                setButtonLoading('withdraw-btn', 'withdraw-text', 'withdraw-spinner', false, defaultText);
            }
            withdrawMessage.text('Network connection error.').addClass('text-red-400');
            if (typeof showToast === 'function') showToast('Network connection error.', 'error');
        });
    });

    // --- Initialization ---
    fetchDetailedBalance(); // Fetch detailed balances on page load
    fetchTransactionHistory(); // Fetch history on page load
});

