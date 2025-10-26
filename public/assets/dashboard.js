// ==============================================================================
// Dashboard Page Logic (public/assets/dashboard.js)
// ==============================================================================
$(document).ready(function() {
    const CURRENT_FILE = 'index.php';
    const showToast = window.showToast; // Access global helper

    // --- State Elements ---
    const recentTxBody = $('#recent-tx-body');
    const recentTxLoader = $('#activity-loader');
    const recentTxEmpty = $('#activity-empty');

    // Helper to format date (simplified)
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        let dateToParse = dateString.replace(' ', 'T'); 
        return new Date(dateToParse).toLocaleDateString(undefined, options);
    }

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
     * Fetches recent transactions for the dashboard.
     * This calls 'get_user_details' which also contains recent_transactions.
     */
    function fetchDashboardActivity() {
        recentTxLoader.removeClass('hidden');
        recentTxEmpty.addClass('hidden');
        recentTxBody.empty();

        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'get_user_details' }, // This action returns 'recent_transactions'
            success: function(response) {
                recentTxLoader.addClass('hidden');

                if (response.success && response.recent_transactions && response.recent_transactions.length > 0) {
                    const transactions = response.recent_transactions;
                    
                    transactions.forEach(function(tx) {
                        const amountStr = String(tx.amount);
                        const amountFloat = parseFloat(amountStr.replace(/,/g, ''));
                        const isPositive = amountFloat >= 0;
                        const amountClass = isPositive ? 'text-emerald-400' : 'text-red-400';
                        const sign = isPositive && tx.type !== 'WITHDRAWAL' ? '+' : '';
                        const formattedAmount = sign + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(Math.abs(amountFloat));

                        const row = `
                            <tr class="animate-fadeIn">
                                <td class="py-3 px-4 text-sm font-semibold">${tx.type || 'N/A'}</td>
                                <td class="py-3 px-4 ${amountClass} font-mono">${formattedAmount}</td>
                                <td class="py-3 px-4">${getStatusBadge(tx.status || 'N/A')}</td>
                                <td class="py-3 px-4 text-gray-400 text-xs">${formatDate(tx.created_at)}</td>
                            </tr>
                        `;
                        recentTxBody.append(row);
                    });
                } else {
                    recentTxEmpty.removeClass('hidden');
                }
            },
            error: function(xhr) {
                recentTxLoader.addClass('hidden');
                recentTxEmpty.removeClass('hidden').html('<p class="text-red-400">Network error loading activity.</p>');
                if (typeof showToast === 'function') {
                    showToast('Network error while loading activity.', 'error');
                }
            }
        });
    }
  
  
  
    // Function to copy the token contract address
function copyTokenAddress() {
    const addressElement = document.getElementById('token-contract-address');
    if (addressElement) {
        const address = addressElement.innerText || addressElement.textContent;
        // Use the globally available copyToClipboard function from main.js
        if (typeof window.copyToClipboard === 'function') {
            window.copyToClipboard(address, 'copy-address-btn-token-text'); // Pass the button text ID
        } else {
            // Basic fallback if copyToClipboard isn't global for some reason
            navigator.clipboard.writeText(address).then(() => {
                const btnText = $('#copy-address-btn-token-text');
                const original = btnText.text();
                btnText.text('Copied!');
                if(window.showToast) window.showToast('✅ Address Copied!', 'success');
                setTimeout(() => btnText.text(original), 2000);
            }).catch(err => {
                 if(window.showToast) window.showToast('❌ Copy Failed', 'error');
                 console.error("Copy failed: ", err);
            });
        }
    }
}
// Make it globally accessible if it's outside document.ready
window.copyTokenAddress = copyTokenAddress;

    // Run on page load
    fetchDashboardActivity();
});