<?php
// Premium Dashboard View
// This view is included by public/index.php only when $is_logged_in is true.
?>

<!-- Payment Modal with Premium Design -->
<div id="payment-modal" class="fixed inset-0 bg-black/80 backdrop-blur-xl z-50 flex justify-center items-center hidden transition-all duration-300">
    <div class="glass-card p-6 md:p-8 w-full max-w-lg mx-4 relative overflow-hidden scale-95 transition-all duration-300">
        <!-- Decorative gradient -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-purple-500 via-cyan-500 to-purple-500"></div>
        
        <h3 class="text-3xl font-bold mb-4 text-center">
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">
                Secure Payment Processing
            </span>
        </h3>
        <p class="text-gray-400 text-center mb-6 text-sm md:text-base">Confirming blockchain transaction...</p>
        
        <div class="bg-black/40 backdrop-blur-sm border border-white/10 rounded-2xl p-6 mb-6">
            <div class="text-center">
                <p class="text-gray-400 text-sm mb-2">Amount Due</p>
                <p id="mock-payment-amount" class="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-4">$0.00</p>
                <p class="text-gray-400 text-sm mt-3">Tokens to Receive</p>
                <p id="mock-payment-tokens" class="text-3xl font-bold text-yellow-400">0 GALAXY</p>
            </div>
        </div>
        
        <div class="bg-black/40 backdrop-blur-sm border border-purple-500/30 rounded-xl p-4 mb-6">
            <p class="text-purple-400 font-semibold mb-2 text-xs">Mock Deposit Address (ERC-20):</p>
            <p class="break-all text-gray-300 font-mono text-xs">0x2f8d3D8C8E75C421fE883a4B81A0d1234F0d9876</p>
        </div>
        
        <div id="payment-processing-status" class="text-center py-4 md:py-6">
            <div id="payment-spinner" class="animate-spin inline-block w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full mb-4"></div>
            <p id="payment-status-text" class="text-gray-300 text-sm md:text-base">Awaiting block confirmation...</p>
        </div>

        <button onclick="$('#payment-modal').addClass('hidden');" class="btn-secondary w-full py-3 text-sm md:text-base">
            Close
        </button>
    </div>
</div>

<div class="container">
    <!-- Welcome Banner -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            </h1>
            <p class="text-base md:text-lg text-gray-400">Manage your PITHOS portfolio and explore opportunities</p>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 md:gap-6 mb-8 md:mb-10">
        <!-- Balance Card -->
        <div class="glass-card p-4 md:p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Total Balance</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold mb-1">
                <span id="token-balance" class="animate-pulse">---</span>
            </div>
            <div class="text-xs text-gray-400">GALAXY Tokens</div>
            <span id="user-id-display" class="hidden"><?php echo $_SESSION['user_id'] ?? '0'; ?></span>
        </div>

        <!-- Referral Earnings Card -->
        <div class="glass-card p-4 md:p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-yellow-400 uppercase tracking-wider">Referrals</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold mb-1">
                <span id="dashboard-referral-earnings" class="animate-pulse">0.00</span>
            </div>
            <div class="text-xs text-gray-400">Total Earned</div>
        </div>

        <!-- Price Card -->
        <div class="glass-card p-4 md:p-6 border-l-4 border-cyan-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-cyan-400 to-blue-500 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-cyan-400 uppercase tracking-wider">Token Price</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold mb-1">$0.001</div>
            <div class="text-xs text-gray-400">USD per Token</div>
        </div>

        <!-- Market Cap Card -->
        <div class="glass-card p-4 md:p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                    </svg>
                </div>
                <span class="text-xs font-semibold text-green-400 uppercase tracking-wider">Market Cap</span>
            </div>
            <div class="text-3xl md:text-4xl font-bold mb-1">$1M</div>
            <div class="text-xs text-gray-400">Total Value</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
        <!-- Main Content Area (Plans & Activity) -->
        <div class="lg:col-span-8 space-y-8 md:space-y-12">
            <!-- Token Plans Section -->
            <section id="plans">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Purchase Token Plans</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Plan 1 - Basic -->
                    <div class="glass-card p-6 group">
                        <div class="inline-block px-4 py-1 rounded-full bg-purple-500/20 border border-purple-500/30 text-purple-400 text-xs font-semibold mb-4">
                            STARTER
                        </div>
                        <h3 class="text-2xl font-extrabold mb-2">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-purple-600">$10</span>
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm">Perfect to get started</p>
                        <div class="text-4xl font-extrabold mb-4">
                            <span class="text-white">10,000</span>
                            <div class="text-sm text-gray-400 font-normal mt-1">GALAXY Tokens</div>
                        </div>
                        <button onclick="simulateTokenPurchase(10, 0)" class="btn-secondary w-full py-3 text-base" data-usd="10" data-bonus="0">
                            Buy Now
                        </button>
                    </div>

                    <!-- Plan 2 - Pro (Featured) -->
                    <div class="glass-card p-6 group border-2 border-cyan-500/50 relative transform scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <div class="px-5 py-1 rounded-full bg-gradient-to-r from-cyan-500 to-cyan-600 text-white text-xs font-bold shadow-lg shadow-cyan-500/50">
                                🔥 MOST POPULAR
                            </div>
                        </div>
                        <div class="inline-block px-4 py-1 rounded-full bg-cyan-500/20 border border-cyan-500/30 text-cyan-400 text-xs font-semibold mb-4 mt-4">
                            PRO
                        </div>
                        <h3 class="text-2xl font-extrabold mb-2">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">$100</span>
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm">Best value for serious investors</p>
                        <div class="mb-4">
                            <div class="text-xl line-through text-gray-500 font-semibold">100,000</div>
                            <div class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                                125,000
                            </div>
                            <div class="text-sm text-yellow-400 font-semibold mt-1">+25% BONUS</div>
                        </div>
                        <button onclick="simulateTokenPurchase(100, 0.25)" class="btn-primary w-full py-3 text-base" data-usd="100" data-bonus="0.25">
                            Buy Now
                        </button>
                    </div>

                    <!-- Plan 3 - Enterprise -->
                    <div class="glass-card p-6 group">
                        <div class="inline-block px-4 py-1 rounded-full bg-green-500/20 border border-green-500/30 text-green-400 text-xs font-semibold mb-4">
                            ENTERPRISE
                        </div>
                        <h3 class="text-2xl font-extrabold mb-2">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500">$50</span>
                        </h3>
                        <p class="text-gray-400 mb-6 text-sm">Great value package</p>
                        <div class="mb-4">
                            <div class="text-4xl font-extrabold text-white">55,000</div>
                            <div class="text-sm text-green-400 font-semibold mt-1">+10% BONUS</div>
                        </div>
                        <button onclick="simulateTokenPurchase(50, 0.10)" class="btn-secondary w-full py-3 text-base" data-usd="50" data-bonus="0.10">
                            Buy Now
                        </button>
                    </div>
                </div>
            </section>

            <!-- Recent Activity Section -->
            <section id="activity">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">Recent Activity</h2>
                
                <div class="glass-card p-4 md:p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/10 text-xs sm:text-sm">
                                    <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Type</th>
                                    <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Amount</th>
                                    <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Date</th>
                                    <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody id="recent-tx-body">
                                <tr class="border-b border-white/5 hover:bg-white/5 transition-colors text-xs sm:text-sm">
                                    <td class="py-3 px-2 sm:px-4 font-semibold">Purchase</td>
                                    <td class="py-3 px-2 sm:px-4 text-emerald-400 font-mono">+125,000.00</td>
                                    <td class="py-3 px-2 sm:px-4 text-gray-400"><?php echo date("M d, Y"); ?></td>
                                    <td class="py-3 px-2 sm:px-4">
                                        <span class="status-badge status-complete">Complete</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-6 pt-6 border-t border-white/10">
                        <a href="index.php?p=wallet" class="text-purple-400 hover:text-purple-300 font-semibold inline-flex items-center gap-2 transition-colors text-sm">
                            View Full History
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Referral Card -->
            <section id="referral" class="glass-card p-6 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/20 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                            Referral System
                        </h2>
                    </div>
                    
                    <p class="text-gray-300 mb-4 text-sm">Earn <strong class="text-yellow-400"><?php echo number_format(REFERRAL_BONUS); ?> GALAXY</strong> for each friend who joins!</p>
                    
                    <p id="referrer-info" class="mt-2 text-sm text-emerald-400 hidden mb-4 bg-emerald-500/10 border border-emerald-500/30 p-3 rounded-lg"></p>

                    <div class="space-y-3">
                        <label for="referral-link-input" class="text-sm font-semibold text-gray-400">Your Unique Link:</label>
                        <input type="text" id="referral-link-input" class="w-full p-3 rounded-xl bg-black/40 text-gray-200 border border-white/10 text-xs font-mono" readonly value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?ref=' . ($_SESSION['user_id'] ?? '1'); ?>">
                        <button onclick="copyReferralLink()" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-yellow-500/50 text-sm">
                            📋 Copy Referral Link
                        </button>
                    </div>
                </div>
            </section>
            
            <!-- Sale Status Card -->
            <div class="glass-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold">Sale Status</h3>
                </div>
                
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-white/10">
                        <span class="text-gray-400">KYC Signup Bonus:</span>
                        <span class="font-semibold text-emerald-400"><?php echo number_format(KYC_BONUS); ?> GALAXY</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/10">
                        <span class="text-gray-400">Referral Bonus:</span>
                        <span class="font-semibold text-yellow-400"><?php echo number_format(REFERRAL_BONUS); ?> GALAXY</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-white/10">
                        <span class="text-gray-400">Max Supply:</span>
                        <span class="font-semibold text-white">1 Billion</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-400">Soft Cap:</span>
                        <span class="font-semibold text-green-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Reached
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="glass-card p-6">
                <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="index.php?p=wallet" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Withdraw Tokens</div>
                            <div class="text-xs text-gray-400">Transfer to your wallet</div>
                        </div>
                    </a>
                    <a href="index.php?p=referrals" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Referral Dashboard</div>
                            <div class="text-xs text-gray-400">View your earnings</div>
                        </div>
                    </a>
                    <a href="index.php?p=profile" class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center group-hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Profile Settings</div>
                            <div class="text-xs text-gray-400">Manage your account</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
