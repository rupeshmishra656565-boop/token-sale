// ==============================================================================
// Admin Panel Logic (admin/assets/admin.js) - Enhanced Version
// ==============================================================================

$(document).ready(function() {
    // Assumes JS is called from admin/index.php
    const CURRENT_FILE = 'index.php';
    // Access global helper defined in admin/index.php layout
    const showToast = window.showToast;

    // --- State Elements ---
    const STATS = {
        totalUsers: $('#stat-total-users'),
        totalCirculated: $('#stat-total-circulated'),
        pendingWithdrawals: $('#stat-pending-withdrawals'),
        totalRevenue: $('#stat-total-revenue')
    };
    const PENDING = {
        container: $('#pending-withdrawals-section'), // Target container for showing/hiding
        body: $('#pending-withdrawals-body'),
        loader: $('#pending-loader'),
        empty: $('#pending-empty')
    };
    const ACTIVITY = {
        body: $('#activity-body'),
        loader: $('#activity-loader'),
        empty: $('#activity-empty') // Added empty state for activity
    };
    const USERS = {
        body: $('#user-list-body'),
        loader: $('#user-list-loader'),
        empty: $('#user-list-empty') // Added empty state for users
    };
    const MODAL = {
        el: $('#balance-adjust-modal'),
        userId: $('#adjust-user-id'),
        username: $('#adjust-username'),
        formUserId: $('#adjustment-user-id'),
        submitBtn: $('#adjust-submit-btn'),
        submitText: $('#adjust-submit-text'),
        submitSpinner: $('#adjust-submit-spinner'),
        message: $('#adjust-message'),
        defaultText: 'Apply Adjustment'
    };

    // --- Helper Functions ---

    // More robust status badge, handles null/empty status
    function getStatusBadge(status) {
        let badgeClass = '';
        let statusText = status || 'Unknown'; // Default text if null/empty
        statusText = statusText.charAt(0).toUpperCase() + statusText.slice(1); // Capitalize

        switch (status ? status.toLowerCase() : '') {
            case 'complete': badgeClass = 'status-complete'; break;
            case 'processing': badgeClass = 'status-processing'; break;
            case 'failed': badgeClass = 'status-failed'; break;
            case 'pending': badgeClass = 'status-pending'; break;
            default: badgeClass = 'status-unknown'; statusText = 'Unknown'; break; // Handle unexpected statuses
        }
        // Removed dot span for cleaner look
        return `<span class="status-badge ${badgeClass} text-xs px-2.5 py-0.5 rounded-full">${statusText}</span>`;
    }

    // Improved date formatting
    function formatDate(dateString, includeTime = false) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString.replace(' ', 'T')); // Handle potential space separator
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            if (includeTime) {
                options.hour = '2-digit';
                options.minute = '2-digit';
                // options.timeZoneName = 'short'; // Optional: Add timezone
            }
            return date.toLocaleString(undefined, options);
        } catch (e) {
            console.error("Error formatting date:", dateString, e);
            return 'Invalid Date';
        }
    }


    // --- Fetchers ---

    function fetchAdminOverview() {
        Object.values(STATS).forEach(el => el.text('...').addClass('animate-pulse'));
        ACTIVITY.loader.removeClass('hidden'); // Use removeClass/addClass
        ACTIVITY.empty.addClass('hidden');
        ACTIVITY.body.empty();

        $.ajax({
            url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'getAdminOverview' },
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    STATS.totalUsers.text(data.total_users.toLocaleString());
                    STATS.totalCirculated.text(data.total_tokens_circulated);
                    STATS.pendingWithdrawals.text(data.pending_withdrawal);
                    STATS.totalRevenue.text(data.total_revenue_usd);

                    ACTIVITY.body.empty();
                    if (data.recent_activity && data.recent_activity.length > 0) {
                        data.recent_activity.forEach(tx => {
                            const amount = parseFloat(tx.amount);
                            const isPositive = amount >= 0;
                            // More specific coloring based on type
                            let amountClass = 'text-gray-400';
                            if (['PURCHASE', 'SIGNUP', 'REFERRAL', 'ADJUST_IN'].includes(tx.type)) amountClass = 'text-emerald-400';
                            else if (['WITHDRAWAL', 'ADJUST_OUT'].includes(tx.type)) amountClass = 'text-red-400';

                            const sign = (isPositive && tx.type !== 'WITHDRAWAL' && tx.type !== 'ADJUST_OUT') ? '+' : '';
                            const formattedAmount = sign + Number(Math.abs(amount)).toLocaleString('en-US', { minimumFractionDigits: 2 });

                            const row = `
                                <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-colors duration-150">
                                    <td class="py-3 px-4 text-sm font-medium text-gray-200">${tx.username || 'N/A'}</td>
                                    <td class="py-3 px-4 text-xs uppercase tracking-wider text-gray-400">${tx.type || 'N/A'}</td>
                                    <td class="py-3 px-4 text-sm text-right ${amountClass} font-mono">${formattedAmount}</td>
                                    <td class="py-3 px-4 text-center">${getStatusBadge(tx.status)}</td>
                                    <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">${formatDate(tx.created_at, true)}</td>
                                </tr>`;
                            ACTIVITY.body.append(row);
                        });
                         ACTIVITY.empty.addClass('hidden');
                    } else {
                         ACTIVITY.empty.removeClass('hidden'); // Show empty state
                    }
                } else {
                    Object.values(STATS).forEach(el => el.text('N/A'));
                    ACTIVITY.body.empty();
                    ACTIVITY.empty.removeClass('hidden').text('Failed to load activity.');
                    if (typeof showToast === 'function') showToast(response.message || 'Failed to load admin overview.', 'error');
                }
            },
            error: function() {
                Object.values(STATS).forEach(el => el.text('Error'));
                ACTIVITY.body.empty();
                ACTIVITY.empty.removeClass('hidden').text('Network error loading activity.');
                if (typeof showToast === 'function') showToast('Network Error loading overview.', 'error');
            },
            complete: function() {
                Object.values(STATS).forEach(el => el.removeClass('animate-pulse'));
                ACTIVITY.loader.addClass('hidden');
            }
        });
    }

    function fetchPendingWithdrawals() {
        PENDING.loader.removeClass('hidden');
        PENDING.empty.addClass('hidden');
        PENDING.body.empty().closest('table').addClass('hidden'); // Hide table initially

        $.ajax({
            url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'getPendingWithdrawals' },
            success: function(response) {
                if (response.success && response.withdrawals && response.withdrawals.length > 0) {
                    PENDING.body.empty(); // Clear just in case
                    response.withdrawals.forEach(tx => {
                        const row = `
                            <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="py-3 px-4 text-sm font-medium text-gray-200">${tx.username || 'N/A'}</td>
                                <td class="py-3 px-4 text-sm text-right text-yellow-400 font-mono">${tx.amount}</td>
                                <td class="py-3 px-4 text-xs text-gray-400 break-all">${tx.details || 'N/A'}</td>
                                <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">${tx.created_at || 'N/A'}</td>
                                <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                    <button class="btn-action text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-md transition duration-150" data-id="${tx.id}" data-action="Complete">Approve</button>
                                    <button class="btn-action text-xs font-semibold bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition duration-150" data-id="${tx.id}" data-action="Failed">Reject</button>
                                </td>
                            </tr>`;
                        PENDING.body.append(row);
                    });
                    PENDING.empty.addClass('hidden');
                    PENDING.container.removeClass('hidden'); // Show section
                     PENDING.body.closest('table').removeClass('hidden'); // Show table
                } else {
                    PENDING.empty.removeClass('hidden'); // Show empty message
                    PENDING.container.removeClass('hidden'); // Ensure section is visible even if empty
                    PENDING.body.closest('table').addClass('hidden'); // Hide table
                    if(!response.success && response.message) { // Show error if backend failed
                         PENDING.empty.text('Error: ' + response.message).addClass('text-red-400');
                    }
                }
            },
            error: function() {
                 PENDING.empty.removeClass('hidden').text('Network error loading withdrawals.').addClass('text-red-400');
                 PENDING.container.removeClass('hidden');
                 PENDING.body.closest('table').addClass('hidden');
            },
            complete: function() {
                 PENDING.loader.addClass('hidden');
            }
        });
    }

    function fetchUserList() {
        USERS.loader.removeClass('hidden');
        USERS.empty.addClass('hidden');
        USERS.body.empty().closest('table').addClass('hidden');

        $.ajax({
            url: CURRENT_FILE, type: 'POST', dataType: 'json', data: { action: 'getAllUsers' },
            success: function(response) {
                if (response.success && response.users && response.users.length > 0) {
                    USERS.body.empty();
                    response.users.forEach(user => {
                        const adminBadge = user.is_admin ? '<span class="ml-2 status-badge status-complete text-xs px-2 py-0.5 rounded-full">Admin</span>' : '';
                        const row = `
                            <tr class="border-b border-gray-700/50 hover:bg-gray-700/30 transition-colors duration-150">
                                <td class="py-3 px-4 text-sm font-mono text-gray-400">${user.id}</td>
                                <td class="py-3 px-4 text-sm font-medium text-gray-200 flex items-center">${user.username}${adminBadge}</td>
                                <td class="py-3 px-4 text-sm text-gray-300 break-all">${user.email}</td>
                                <td class="py-3 px-4 text-sm text-right font-mono text-emerald-400">${user.tokens}</td>
                                <td class="py-3 px-4 text-sm text-center text-gray-300">${user.referrals}</td>
                                <td class="py-3 px-4 text-xs text-gray-500 whitespace-nowrap">${user.created_at}</td>
                                <td class="py-3 px-4 text-center">
                                    <button class="btn-adjust-balance text-xs font-semibold bg-purple-600 hover:bg-purple-700 text-white px-3 py-1.5 rounded-md transition duration-150"
                                            data-id="${user.id}" data-username="${user.username}">Adjust</button>
                                </td>
                            </tr>`;
                        USERS.body.append(row);
                    });
                    USERS.empty.addClass('hidden');
                     USERS.body.closest('table').removeClass('hidden');
                } else {
                    USERS.empty.removeClass('hidden');
                    USERS.body.closest('table').addClass('hidden');
                     if(!response.success && response.message) {
                         USERS.empty.text('Error: ' + response.message).addClass('text-red-400');
                    }
                }
            },
            error: function() {
                 USERS.empty.removeClass('hidden').text('Network error loading users.').addClass('text-red-400');
                 USERS.body.closest('table').addClass('hidden');
            },
            complete: function() {
                 USERS.loader.addClass('hidden');
            }
        });
    }

    // --- Action Handlers ---

    // Process/Reject Withdrawal
    $(document).on('click', '.btn-action', function() {
        const button = $(this);
        const txId = button.data('id');
        const status = button.data('action'); // 'Complete' or 'Failed'
        const originalText = button.text();

        // Confirmation dialog
        const actionVerb = status === 'Complete' ? 'approve' : 'reject (and refund)';
        if (!confirm(`Are you sure you want to ${actionVerb} withdrawal ID ${txId}?`)) {
            return;
        }

        button.prop('disabled', true).text('Processing...');
        // Disable the other button in the same row
        button.siblings('.btn-action').prop('disabled', true);

        $.ajax({
            url: CURRENT_FILE, type: 'POST', dataType: 'json',
            data: { action: 'processWithdrawal', tx_id: txId, status: status },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    fetchAdminOverview(); // Refresh stats
                    fetchPendingWithdrawals(); // Refresh the list
                } else {
                    showToast(response.message || `Failed to ${actionVerb}.`, 'error');
                    button.prop('disabled', false).text(originalText);
                    button.siblings('.btn-action').prop('disabled', false);
                }
            },
            error: function() {
                showToast(`Action failed due to server error.`, 'error');
                button.prop('disabled', false).text(originalText);
                button.siblings('.btn-action').prop('disabled', false);
            }
        });
    });

    // Open Balance Adjustment Modal
    $(document).on('click', '.btn-adjust-balance', function() {
        const userId = $(this).data('id');
        const username = $(this).data('username');

        MODAL.userId.text(userId);
        MODAL.username.text(username);
        MODAL.formUserId.val(userId);
        MODAL.message.text('').removeClass('text-emerald-400 text-red-400');
        $('#adjustment-amount').val('');
        $('#adjustment-details').val('');
        // Reset button state
        MODAL.submitBtn.prop('disabled', false);
        MODAL.submitText.removeClass('hidden').text(MODAL.defaultText);
        MODAL.submitSpinner.addClass('hidden');

        MODAL.el.removeClass('hidden flex').addClass('flex'); // Use flex to show
    });

    // Close Balance Adjustment Modal
    $('.close-modal-btn').on('click', function() {
        MODAL.el.removeClass('flex').addClass('hidden');
    });

    // Submit Balance Adjustment
    $('#adjust-balance-form').on('submit', function(e) {
        e.preventDefault();

        MODAL.submitBtn.prop('disabled', true);
        MODAL.submitText.addClass('hidden');
        MODAL.submitSpinner.removeClass('hidden');
        MODAL.message.text('').removeClass('text-emerald-400 text-red-400');

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'adjustUserBalance' });

        $.post(CURRENT_FILE, formData, function(response) {
            // Re-enable button regardless of outcome
            MODAL.submitBtn.prop('disabled', false);
            MODAL.submitText.removeClass('hidden').text(MODAL.defaultText);
            MODAL.submitSpinner.addClass('hidden');

            if (response.success) {
                MODAL.message.text(response.message).addClass('text-emerald-400');
                showToast(response.message, 'success');
                fetchAdminOverview(); // Refresh stats
                fetchUserList(); // Refresh user balances in the list
                // Close modal after successful adjustment
                setTimeout(() => MODAL.el.removeClass('flex').addClass('hidden'), 1500);
            } else {
                MODAL.message.text(response.message || 'Adjustment failed.').addClass('text-red-400');
                showToast(response.message || 'Adjustment failed.', 'error');
            }
        }).fail(function() {
            MODAL.submitBtn.prop('disabled', false);
            MODAL.submitText.removeClass('hidden').text(MODAL.defaultText);
            MODAL.submitSpinner.addClass('hidden');
            MODAL.message.text('Network connection error.').addClass('text-red-400');
            showToast('Network error during adjustment.', 'error');
        });
    });


    // --- Initialization ---
    // Fetch data on page load
    fetchAdminOverview();
    fetchPendingWithdrawals();
    fetchUserList();

    // Optional: Add auto-refresh buttons or intervals if desired
    $('#refresh-overview-btn').on('click', fetchAdminOverview);
    $('#refresh-pending-btn').on('click', fetchPendingWithdrawals);
    $('#refresh-users-btn').on('click', fetchUserList);

}); // End document ready