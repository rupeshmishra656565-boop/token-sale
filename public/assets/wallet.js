$(document).ready(function() {
    const CURRENT_FILE = 'index.php';

    // --- Access Global Helpers ---
    // Safely assign global functions defined in main.js
    const setButtonLoading = window.setButtonLoading;
    const fetchBalance = window.fetchBalance;
    const showToast = window.showToast;

    // --- State Elements ---
    const withdrawMessage = $('#withdrawal-message');
    const historyBody = $('#transaction-history-body');
    const historyLoader = $('#history-loader');
    const historyEmpty = $('#history-empty');

    /**
     * Helper to render status badge HTML
     */
    function getStatusBadge(status) {
        let badgeClass = '';
        if (status === 'Complete') badgeClass = 'status-complete';
        else if (status === 'Processing') badgeClass = 'status-processing';
        else if (status === 'Failed') badgeClass = 'status-failed';
        else badgeClass = 'status-pending'; // Default/Unknown
        
        return `<span class="status-badge ${badgeClass}"><span class="status-badge-dot"></span>${status}</span>`;
    }

    /**
     * Fetches user transaction history and updates the table.
     */
    function fetchTransactionHistory() {
        historyLoader.show();
        historyEmpty.hide();
        historyBody.empty();

        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'get_transaction_history' },
            success: function(response) {
                historyLoader.hide();

                // Check for the specific DB error message (if the transactions table is still missing)
                if (response.message && response.message.indexOf('DB Error: Transactions table missing') > -1) {
                    historyBody.html(`<tr><td colspan="5" class="text-center py-10 text-red-400 font-semibold">${response.message}</td></tr>`);
                    return;
                }

                if (response.success && response.history.length > 0) {
                    response.history.forEach(function(tx) {
                        // Ensure amount is treated as a number
                        const amount = parseFloat(tx.amount);
                        const isPositive = amount >= 0; // Purchase/Signup/Referral are >= 0
                        const amountClass = isPositive ? 'text-emerald-400' : 'text-red-400';
                        const sign = isPositive ? '+' : '';
                        const formattedAmount = sign + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(Math.abs(amount));

                        // Parse date string
                        const date = new Date(tx.created_at).toLocaleDateString("en-US", { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                        const row = `
                            <tr>
                                <td class="font-semibold">${tx.type}</td>
                                <td class="${amountClass} font-mono">${formattedAmount}</td>
                                <td>${getStatusBadge(tx.status)}</td>
                                <td class="text-gray-400">${date}</td>
                                <td class="text-gray-400">${tx.details || 'N/A'}</td>
                            </tr>
                        `;
                        historyBody.append(row);
                    });
                } else {
                    historyEmpty.show();
                }
            },
            error: function() {
                historyLoader.hide();
                historyBody.html(`<tr><td colspan="5" class="text-center py-10 text-red-400">A network error occurred while loading history.</td></tr>`);
            }
        });
    }

    // --- Form Submission Handler ---
    $('#withdrawal-form').on('submit', function(e) {
        e.preventDefault();
        const defaultText = 'Initiate Withdrawal';

        // Use global helper function safely
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

                // Refresh balance and history on success
                if (typeof fetchBalance === 'function') fetchBalance();
                fetchTransactionHistory();
                $('#withdrawal-form')[0].reset(); // Clear form

            } else {
                withdrawMessage.text(response.message).addClass('text-red-400');
                if (typeof showToast === 'function') showToast(response.message, 'error');
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
    // fetchBalance() is called from main.js, but we call it here for robustness if main.js is slow
    if (typeof fetchBalance === 'function') {
        fetchBalance();
    } 
    fetchTransactionHistory();
});
