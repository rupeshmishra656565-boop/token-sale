<?php
// Admin Dashboard View - Enhanced UI
// Included by admin/index.php when admin is logged in.
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 md:mb-10 animate-fadeIn" style="animation-delay: 100ms;">
        <h1 class="font-heading text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400 mb-2">
            Admin Dashboard
        </h1>
        <p class="text-base md:text-lg text-gray-400">System overview and management panel.</p>
    </div>

    <section class="mb-10 md:mb-12 animate-fadeIn" style="animation-delay: 200ms;">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-200 flex items-center gap-2">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                System Overview
            </h2>
            <button id="refresh-overview-btn" title="Refresh Overview" class="ml-auto p-1.5 text-gray-500 hover:text-purple-400 transition duration-150 rounded-full hover:bg-gray-700/50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2m0 0H15"></path></svg>
            </button>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-purple-500/50">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Total Users</span>
                    <div class="p-2 bg-purple-500/20 rounded-xl text-purple-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-white mt-3 font-heading"><span id="stat-total-users">---</span></div>
            </div>
            <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-emerald-500/50">
                 <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Tokens Circulated</span>
                    <div class="p-2 bg-emerald-500/20 rounded-xl text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5"></path></svg>
                    </div>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-emerald-400 mt-3 font-heading"><span id="stat-total-circulated">---</span></div>
            </div>
            <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-yellow-500/50">
                 <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Pending GALAXY</span>
                    <div class="p-2 bg-yellow-500/20 rounded-xl text-yellow-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-yellow-400 mt-3 font-heading"><span id="stat-pending-withdrawals">---</span></div>
            </div>
            <div class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-cyan-500/50">
                 <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Est. Revenue</span>
                    <div class="p-2 bg-cyan-500/20 rounded-xl text-cyan-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                    </div>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-cyan-400 mt-3 font-heading">$<span id="stat-total-revenue">---</span></div>
            </div>
        </div>
    </section>

    <section class="mb-10 md:mb-12 grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

        <div id="pending-withdrawals-section" class="lg:col-span-6 hidden animate-fadeIn" style="animation-delay: 300ms;"> 
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-0 flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    Pending Withdrawals
                </h2>
                 <button id="refresh-pending-btn" title="Refresh Pending" class="ml-auto p-1.5 text-gray-500 hover:text-yellow-400 transition duration-150 rounded-full hover:bg-gray-700/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2m0 0H15"></path></svg>
                 </button>
            </div>
             <div class="glass-card p-0 shadow-lg overflow-hidden"> 
                <div class="min-h-[200px] relative"> 
                    <div id="pending-loader" class="absolute inset-0 flex items-center justify-center bg-[var(--bg-card)] z-10 hidden">
                         <div class="spinner !w-8 !h-8 !border-4"></div>
                    </div>
                    <div id="pending-empty" class="text-center py-10 px-4 text-gray-500 text-sm hidden">No pending withdrawals found.</div>
                     
                    <div class="overflow-x-auto">
                         <table class="w-full min-w-[600px]">
                             <thead class="bg-gray-900/50">
                                 <tr class="border-b border-[var(--border-color)] text-xs">
                                     <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">User</th>
                                     <th class="text-right py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Amount</th>
                                     <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Address</th>
                                     <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Requested</th>
                                     <th class="text-right py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Actions</th>
                                 </tr>
                             </thead>
                             <tbody id="pending-withdrawals-body">
                                 </tbody>
                         </table>
                     </div>
                 </div>
             </div>
        </div>

        <div class="lg:col-span-6 animate-fadeIn" style="animation-delay: 400ms;">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-4 flex items-center gap-2">
                 <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                 Recent System Activity (Last 5)
            </h2>
            <div class="glass-card p-0 shadow-lg overflow-hidden">
                <div class="min-h-[200px] relative">
                     <div id="activity-loader" class="absolute inset-0 flex items-center justify-center bg-[var(--bg-card)] z-10 hidden">
                         <div class="spinner !w-8 !h-8 !border-4"></div>
                     </div>
                     <div id="activity-empty" class="text-center py-10 px-4 text-gray-500 text-sm hidden">No recent activity found.</div>
                    <div class="overflow-x-auto">
                         <table class="w-full min-w-[500px]">
                             <thead class="bg-gray-900/50">
                                 <tr class="border-b border-[var(--border-color)] text-xs">
                                     <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">User</th>
                                     <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Type</th>
                                     <th class="text-right py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Amount</th>
                                     <th class="text-center py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Status</th>
                                     <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Date</th>
                                 </tr>
                             </thead>
                             <tbody id="activity-body">
                                 </tbody>
                         </table>
                     </div>
                </div>
            </div>
        </div>
    </section>

    <section class="animate-fadeIn" style="animation-delay: 500ms;">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-0 flex items-center gap-2">
                 <svg class="w-6 h-6 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                 User Management
            </h2>
             <button id="refresh-users-btn" title="Refresh Users" class="ml-auto p-1.5 text-gray-500 hover:text-purple-400 transition duration-150 rounded-full hover:bg-gray-700/50">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2m0 0H15"></path></svg>
             </button>
        </div>
        <div class="glass-card p-0 shadow-lg overflow-hidden">
            <div class="min-h-[200px] relative">
                 <div id="user-list-loader" class="absolute inset-0 flex items-center justify-center bg-[var(--bg-card)] z-10 hidden">
                    <div class="spinner !w-8 !h-8 !border-4"></div>
                </div>
                 <div id="user-list-empty" class="text-center py-16 px-4 text-gray-500 text-sm hidden">No users found.</div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[768px]">
                        <thead class="bg-gray-900/50">
                            <tr class="border-b border-[var(--border-color)] text-xs">
                                <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">ID</th>
                                <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Username</th>
                                <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Email</th>
                                <th class="text-right py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Balance</th>
                                <th class="text-center py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Referrals</th>
                                <th class="text-left py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Joined</th>
                                <th class="text-center py-3 px-4 text-gray-400 uppercase tracking-wider font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="user-list-body">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="balance-adjust-modal" class="fixed inset-0 bg-gray-900/80 backdrop-blur-md z-50 hidden items-center justify-center p-4 transition-opacity duration-300">
    <div class="glass-card p-6 md:p-8 w-full max-w-md mx-auto rounded-xl shadow-2xl shadow-purple-900/30 border border-purple-500/30 relative">
        <button class="close-modal-btn absolute top-4 right-4 p-1 text-gray-500 hover:text-red-400 transition rounded-full hover:bg-gray-700/50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="font-heading text-xl font-semibold text-white mb-2">Adjust User Balance</h3>
        <p class="text-sm text-gray-400 mb-5">
            User ID: <span id="adjust-user-id" class="font-bold text-gray-200 font-mono"></span>
            (<span id="adjust-username" class="text-purple-400 font-medium"></span>)
        </p>

        <form id="adjust-balance-form" class="space-y-4">
            <input type="hidden" id="adjustment-user-id" name="user_id">

            <div>
                <label for="adjustment-amount" class="block text-sm font-medium text-gray-300 mb-1">Amount (GALAXY)</label>
                <input type="number" id="adjustment-amount" name="amount" placeholder="e.g., 1000 or -500" required step="0.01"
                       class="w-full px-4 py-2 bg-gray-900/70 border border-gray-600 rounded-md text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
                <p class="text-xs text-gray-500 mt-1">Use a negative number to deduct tokens.</p>
            </div>

            <div>
                <label for="adjustment-details" class="block text-sm font-medium text-gray-300 mb-1">Reason for Adjustment</label>
                <input type="text" id="adjustment-details" name="details" placeholder="Brief reason (e.g., Contest prize)" required maxlength="100"
                       class="w-full px-4 py-2 bg-gray-900/70 border border-gray-600 rounded-md text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition">
            </div>

            <p id="adjust-message" class="text-sm text-center py-1 min-h-[1.25rem]"></p>

            <div class="flex gap-4 pt-3">
                 <button type="button" class="close-modal-btn w-full bg-gray-600/50 hover:bg-gray-500/50 text-gray-300 font-semibold py-2.5 px-4 rounded-md transition duration-200">
                    Cancel
                 </button>
                <button type="submit" id="adjust-submit-btn"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-md transition duration-200 flex items-center justify-center shadow-lg shadow-purple-500/30 hover:shadow-purple-500/40">
                    <span id="adjust-submit-text">Apply Adjustment</span>
                    <span id="adjust-submit-spinner" class="spinner hidden"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Style table row hover */
     tbody tr {
        transition: background-color 0.2s ease-in-out;
     }
     tbody tr:hover { 
        background-color: rgba(55, 65, 81, 0.3); /* gray-700/30 */
     }
</style>