// ==============================================================================
// Referral Page Logic (public/assets/referrals.js)
// ==============================================================================
$(document).ready(function() {
    const CURRENT_FILE = 'index.php';
    
    // Define the constant value locally (must match config.php: 1000)
    const REFERRAL_BONUS = 1000; 
    const showToast = window.showToast; // Access global helper

    // Helper functions
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        // Parse date string
        let dateToParse = dateString.replace(' ', 'T'); 
        return new Date(dateToParse).toLocaleDateString("en-US", { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }


    /**
     * Fetches user referral history and updates the table and stats cards.
     */
    function fetchReferralHistory() {
        const totalReferralsEl = $('#total-referrals');
        const totalEarningsEl = $('#total-earnings');
        const historyBody = $('#referral-history-body');
        const loader = $('#history-loader');
        const emptyState = $('#history-empty');

        // Initial state: Set all to loading and show spinner
        loader.show();
        emptyState.hide();
        totalReferralsEl.text('---').addClass('animate-pulse');
        totalEarningsEl.text('...').addClass('animate-pulse');
        historyBody.empty();


        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'get_referral_history' },
            success: function(response) {
                // Ensure pulse is removed on success
                totalReferralsEl.removeClass('animate-pulse');
                totalEarningsEl.removeClass('animate-pulse');


                if (response.success && response.stats) {
                    // Update stats cards
                    totalReferralsEl.text(response.stats.total_referrals);
                    totalEarningsEl.text(response.stats.total_earnings);

                    if (response.history && response.history.length > 0) {
                        // Populate table
                        response.history.forEach(function(referral) {
                            
                            // Use the local constant for the bonus amount display
                            const bonus = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(REFERRAL_BONUS); 
                            
                            const row = `
                                <tr>
                                    <td class="py-4 px-6">${escapeHtml(referral.username)}</td>
                                    <td class="py-4 px-6 text-gray-400">${formatDate(referral.created_at)}</td>
                                    <td class="py-4 px-6 text-emerald-400 font-semibold text-right">+${bonus} GALAXY</td>
                                </tr>
                            `;
                            historyBody.append(row);
                        });
                    } else {
                        emptyState.show();
                    }
                } else {
                    totalReferralsEl.text('0');
                    totalEarningsEl.text('0.00');
                    historyBody.html(`<tr><td colspan="3" class="text-center py-10 text-red-400">${response.message || 'Could not load referral data.'}</td></tr>`);
                    if (typeof showToast === 'function') {
                         showToast(response.message || 'Failed to fetch referral stats.', 'error');
                    }
                }
            },
            error: function() {
                // Ensure pulse is removed and error state is shown on network failure
                totalReferralsEl.removeClass('animate-pulse').text('Error');
                totalEarningsEl.removeClass('animate-pulse').text('Error');
                historyBody.html(`<tr><td colspan="3" class="text-center py-10 text-red-400">A network error occurred.</td></tr>`);
                if (typeof showToast === 'function') {
                    showToast('Network error while fetching referral data.', 'error');
                }
            },
            complete: function() {
                loader.hide();
            }
        });
    }

    // Run on page load
    fetchReferralHistory();
});
