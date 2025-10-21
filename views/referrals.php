<?php
// Premium Referrals View
?>
<div class="container">
    <!-- Page Header -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-yellow-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">Referral Dashboard</span>
            </h1>
            <p class="text-base md:text-xl text-gray-400">Track your referral success and maximize earnings</p>
        </div>
    </div>

    <!-- Referral Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-8 md:mb-10">
        <div class="glass-card p-4 md:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Total Referrals</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-1">
                <span id="total-referrals" class="animate-pulse">---</span>
            </div>
            <div class="text-xs text-gray-400">Friends Joined</div>
        </div>
        
        <div class="glass-card p-4 md:p-6 border-l-4 border-emerald-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-green-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Total Earnings</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-500 mb-1">
                <span id="total-earnings" class="animate-pulse">...</span>
            </div>
            <div class="text-xs text-gray-400">GALAXY Tokens</div>
        </div>
        
        <div class="glass-card p-4 md:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Your Referral ID</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-white mb-1">
                <?php echo htmlspecialchars($_SESSION['user_id'] ?? 'N/A'); ?>
            </div>
            <div class="text-xs text-gray-400">Unique Identifier</div>
        </div>
    </div>

    <!-- Referral Link Card with Enhanced Design -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/30 rounded-full blur-3xl"></div>
        
        <div class="relative z-10">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 via-cyan-500 to-purple-500 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-1">Your Unique Referral Link</h2>
                    <p class="text-gray-400 text-sm md:text-base">Share this link to earn <strong class="text-yellow-400"><?php echo number_format(REFERRAL_BONUS); ?> GALAXY</strong> for every friend who signs up!</p>
                </div>
            </div>
            
            <div class="bg-black/40 backdrop-blur-sm border border-purple-500/30 rounded-xl p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" id="referral-link-input" class="flex-1 p-3 rounded-lg bg-white/5 text-gray-200 border border-white/10 text-xs font-mono" readonly value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?') . '?ref=' . ($_SESSION['user_id'] ?? ''); ?>">
                    
                    <button onclick="copyReferralLink()" class="py-3 px-6 whitespace-nowrap rounded-lg text-white font-bold bg-gradient-to-r from-purple-600 to-cyan-600 hover:from-purple-700 hover:to-cyan-700 transition-all duration-300 shadow-lg hover:shadow-purple-500/50 flex items-center justify-center gap-2 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Copy Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Referral History Table -->
    <div class="glass-card p-6 md:p-8">
        <h2 class="text-3xl font-bold mb-6">Referral History</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-white/10 text-xs sm:text-sm">
                        <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Username</th>
                        <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Date Joined</th>
                        <th class="text-right py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Bonus Earned</th>
                    </tr>
                </thead>
                <tbody id="referral-history-body">
                    <!-- JS will populate -->
                </tbody>
            </table>
            
            <div id="history-loader" class="text-center py-10">
                <div class="inline-block w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mb-3"></div>
                <p class="text-gray-400 text-sm">Loading your referral history...</p>
            </div>
            
            <div id="history-empty" class="text-center py-10 hidden">
                <p class="text-gray-400 text-base">You haven't referred anyone yet. Share your link to get started!</p>
            </div>
        </div>
    </div>
</div>
