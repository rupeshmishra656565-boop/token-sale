// ==============================================================================
// Profile Page Logic (public/assets/profile.js)
// ==============================================================================
$(document).ready(function() {
    const CURRENT_FILE = 'index.php';

    // Access Global Helpers (defined in main.js)
    const showToast = window.showToast;
    const hideModal = () => $('#password-modal').removeClass('flex').addClass('hidden');
    const setButtonLoading = window.setButtonLoading; 

    // Helper to format date
    function formatDate(dateString, includeTime = true) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        if (includeTime) {
            options.hour = '2-digit';
            options.minute = '2-digit';
        }
        // Replace space with 'T' for consistent parsing
        let dateToParse = dateString.replace(' ', 'T'); 
        return new Date(dateToParse).toLocaleDateString(undefined, options);
    }

    /**
     * Helper to render status badge HTML (Replicated from main.js/wallet.js for robustness)
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
     * Fetches user details, lifetime statistics, and recent transactions.
     */
    function fetchProfileData() {
        const pulseElements = [
            '#profile-user-id', '#profile-username', '#profile-email', 
            '#profile-member-since', '#profile-referrer-id', 
            '#stat-tokens-acquired', '#stat-referral-earnings', '#stat-total-purchases'
        ];
        
        // Show loading state
        pulseElements.forEach(id => $(id).addClass('animate-pulse').text('...'));
        const recentTxBody = $('#recent-tx-body');
        const recentTxLoader = $('#recent-activity-loader');
        const recentTxEmpty = $('#recent-activity-empty');
        
        recentTxLoader.show();
        recentTxEmpty.hide();
        recentTxBody.empty();


        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'get_user_details' },
            success: function(response) {
                recentTxLoader.hide();

                if (response.success && response.details) {
                    const details = response.details;
                    const stats = details.stats;

                    // 1. Account Details (Use || 'N/A' for robustness against nulls)
                    $('#profile-user-id').text(details.id || 'N/A');
                    $('#profile-username').text(details.username || 'N/A');
                    $('#profile-email').text(details.email || 'N/A');
                    $('#profile-member-since').text(formatDate(details.member_since, true));
                    
                    const referrerText = details.referrer_id 
                        ? `User ID: ${details.referrer_id}` 
                        : 'No referrer found. Share your link!';
                    $('#profile-referrer-id').text(referrerText);

                    // 2. Lifetime Metrics (Data is fully formatted in PHP, just display it)
                    $('#stat-tokens-acquired').text(stats.tokens_acquired || '0.00');
                    $('#stat-referral-earnings').text(stats.referral_earnings || '0.00');
                    $('#stat-total-purchases').text(stats.total_purchases || '0');

                    // 3. Recent Transactions (Last 5)
                    recentTxBody.empty();
                    const transactions = response.recent_transactions;

                    if (transactions && transactions.length > 0) {
                        transactions.forEach(function(tx) {
                            // Amount is already formatted in PHP (e.g., "125,000.00") or is a raw number (e.g., "-1000.00")
                            const amountStr = String(tx.amount);
                            const amountFloat = parseFloat(amountStr.replace(/,/g, '')); // Strip commas for calculation
                            const isPositive = amountFloat >= 0;
                            const amountClass = isPositive ? 'text-emerald-400' : 'text-red-400';
                            const sign = isPositive && tx.type !== 'WITHDRAWAL' ? '+' : ''; // Only show '+' for income

                            const formattedAmount = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(Math.abs(amountFloat)); // Reformat for display

                            const row = `
                                <tr>
                                    <td class="font-semibold">${tx.type || 'N/A'}</td>
                                    <td class="${amountClass} font-mono">${sign}${formattedAmount}</td>
                                    <td>${getStatusBadge(tx.status || 'N/A')}</td>
                                    <td class="text-gray-400">${formatDate(tx.created_at, false)}</td>
                                </tr>
                            `;
                            recentTxBody.append(row);
                        });
                    } else {
                        recentTxEmpty.show();
                    }

                } else {
                    if (typeof showToast === 'function') {
                        showToast(response.message || 'Failed to fetch profile data.', 'error');
                    }
                    pulseElements.forEach(id => $(id).text('N/A'));
                    recentTxEmpty.show();
                }
            },
            error: function(xhr) {
                recentTxLoader.hide();
                if (typeof showToast === 'function') {
                    showToast('Network error while loading profile.', 'error');
                }
                pulseElements.forEach(id => $(id).text('Error'));
                recentTxBody.html(`<tr><td colspan="4" class="text-center py-10 text-red-400">Network Error.</td></tr>`);
            },
            complete: function() {
                // ALWAYS remove pulse animation
                pulseElements.forEach(id => $(id).removeClass('animate-pulse'));
            }
        });
    }

    // --- Password Update Handler ---
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        const defaultText = 'Update Password';
        
        const messageEl = $('#password-message');
        messageEl.text('').removeClass('text-emerald-400 text-red-400');

        // Check if setButtonLoading is available
        if (typeof setButtonLoading === 'function') {
            setButtonLoading('password-submit-btn', 'password-submit-text', 'password-submit-spinner', true, defaultText);
        }

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'updatePassword' }); // The correct route name

        $.post(CURRENT_FILE, formData, function(response) {
            if (typeof setButtonLoading === 'function') {
                setButtonLoading('password-submit-btn', 'password-submit-text', 'password-submit-spinner', false, defaultText);
            }
            
            if (response.success) {
                messageEl.text(response.message).addClass('text-emerald-400');
                if (typeof showToast === 'function') showToast(response.message, 'success');
                $('#password-form')[0].reset(); // Clear form
                setTimeout(hideModal, 1500);
            } else {
                messageEl.text(response.message).addClass('text-red-400');
                if (typeof showToast === 'function') showToast(response.message, 'error');
            }
        }).fail(function() {
            if (typeof setButtonLoading === 'function') {
                setButtonLoading('password-submit-btn', 'password-submit-text', 'password-submit-spinner', false, defaultText);
            }
            messageEl.text('Network connection failed.').addClass('text-red-400');
            if (typeof showToast === 'function') showToast('Network connection failed.', 'error');
        });
    });

    // --- Modal Control ---
    window.showPasswordModal = () => $('#password-modal').removeClass('hidden').addClass('flex');
    window.hidePasswordModal = hideModal;


    // Run on page load
    fetchProfileData();
});
2