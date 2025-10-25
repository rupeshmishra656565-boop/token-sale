<?php
// Premium Profile View - ENHANCED UI
?>

<!-- Password Update Modal -->
<div id="password-modal" class="fixed inset-0 bg-gray-900/80 backdrop-blur-md z-[60] hidden items-center justify-center p-4 transition-opacity duration-300">
    <div class="glass-card p-6 md:p-8 w-full max-w-md mx-auto rounded-xl shadow-2xl shadow-purple-900/30 border border-purple-500/30 relative">
        <!-- Close Button -->
        <button onclick="hidePasswordModal()" class="absolute top-4 right-4 p-1 text-gray-500 hover:text-red-400 transition rounded-full hover:bg-gray-700/50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        
        <h3 class="font-heading text-xl font-semibold text-white mb-6 text-center">Update Password</h3>
        
        <form id="password-form" class="space-y-4">
            <div class="input-group">
                <input type="password" id="current-password" name="current_password" placeholder=" " required class="input-field peer !bg-black/40">
                <label for="current-password" class="peer-label">Current Password</label>
            </div>
            
            <div class="input-group">
                <input type="password" id="new-password" name="new_password" placeholder=" " required minlength="6" class="input-field peer !bg-black/40">
                <label for="new-password" class="peer-label">New Password (min 6 chars)</label>
            </div>
            
            <div class="input-group">
                <input type="password" id="confirm-password" name="confirm_password" placeholder=" " required class="input-field peer !bg-black/40">
                <label for="confirm-password" class="peer-label">Confirm New Password</label>
            </div>

            <p id="password-message" class="text-sm text-center min-h-[1.25rem]"></p>

             <div class="flex gap-4 pt-3">
                 <button type="button" onclick="hidePasswordModal()" class="w-full bg-gray-600/50 hover:bg-gray-500/50 text-gray-300 font-semibold py-2.5 px-4 rounded-md transition duration-200">
                    Cancel
                 </button>
                <button type="submit" id="password-submit-btn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-md transition duration-200 flex items-center justify-center shadow-lg shadow-purple-500/30 hover:shadow-purple-500/40">
                    <span id="password-submit-text">Update Password</span>
                    <span id="password-submit-spinner" class="spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden animate-fadeIn" style="animation-delay: 100ms;">
        <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl opacity-60"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold font-heading mb-2">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">My Profile</span>
            </h1>
            <p class="text-base md:text-lg text-gray-400">Manage your account and view lifetime statistics</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
        <!-- Left Column - Account Details -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Profile Card -->
            <div class="glass-card p-6 md:p-8 relative overflow-hidden animate-fadeIn" style="animation-delay: 200ms;">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/30 rounded-full blur-2xl opacity-50"></div>
                
                <div class="relative z-10">
                    <!-- Avatar -->
                    <div class="flex justify-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-purple-500 via-cyan-500 to-purple-500 p-1 shadow-lg shadow-purple-500/30">
                            <div class="w-full h-full rounded-full bg-gray-900 flex items-center justify-center ring-2 ring-inset ring-purple-500/50">
                                <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-bold font-heading text-center mb-6 text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">
                        Account Details
                    </h2>
                    
                    <div class="space-y-4 text-sm">
                        <div class="bg-black/30 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">User ID</p>
                            <p id="profile-user-id" class="text-base font-bold font-mono animate-pulse">---</p>
                        </div>
                        
                        <div class="bg-black/30 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Username</p>
                            <p id="profile-username" class="text-base font-bold animate-pulse">---</p>
                        </div>
                        
                        <div class="bg-black/30 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Email</p>
                            <p id="profile-email" class="text-base font-semibold break-all animate-pulse">---</p>
                        </div>
                        
                        <div class="bg-black/30 rounded-xl p-4 border border-white/10">
                            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">Member Since</p>
                            <p id="profile-member-since" class="text-base font-semibold animate-pulse">---</p>
                        </div>
                    </div>

                    <button onclick="showPasswordModal()" class="btn-secondary w-full py-3 mt-6 text-sm group">
                        <svg class="w-4 h-4 transition-transform group-hover:rotate-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H5v-2H3v-2H1v-4a8 8 0 0115.048-5.128 2 2 0 113.904.744A9.957 9.957 0 0021 9a2 2 0 11-4 0z"></path></svg>
                        Change Password
                    </button>
                </div>
            </div>
            
            <!-- Referral Info Card -->
            <div class="glass-card p-6 border-2 border-yellow-500/30 relative overflow-hidden animate-fadeIn" style="animation-delay: 300ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/20 rounded-full blur-2xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center shadow-lg shadow-yellow-500/30">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold font-heading text-yellow-400">Referred By</h3>
                    </div>
                    <p id="profile-referrer-id" class="text-base font-semibold animate-pulse">---</p>
                    <a href="index.php?p=referrals" class="text-sm text-gray-400 hover:text-yellow-400 transition-colors mt-3 inline-flex items-center gap-1 group">
                        Share your link to earn!
                        <svg class="w-3 h-3 transition-transform group-hover:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column - Lifetime Stats & Activity -->
        <div class="lg:col-span-8 space-y-8 md:space-y-10">
            <!-- Lifetime Metrics -->
            <div class="animate-fadeIn" style="animation-delay: 400ms;">
                <h2 class="text-3xl font-bold font-heading mb-6">Lifetime Metrics</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
                    <!-- Stat 1: Tokens Acquired -->
                    <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-emerald-500/50">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Net Tokens Acquired</span>
                            <div class="p-2 bg-emerald-500/20 rounded-xl text-emerald-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-emerald-400 mt-3 font-heading"><span id="stat-tokens-acquired" class="animate-pulse">---</span></div>
                    </div>
                    
                    <!-- Stat 2: Referral Earnings -->
                     <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-yellow-500/50">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Referral Earnings</span>
                            <div class="p-2 bg-yellow-500/20 rounded-xl text-yellow-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-yellow-400 mt-3 font-heading"><span id="stat-referral-earnings" class="animate-pulse">---</span></div>
                    </div>

                    <!-- Stat 3: Total Purchases -->
                    <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-purple-500/50">
                         <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Purchases Made</span>
                            <div class="p-2 bg-purple-500/20 rounded-xl text-purple-400">
                               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-purple-400 mt-3 font-heading"><span id="stat-total-purchases" class="animate-pulse">---</span></div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Table -->
            <div class="glass-card p-6 md:p-8 animate-fadeIn" style="animation-delay: 500ms;">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold font-heading mb-1">Recent Activity</h2>
                        <p class="text-gray-400 text-sm">Last 5 wallet transactions</p>
                    </div>
                     <a href="index.php?p=wallet" class="btn-secondary py-2 px-4 text-sm group">
                        <span>Full History</span>
                        <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                
                <div class="min-h-[200px] relative">
                    <!-- Loader -->
                    <div id="recent-activity-loader" class="absolute inset-0 flex items-center justify-center bg-[var(--bg-card)] z-10">
                        <div class="spinner !w-8 !h-8 !border-4"></div>
                    </div>
                    <!-- Empty State -->
                    <div id="recent-activity-empty" class="text-center py-16 px-4 text-gray-500 text-sm hidden">
                         <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        No recent transactions.
                    </div>
                     <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-[var(--border-color)] text-xs sm:text-sm">
                                    <th class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Type</th>
                                    <th class="text-right py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Amount</th>
                                    <th class="text-center py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Status</th>
                                    <th class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Date</th>
                                </tr>
                            </thead>
                            <tbody id="recent-tx-body" class="divide-y divide-[var(--border-color)]">
                                <!-- JS will populate -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Status Badges */
    .status-badge { display: inline-flex; align-items: center; padding: 0.2rem 0.6rem; border-radius: 99px; font-size: 0.7rem; font-weight: 600; line-height: 1; text-transform: uppercase; letter-spacing: 0.05em; }
    .status-complete { background-color: rgba(16, 185, 129, 0.2); color: #10b981; }
    .status-processing, .status-pending { background-color: rgba(245, 158, 11, 0.2); color: #f59e0b; }
    .status-failed { background-color: rgba(239, 68, 68, 0.2); color: #ef4444; }
    .status-unknown { background-color: rgba(107, 114, 128, 0.2); color: #9ca3af; }
    
    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
</style>
