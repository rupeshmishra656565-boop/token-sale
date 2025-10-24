<?php
// Premium Profile View
?>

<!-- Password Update Modal -->
<div id="password-modal" class="fixed inset-0 bg-black/80 backdrop-blur-xl z-50 flex justify-center items-center hidden">
    <div class="glass-card p-6 md:p-8 w-full max-w-md mx-4 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-purple-500 via-cyan-500 to-purple-500"></div>
        
        <h3 class="text-3xl font-bold mb-6 text-center">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Update Password</span>
        </h3>
        
        <form id="password-form" class="space-y-4">
            <div class="input-group">
                <input type="password" id="current-password" name="current_password" placeholder=" " required class="input-field peer">
                <label for="current-password" class="peer-label">Current Password</label>
            </div>
            
            <div class="input-group">
                <input type="password" id="new-password" name="new_password" placeholder=" " required class="input-field peer">
                <label for="new-password" class="peer-label">New Password (min 6 chars)</label>
            </div>
            
            <div class="input-group">
                <input type="password" id="confirm-password" name="confirm_password" placeholder=" " required class="input-field peer">
                <label for="confirm-password" class="peer-label">Confirm New Password</label>
            </div>

            <p id="password-message" class="text-sm text-center"></p>

            <button type="submit" id="password-submit-btn" class="btn-primary w-full py-3 flex justify-center items-center gap-3 text-base">
                <span id="password-submit-text">Update Password</span>
                <span id="password-submit-spinner" class="spinner hidden"></span>
            </button>
            
            <button type="button" onclick="hidePasswordModal()" class="btn-secondary w-full py-3 text-base mt-2">
                Cancel
            </button>
        </form>
    </div>
</div>

<div class="container">
    <!-- Page Header -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">My Profile</span>
            </h1>
            <p class="text-base md:text-xl text-gray-400">Manage your account and view lifetime statistics</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
        <!-- Left Column - Account Details -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Profile Card -->
            <div class="glass-card p-6 md:p-8 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/30 rounded-full blur-2xl"></div>
                
                <div class="relative z-10">
                    <!-- Avatar -->
                    <div class="flex justify-center mb-6">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 via-cyan-500 to-purple-500 p-1">
                            <div class="w-full h-full rounded-full bg-gray-900 flex items-center justify-center">
                                <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-center mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">
                        Account Details
                    </h2>
                    
                    <div class="space-y-4 text-sm">
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">User ID</p>
                            <p id="profile-user-id" class="text-base font-bold font-mono animate-pulse">---</p>
                        </div>
                        
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Username</p>
                            <p id="profile-username" class="text-base font-bold animate-pulse">---</p>
                        </div>
                        
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Email</p>
                            <p id="profile-email" class="text-base font-semibold break-all animate-pulse">---</p>
                        </div>
                        
                        <div class="bg-white/5 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Member Since</p>
                            <p id="profile-member-since" class="text-base font-semibold animate-pulse">---</p>
                        </div>
                    </div>

                    <button onclick="showPasswordModal()" class="btn-secondary w-full py-3 mt-6 text-sm">
                        🔒 Change Password
                    </button>
                </div>
            </div>
            
            <!-- Referral Info Card -->
            <div class="glass-card p-6 border-2 border-yellow-500/30 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/20 rounded-full blur-2xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-yellow-400">Referred By</h3>
                    </div>
                    <p id="profile-referrer-id" class="text-base font-semibold animate-pulse">---</p>
                    <p class="text-sm text-gray-400 mt-3">Share your link to earn rewards!</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Lifetime Stats & Activity -->
        <div class="lg:col-span-8 space-y-8 md:space-y-10">
            <!-- Lifetime Metrics -->
            <div>
                <h2 class="text-3xl font-bold mb-6">Lifetime Metrics</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                    <!-- Stat 1: Tokens Acquired -->
                    <div class="glass-card p-4 md:p-6 border-l-4 border-emerald-500">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <span class="text-xs text-emerald-400 uppercase tracking-wider font-semibold">Tokens Acquired</span>
                        </div>
                        <div class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-500 mb-1">
                            <span id="stat-tokens-acquired" class="animate-pulse">---</span>
                        </div>
                        <div class="text-xs text-gray-400">Net GALAXY Tokens</div>
                    </div>
                    
                    <!-- Stat 2: Referral Earnings -->
                    <div class="glass-card p-4 md:p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-yellow-400 uppercase tracking-wider font-semibold">Referral Earnings</span>
                        </div>
                        <div class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-1">
                            <span id="stat-referral-earnings" class="animate-pulse">---</span>
                        </div>
                        <div class="text-xs text-gray-400">Total from Referrals</div>
                    </div>

                    <!-- Stat 3: Total Purchases -->
                    <div class="glass-card p-4 md:p-6 border-l-4 border-purple-500">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs text-purple-400 uppercase tracking-wider font-semibold">Purchases</span>
                        </div>
                        <div class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-purple-600 mb-1">
                            <span id="stat-total-purchases" class="animate-pulse">---</span>
                        </div>
                        <div class="text-xs text-gray-400">Total Transactions</div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Table -->
            <div class="glass-card p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold mb-1">Recent Activity</h2>
                        <p class="text-gray-400 text-sm">Last 5 wallet transactions</p>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-xs sm:text-sm">
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Type</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Amount</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Status</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody id="recent-tx-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                    
                    <div id="recent-activity-loader" class="text-center py-10">
                        <div class="inline-block w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mb-3"></div>
                        <p class="mt-2 text-gray-400 text-sm">Loading recent transactions...</p>
                    </div>
                    <div id="recent-activity-empty" class="text-center py-10 hidden">
                        <p class="text-gray-400 text-base">No recent activity recorded.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group {
        position: relative;
        margin-bottom: 1.5rem;
    }

    .peer-label {
        position: absolute;
        left: 1.5rem;
        top: 1.25rem;
        color: rgba(255, 255, 255, 0.5);
        transition: all 0.3s ease;
        pointer-events: none;
        background: transparent;
        padding: 0 0.5rem;
    }

    .input-field:focus ~ .peer-label,
    .input-field:not(:placeholder-shown) ~ .peer-label {
        transform: translateY(-2.5rem) scale(0.85);
        color: var(--primary);
        background: var(--dark-bg);
    }
</style>