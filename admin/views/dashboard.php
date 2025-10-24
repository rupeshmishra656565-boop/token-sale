<?php
// Admin Dashboard View - Enhanced UI
// Included by admin/index.php when admin is logged in.
?>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 md:mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-red-400 to-yellow-500 mb-2">
            Admin Dashboard
        </h1>
        <p class="text-base md:text-lg text-gray-400">System overview and management panel.</p>
    </div>

    <section class="mb-10 md:mb-12">
        <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            System Overview
            <button id="refresh-overview-btn" title="Refresh Overview" class="ml-auto p-1.5 text-gray-500 hover:text-purple-400 transition duration-150 rounded-full hover:bg-gray-700/50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2m0 0H15"></path></svg>
            </button>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <div class="glass-card p-4 md:p-5 shadow-lg border-l-4 border-purple-500">
                <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Total Users</div>
                <div class="text-3xl md:text-4xl font-bold text-white"><span id="stat-total-users">---</span></div>
            </div>
            <div class="glass-card p-4 md:p-5 shadow-lg border-l-4 border-emerald-500">
                <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Tokens Circulated</div>
                <div class="text-3xl md:text-4xl font-bold text-emerald-400"><span id="stat-total-circulated">---</span></div>
                 <div class="text-xs text-gray-500">GALAXY</div>
            </div>
            <div class="glass-card p-4 md:p-5 shadow-lg border-l-4 border-yellow-500">
                <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Pending Withdrawals</div>
                <div class="text-3xl md:text-4xl font-bold text-yellow-400"><span id="stat-pending-withdrawals">---</span></div>
                 <div class="text-xs text-gray-500">GALAXY</div>
            </div>
            <div class="glass-card p-4 md:p-5 shadow-lg border-l-4 border-cyan-500">
                <div class="text-xs text-gray-400 uppercase tracking-wider mb-2">Est. Revenue (USD)</div>
                <div class="text-3xl md:text-4xl font-bold text-cyan-400">$<span id="stat-total-revenue">---</span></div>
                 <div class="text-xs text-gray-500">Based on completed purchases</div>
            </div>
        </div>
    </section>

    <section class="mb-10 md:mb-12 grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">

        <div id="pending-withdrawals-section" class="lg:col-span-6 hidden"> <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-4 flex items-center">
                 <svg class="w-6 h-6 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                 Pending Withdrawals
                 <button id="refresh-pending-btn" title="Refresh Pending" class="ml-auto p-1.5 text-gray-500 hover:text-yellow-400 transition duration-150 rounded-full hover:bg-gray-700/50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2m0 0H15"></path></svg>
                 </button>
             </h2>
             <div class="glass-card p-0 shadow-lg overflow-hidden"> <div class="min-h-[100px] relative"> <div id="pending-loader" class="absolute inset-0 flex items-center justify-center bg-gray-800/50 z-10 hidden">
                         <div class="spinner !w-8 !h-8 !border-4"></div>
                     </div>
                     <div id="pending-empty" class="text-center py-10 px-4 text-gray-500 text-sm hidden">No pending withdrawals found.</div>
                     <div class="overflow-x-auto">
                         <table class="w-full min-w-[600px]">
                             <thead class="bg-gray-700/30">
                                 <tr class="border-b border-gray-700/50 text-xs">
                                     <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">User</th>
                                     <th class="text-right py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Amount</th>
                                     <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Address</th>
                                     <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Requested</th>
                                     <th class="text-right py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Actions</th>
                                 </tr>
                             </thead>
                             <tbody id="pending-withdrawals-body" class="divide-y divide-gray-700/50">
                                 </tbody>
                         </table>
                     </div>
                 </div>
             </div>
        </div>

        <div class="lg:col-span-6">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-4 flex items-center">
                 <svg class="w-6 h-6 mr-2 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                 Recent System Activity (Last 5)
                 </h2>
            <div class="glass-card p-0 shadow-lg overflow-hidden">
                <div class="min-h-[100px] relative">
                     <div id="activity-loader" class="absolute inset-0 flex items-center justify-center bg-gray-800/50 z-10 hidden">
                         <div class="spinner !w-8 !h-8 !border-4"></div>
                     </div>
                     <div id="activity-empty" class="text-center py-10 px-4 text-gray-500 text-sm hidden">No recent activity found.</div>
                    <div class="overflow-x-auto">
                         <table class="w-full min-w-[500px]">
                             <thead class="bg-gray-700/30">
                                 <tr class="border-b border-gray-700/50 text-xs">
                                     <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">User</th>
                                     <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Type</th>
                                     <th class="text-right py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Amount</th>
                                     <th class="text-center py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Status</th>
                                      <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Date</th>
                                 </tr>
                             </thead>
                             <tbody id="activity-body" class="divide-y divide-gray-700/50">
                                 </tbody>
                         </table>
                     </div>
                </div>
            </div>
        </div>
    </section>


    <section>
        <h2 class="text-xl md:text-2xl font-semibold text-gray-200 mb-4 flex items-center">
             <svg class="w-6 h-6 mr-2 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
             User Management
             <button id="refresh-users-btn" title="Refresh Users" class="ml-auto p-1.5 text-gray-500 hover:text-purple-400 transition duration-150 rounded-full hover:bg-gray-700/50">
                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m-15.357-2a8.001 8.001 0 0015.357 2m0 0H15"></path></svg>
             </button>
         </h2>
        <div class="glass-card p-0 shadow-lg overflow-hidden">
            <div class="min-h-[200px] relative">
                 <div id="user-list-loader" class="absolute inset-0 flex items-center justify-center bg-gray-800/50 z-10 hidden">
                    <div class="spinner !w-8 !h-8 !border-4"></div>
                </div>
                 <div id="user-list-empty" class="text-center py-16 px-4 text-gray-500 text-sm hidden">No users found.</div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[768px]">
                        <thead class="bg-gray-700/30">
                            <tr class="border-b border-gray-700/50 text-xs">
                                <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">ID</th>
                                <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Username</th>
                                <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Email</th>
                                <th class="text-right py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Balance</th>
                                <th class="text-center py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Referrals</th>
                                <th class="text-left py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Joined</th>
                                <th class="text-center py-2.5 px-4 text-gray-400 uppercase tracking-wider font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="user-list-body" class="divide-y divide-gray-700/50">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div id="balance-adjust-modal" class="fixed inset-0 bg-gray-900/80 backdrop-blur-md z-50 hidden items-center justify-center p-4 transition-opacity duration-300">
    <div class="glass-card p-6 md:p-8 w-full max-w-md mx-auto rounded-xl shadow-xl border border-purple-500/30">
        <div class="flex justify-between items-center mb-4">
             <h3 class="text-xl font-semibold text-white">Adjust User Balance</h3>
             <button class="close-modal-btn p-1 text-gray-500 hover:text-red-400 transition rounded-full hover:bg-gray-700/50">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
             </button>
        </div>

        <p class="text-sm text-gray-400 mb-5">
            User ID: <span id="adjust-user-id" class="font-bold text-gray-200 font-mono"></span>
            (<span id="adjust-username" class="text-purple-400 font-medium"></span>)
        </p>

        <form id="adjust-balance-form" class="space-y-4">
            <input type="hidden" id="adjustment-user-id" name="user_id">

            <div>
                <label for="adjustment-amount" class="block text-sm font-medium text-gray-300 mb-1">Amount (GALAXY)</label>
                <input type="number" id="adjustment-amount" name="amount" placeholder="e.g., 1000 or -500" required step="0.01"
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-md text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">Use a negative number to deduct tokens.</p>
            </div>

            <div>
                <label for="adjustment-details" class="block text-sm font-medium text-gray-300 mb-1">Reason for Adjustment</label>
                <input type="text" id="adjustment-details" name="details" placeholder="Brief reason..." required maxlength="100"
                       class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-md text-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500">
            </div>

            <p id="adjust-message" class="text-sm text-center py-1"></p>

            <div class="flex gap-4 pt-3">
                 <button type="button" class="close-modal-btn w-full bg-gray-600/50 hover:bg-gray-500/50 text-gray-300 font-semibold py-2.5 px-4 rounded-md transition duration-200">
                    Cancel
                 </button>
                <button type="submit" id="adjust-submit-btn"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2.5 px-4 rounded-md transition duration-200 flex items-center justify-center">
                    <span id="adjust-submit-text">Apply Adjustment</span>
                    <span id="adjust-submit-spinner" class="spinner hidden ml-2"></span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .status-unknown { background-color: rgba(107, 114, 128, 0.2); color: #9ca3af; } /* Gray */
    /* Enhance table hover */
     tbody tr:hover { background-color: rgba(55, 65, 81, 0.3); } /* gray-700/30 */
</style>