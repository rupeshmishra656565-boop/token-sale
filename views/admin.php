<?php
// Admin Dashboard View
// Requires: $authController, $_SESSION
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo '<div class="container py-20 text-center"><h1 class="text-4xl font-bold text-red-500">ACCESS DENIED</h1><p class="text-xl text-gray-400 mt-4">You do not have administrative privileges to view this page.</p></div>';
    return;
}
?>

<div class="container">
    <!-- Page Header -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-red-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-2">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-yellow-500">Admin Panel</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-400">System overview and token management dashboard.</p>
        </div>
    </div>

    <!-- Overview Stats -->
    <h2 class="text-2xl md:text-3xl font-bold mb-6">System Overview</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-12">
        <div class="glass-card p-4 md:p-6 border-l-4 border-purple-500">
            <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total Users</div>
            <div class="text-3xl md:text-4xl font-bold text-white">
                <span id="stat-total-users" class="animate-pulse">---</span>
            </div>
        </div>
        <div class="glass-card p-4 md:p-6 border-l-4 border-emerald-500">
            <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total Circulated (GALAXY)</div>
            <div class="text-3xl md:text-4xl font-bold text-emerald-400">
                <span id="stat-total-circulated" class="animate-pulse">---</span>
            </div>
        </div>
        <div class="glass-card p-4 md:p-6 border-l-4 border-yellow-500">
            <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Pending Withdrawals (GALAXY)</div>
            <div class="text-3xl md:text-4xl font-bold text-yellow-400">
                <span id="stat-pending-withdrawals" class="animate-pulse">---</span>
            </div>
        </div>
        <div class="glass-card p-4 md:p-6 border-l-4 border-cyan-500">
            <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Est. Total Revenue (USD)</div>
            <div class="text-3xl md:text-4xl font-bold text-cyan-400">
                $<span id="stat-total-revenue" class="animate-pulse">---</span>
            </div>
        </div>
    </div>

    <!-- Pending Withdrawals & Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
        
        <!-- Pending Withdrawals -->
        <div class="lg:col-span-6">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                </svg>
                Pending Withdrawals
            </h2>
            
            <div class="glass-card p-4 md:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-xs sm:text-sm">
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">ID</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">User</th>
                                <th class="text-right py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Amount</th>
                                <th class="text-right py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="pending-withdrawals-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>

                    <div id="pending-loader" class="text-center py-10">
                        <div class="spinner"></div>
                        <p class="mt-2 text-gray-400 text-sm">Fetching pending requests...</p>
                    </div>
                    <div id="pending-empty" class="text-center py-10 hidden">
                        <p class="text-gray-400 text-base">No pending withdrawals.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent System Activity -->
        <div class="lg:col-span-6">
            <h2 class="text-2xl md:text-3xl font-bold mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-cyan-500" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 3v2.09c3.488.484 6.276 3.328 6.57 6.883L21 12l-.43.027c-.294 3.555-3.082 6.399-6.57 6.883V21h-2v-2.09c-3.488-.484-6.276-3.328-6.57-6.883L3 12l.43-.027c.294-3.555 3.082-6.399 6.57-6.883V3h2zm0 2.09V3h-2v2.09c-3.488.484-6.276 3.328-6.57 6.883L3 12l.43-.027c.294-3.555 3.082-6.399 6.57-6.883V21h-2v-2.09c-3.488-.484-6.276-3.328-6.57-6.883L3 12l.43-.027c.294-3.555 3.082-6.399 6.57-6.883V3h2zm0 2.09V3h-2v2.09c-3.488.484-6.276 3.328-6.57 6.883L3 12l.43-.027c.294-3.555 3.082-6.399 6.57-6.883V21h-2v-2.09c-3.488-.484-6.276-3.328-6.57-6.883L3 12l.43-.027c.294-3.555 3.082-6.399 6.57-6.883V3h2z"/>
                </svg>
                Recent Activity
            </h2>
            <div class="glass-card p-4 md:p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-xs sm:text-sm">
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">User</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Type</th>
                                <th class="text-right py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Amount</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody id="activity-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>

                    <div id="activity-loader" class="text-center py-10">
                        <div class="spinner"></div>
                        <p class="mt-2 text-gray-400 text-sm">Fetching system activity...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management Table -->
    <div class="mt-12">
        <h2 class="text-2xl md:text-3xl font-bold mb-6 flex items-center gap-3">
            <svg class="w-6 h-6 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
            User Management
        </h2>
        <div class="glass-card p-4 md:p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-white/10 text-xs sm:text-sm">
                            <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">ID</th>
                            <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Username</th>
                            <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Balance</th>
                            <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Referrals</th>
                            <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Joined</th>
                            <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="user-list-body">
                        <!-- JS will populate -->
                    </tbody>
                </table>
                <div id="user-list-loader" class="text-center py-10">
                    <div class="spinner"></div>
                    <p class="mt-2 text-gray-400 text-sm">Fetching user list...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Balance Adjustment (Hidden by default) -->
<div id="balance-adjust-modal" class="fixed inset-0 bg-black/80 backdrop-blur-xl z-50 flex justify-center items-center hidden">
    <div class="glass-card p-8 w-full max-w-md mx-4 relative overflow-hidden">
        <h3 class="text-3xl font-bold mb-4 text-center">Adjust User Balance</h3>
        <p class="text-gray-400 text-center mb-6">User ID: <span id="adjust-user-id" class="font-bold text-white"></span> (<span id="adjust-username" class="text-purple-400"></span>)</p>
        
        <form id="adjust-balance-form" class="space-y-4">
            <input type="hidden" id="adjustment-user-id" name="user_id">
            
            <div class="input-group">
                <input type="number" id="adjustment-amount" name="amount" placeholder=" " required step="0.01" class="input-field peer">
                <label for="adjustment-amount" class="peer-label">Amount to Add/Deduct (GALAXY)</label>
            </div>
            
            <div class="input-group">
                <input type="text" id="adjustment-details" name="details" placeholder=" " required class="input-field peer">
                <label for="adjustment-details" class="peer-label">Reason for Adjustment</label>
            </div>
            
            <p class="text-sm text-gray-400 text-center">Use a **negative** number to deduct tokens.</p>
            
            <p id="adjust-message" class="text-sm text-center"></p>

            <button type="submit" id="adjust-submit-btn" class="btn-primary w-full py-3 flex justify-center items-center gap-3">
                <span id="adjust-submit-text">Apply Adjustment</span>
                <span id="adjust-submit-spinner" class="spinner hidden"></span>
            </button>
            
            <button type="button" onclick="$('#balance-adjust-modal').addClass('hidden');" class="btn-secondary w-full py-3">
                Cancel
            </button>
        </form>
    </div>
</div>
