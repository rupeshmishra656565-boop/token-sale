// ==============================================================================
// Admin Panel Logic (public/assets/admin.js)
// ==============================================================================

$(document).ready(function() {
    const CURRENT_FILE = 'index.php';
    const showToast = window.showToast; // Global helper

    // --- State Elements ---
    const STATS = {
        totalUsers: $('#stat-total-users'),
        totalCirculated: $('#stat-total-circulated'),
        pendingWithdrawals: $('#stat-pending-withdrawals'),
        totalRevenue: $('#stat-total-revenue')
    };

    const PENDING = {
        body: $('#pending-withdrawals-body'),
        loader: $('#pending-loader'),
        empty: $('#pending-empty')
    };
    
    const ACTIVITY = {
        body: $('#activity-body'),
        loader: $('#activity-loader')
    };

    const USERS = {
        body: $('#user-list-body'),
        loader: $('#user-list-loader')
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

    function getStatusBadge(status) {
        let badgeClass = '';
        if (status === 'Complete') badgeClass = 'status-complete';
        else if (status === 'Processing') badgeClass = 'status-processing';
        else if (status === 'Failed') badgeClass = 'status-failed';
        else badgeClass = 'status-pending';

        return `<span class="status-badge ${badgeClass} text-xs">${status}</span>`;
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString.replace(' ', 'T')).toLocaleDateString(undefined, options);
    }
    
    function formatTime(dateString) {
        if (!dateString) return 'N/A';
        const options = { hour: '2-digit', minute: '2-digit' };
        return new Date(dateString.replace(' ', 'T')).toLocaleTimeString(undefined, options);
    }


    // --- Fetchers ---

    function fetchAdminOverview() {
        // Set all stats to pulse state
        Object.values(STATS).forEach(el => el.text('---').addClass('animate-pulse'));
        ACTIVITY.loader.show();
        ACTIVITY.body.empty();
        
        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'getAdminOverview' },
            success: function(response) {
                if (response.success && response.data) {
                    const data = response.data;
                    
                    // Populate main stats cards
                    STATS.totalUsers.text(data.total_users.toLocaleString());
                    STATS.totalCirculated.text(data.total_tokens_circulated);
                    STATS.pendingWithdrawals.text(data.pending_withdrawal);
                    STATS.totalRevenue.text(data.total_revenue_usd);
                    
                    // Populate recent activity
                    ACTIVITY.body.empty();
                    data.recent_activity.forEach(tx => {
                        const amount = parseFloat(tx.amount);
                        const isPositive = amount >= 0;
                        const amountClass = isPositive && tx.type !== 'WITHDRAWAL' ? 'text-emerald-400' : 'text-red-400';
                        const sign = isPositive && tx.type !== 'WITHDRAWAL' ? '+' : '';
                        const formattedAmount = sign + Number(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
                        
                        const row = `
                            <tr class="border-b border-white/5 text-xs sm:text-sm hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4 font-semibold">${tx.username}</td>
                                <td class="py-3 px-4">${tx.type}</td>
                                <td class="py-3 px-4 text-right ${amountClass} font-mono">${formattedAmount}</td>
                                <td class="py-3 px-4">${getStatusBadge(tx.status)}</td>
                            </tr>
                        `;
                        ACTIVITY.body.append(row);
                    });

                } else {
                    Object.values(STATS).forEach(el => el.text('N/A'));
                    ACTIVITY.body.html('<tr><td colspan="4" class="text-center py-4 text-red-400">Failed to load data.</td></tr>');
                    if (typeof showToast === 'function') showToast(response.message || 'Admin data failed to load.', 'error');
                }
            },
            error: function() {
                Object.values(STATS).forEach(el => el.text('Error'));
                ACTIVITY.body.html('<tr><td colspan="4" class="text-center py-4 text-red-400">Network Error.</td></tr>');
            },
            complete: function() {
                Object.values(STATS).forEach(el => el.removeClass('animate-pulse'));
                ACTIVITY.loader.hide();
            }
        });
    }

    function fetchPendingWithdrawals() {
        PENDING.loader.show();
        PENDING.empty.hide();
        PENDING.body.empty();

        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'getPendingWithdrawals' },
            success: function(response) {
                PENDING.loader.hide();
                
                if (response.success && response.withdrawals.length > 0) {
                    PENDING.empty.hide();
                    response.withdrawals.forEach(tx => {
                        const row = `
                            <tr class="border-b border-white/5 text-xs sm:text-sm hover:bg-white/5 transition-colors">
                                <td class="py-3 px-4 font-mono">${tx.id}</td>
                                <td class="py-3 px-4 font-semibold">${tx.username}</td>
                                <td class="py-3 px-4 text-right text-yellow-400 font-mono">${tx.amount}</td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button class="btn-action bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-md text-xs transition" data-id="${tx.id}" data-action="Complete">Process</button>
                                    <button class="btn-action bg-red-600 hover:bg-red-700 text-white p-2 rounded-md text-xs transition" data-id="${tx.id}" data-action="Failed">Refund</button>
                                </td>
                            </tr>
                            <tr class="text-xs">
                                <td colspan="4" class="py-1 px-4 text-gray-500 border-b border-white/10">
                                    Destination: <span class="font-mono">${tx.details}</span> (Requested: ${tx.created_at})
                                </td>
                            </tr>
                        `;
                        PENDING.body.append(row);
                    });
                } else {
                    PENDING.empty.show();
                }
            },
            error: function() {
                 PENDING.loader.hide();
                 PENDING.body.html('<tr><td colspan="4" class="text-center py-4 text-red-400">Network Error.</td></tr>');
            }
        });
    }
    
    function fetchUserList() {
        USERS.loader.show();
        USERS.body.empty();
        
        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { action: 'getAllUsers' },
            success: function(response) {
                USERS.loader.hide();
                
                if (response.success && response.users.length > 0) {
                    response.users.forEach(user => {
                        const adminBadge = user.is_admin ? '<span class="status-badge status-complete ml-2 text-xs">ADMIN</span>' : '';
                        const row = `
                            <tr class="border-b border-white/5 text-xs sm:text-sm hover:bg-white/5 transition-colors">
                                <td class="py-3 px-2 sm:px-4 font-mono">${user.id}</td>
                                <td class="py-3 px-2 sm:px-4 font-semibold flex items-center">${user.username} ${adminBadge}</td>
                                <td class="py-3 px-2 sm:px-4 text-emerald-400 font-mono">${user.tokens}</td>
                                <td class="py-3 px-2 sm:px-4">${user.referrals}</td>
                                <td class="py-3 px-2 sm:px-4 text-gray-400">${user.created_at}</td>
                                <td class="py-3 px-2 sm:px-4">
                                    <button class="btn-adjust-balance bg-purple-600 hover:bg-purple-700 text-white p-2 rounded-md text-xs transition" 
                                            data-id="${user.id}" data-username="${user.username}">Adjust</button>
                                </td>
                            </tr>
                        `;
                        USERS.body.append(row);
                    });
                } else {
                    USERS.body.html('<tr><td colspan="6" class="text-center py-4 text-gray-400">No users found.</td></tr>');
                }
            },
            error: function() {
                 USERS.loader.hide();
                 USERS.body.html('<tr><td colspan="6" class="text-center py-4 text-red-400">Network Error.</td></tr>');
            }
        });
    }

    // --- Action Handlers ---
    
    // 1. Process/Refund Withdrawal
    $(document).on('click', '.btn-action', function() {
        const button = $(this);
        const txId = button.data('id');
        const status = button.data('action');
        
        button.prop('disabled', true).text('...');
        
        $.ajax({
            url: CURRENT_FILE,
            type: 'POST',
            dataType: 'json',
            data: { 
                action: 'processWithdrawal', 
                tx_id: txId, 
                status: status 
            },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    // Refresh both sections
                    fetchAdminOverview();
                    fetchPendingWithdrawals();
                } else {
                    showToast(response.message, 'error');
                    button.prop('disabled', false).text(status === 'Complete' ? 'Process' : 'Refund');
                }
            },
            error: function() {
                showToast('Action failed due to server error.', 'error');
                button.prop('disabled', false).text(status === 'Complete' ? 'Process' : 'Refund');
            }
        });
    });

    // 2. Open Balance Adjustment Modal
    $(document).on('click', '.btn-adjust-balance', function() {
        const userId = $(this).data('id');
        const username = $(this).data('username');
        
        MODAL.userId.text(userId);
        MODAL.username.text(username);
        MODAL.formUserId.val(userId);
        MODAL.message.text('').removeClass('text-emerald-400 text-red-400');
        $('#adjustment-amount').val('');
        $('#adjustment-details').val('');

        MODAL.el.removeClass('hidden');
    });
    
    // 3. Submit Balance Adjustment
    $('#adjust-balance-form').on('submit', function(e) {
        e.preventDefault();
        
        MODAL.submitBtn.prop('disabled', true);
        MODAL.submitText.addClass('hidden');
        MODAL.submitSpinner.removeClass('hidden');
        MODAL.message.text('').removeClass('text-emerald-400 text-red-400');

        const formData = $(this).serializeArray();
        formData.push({ name: 'action', value: 'adjustUserBalance' });

        $.post(CURRENT_FILE, formData, function(response) {
            
            MODAL.submitText.removeClass('hidden').text(MODAL.defaultText);
            MODAL.submitSpinner.addClass('hidden');
            MODAL.submitBtn.prop('disabled', false);

            if (response.success) {
                MODAL.message.text(response.message).addClass('text-emerald-400');
                showToast(response.message, 'success');
                fetchAdminOverview(); // Refresh stats
                fetchUserList(); // Refresh user balances
                // Close modal after successful adjustment
                setTimeout(() => MODAL.el.addClass('hidden'), 1500); 
            } else {
                MODAL.message.text(response.message).addClass('text-red-400');
                showToast(response.message, 'error');
            }
        }).fail(function() {
            MODAL.submitText.removeClass('hidden').text(MODAL.defaultText);
            MODAL.submitSpinner.addClass('hidden');
            MODAL.submitBtn.prop('disabled', false);
            MODAL.message.text('Network connection error.').addClass('text-red-400');
        });
    });


    // --- Initialization ---
    fetchAdminOverview();
    fetchPendingWithdrawals();
    fetchUserList();
});
