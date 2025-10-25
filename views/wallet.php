<?php
// Premium Wallet View - ENHANCED UI with Detailed Balances
?>
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden animate-fadeIn" style="animation-delay: 100ms;">
        <div class="absolute top-0 left-0 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl opacity-60"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold font-heading mb-2">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">My Wallet</span>
            </h1>
            <p class="text-base md:text-lg text-gray-400">Manage your GALAXY balance and transactions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
        <!-- Left Column - Wallet Summary & Withdrawal -->
        <div class="lg:col-span-4 space-y-8">
            
            <!-- Balance Card -->
            <div class="glass-card p-6 md:p-8 border-2 border-purple-500/30 relative overflow-hidden animate-fadeIn" style="animation-delay: 200ms;">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/30 rounded-full blur-2xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-cyan-500 flex items-center justify-center shadow-lg shadow-purple-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12"/>
                            </svg>
                        </div>
                        <span class="text-sm text-gray-400 uppercase tracking-wider font-semibold">Total Balance</span>
                    </div>
                    
                    <div class="text-4xl font-extrabold font-heading mb-4">
                        <span id="total-balance" class="animate-pulse text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400">---</span>
                         <span class="text-2xl text-gray-400 font-semibold align-baseline ml-1">GALAXY</span>
                    </div>
                   
                    <!-- Detailed Balance Breakdown -->
                    <div class="space-y-2 text-sm border-t border-[var(--border-color-light)] pt-4 mt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Purchased Balance
                            </span>
                            <span id="purchased-balance" class="text-white font-semibold font-mono animate-pulse">---</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-1.5">
                                 <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                Signup Bonus
                            </span>
                            <span id="bonus-balance" class="text-white font-semibold font-mono animate-pulse">---</span>
                        </div>
                         <div class="flex justify-between items-center">
                            <span class="text-gray-400 flex items-center gap-1.5">
                                 <svg class="w-3.5 h-3.5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Referral Balance <span class="text-xs text-gray-500">(Non-withdrawable)</span>
                            </span>
                            <span id="referral-balance" class="text-white font-semibold font-mono animate-pulse">---</span>
                        </div>
                    </div>
                    
                    <!-- Withdrawable Balance -->
                     <div class="mt-4 pt-4 border-t border-[var(--border-color-light)]">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-300 font-semibold flex items-center gap-1.5">
                                 <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Withdrawable Balance
                            </span>
                            <span id="withdrawable-balance" class="text-green-400 font-bold font-mono animate-pulse">---</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdrawal Form -->
            <form id="withdrawal-form" class="glass-card p-6 md:p-8 animate-fadeIn" style="animation-delay: 300ms;">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold font-heading">Withdraw Tokens</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="input-group">
                        <input type="number" id="withdraw-amount" name="amount" placeholder=" " required step="0.01" min="1" class="input-field peer !bg-black/40">
                        <label for="withdraw-amount" class="peer-label">Amount (GALAXY)</label>
                        <p class="text-xs text-gray-500 mt-1">Enter amount from your withdrawable balance.</p>
                    </div>

                    <div class="input-group">
                        <input type="text" id="wallet-address" name="wallet_address" placeholder=" " required class="input-field peer !bg-black/40">
                        <label for="wallet-address" class="peer-label">Wallet Address (TRC20 / BEP20)</label>
                    </div>
                    
                    <p id="withdrawal-message" class="text-sm text-center min-h-[1.25rem]"></p>

                    <button type="submit" id="withdraw-btn" class="btn-primary w-full py-3 text-base group hover:scale-[1.02] shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/40">
                        <span id="withdraw-text">Initiate Withdrawal</span>
                        <svg class="w-5 h-5 hidden group-hover:block group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                        <span id="withdraw-spinner" class="spinner hidden"></span>
                    </button>
                </div>
            </form>

            <!-- Security Notice -->
            <div class="glass-card p-4 bg-yellow-900/20 border-yellow-500/30 animate-fadeIn" style="animation-delay: 400ms;">
                <div class="flex gap-3">
                    <div class="flex-shrink-0 pt-1">
                        <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"> <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/> </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-yellow-400 mb-1 text-sm">Important Notice</h3>
                        <p class="text-xs text-gray-400">Only Purchased and Signup Bonus tokens are withdrawable. Referral tokens are for future utility. Withdrawals are processed manually (24-48h).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Transaction History -->
        <div class="lg:col-span-8 animate-fadeIn" style="animation-delay: 500ms;">
             <div class="glass-card p-0 overflow-hidden">
                <div class="px-6 md:px-8 pt-6 pb-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-3xl font-bold font-heading mb-1">Transaction History</h2>
                            <p class="text-gray-400 text-sm">All your wallet activities</p>
                        </div>
                        <button class="btn-secondary py-2 px-4 text-sm group" disabled title="Feature coming soon">
                            <svg class="w-4 h-4 transition-transform group-hover:-translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Export CSV</span>
                        </button>
                    </div>
                </div>
                
                <div class="min-h-[300px] relative">
                    <!-- Loader -->
                    <div id="history-loader" class="absolute inset-0 flex items-center justify-center bg-[var(--card-bg)]/80 z-10 rounded-b-2xl">
                        <div class="spinner !w-8 !h-8 !border-4"></div>
                    </div>
                    <!-- Empty State -->
                    <div id="history-empty" class="text-center py-16 px-4 text-gray-500 text-sm hidden">
                         <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        No transactions recorded yet.
                    </div>
                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[650px]">
                            <thead class="bg-black/20">
                                <tr class="border-b border-t border-[var(--border-color)] text-xs sm:text-sm">
                                    <th class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Type</th>
                                    <th class="text-right py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Amount</th>
                                    <th class="text-center py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Status</th>
                                    <th class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Date</th>
                                    <th class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">Details</th>
                                </tr>
                            </thead>
                            <tbody id="transaction-history-body" class="[&>tr:last-child]:border-b-0">
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

    /* Zebra striping & Hover for table */
    #transaction-history-body tr { transition: background-color 0.15s ease-in-out; border-bottom: 1px solid var(--border-color-light); }
    #transaction-history-body tr:nth-child(even) { background-color: rgba(0, 0, 0, 0.1); } 
    #transaction-history-body tr:hover { background-color: rgba(153, 69, 255, 0.1); } 

</style>

<script>
    // Keep the existing USD Value update script here
    function updateUSDValue() {
        const totalBalanceEl = $('#total-balance'); // Target the new total balance ID
        const balance = totalBalanceEl.text();
        if (balance && balance !== '---' && !totalBalanceEl.hasClass('animate-pulse') && !isNaN(parseFloat(balance.replace(/,/g, '')))) {
            const numBalance = parseFloat(balance.replace(/,/g, ''));
            // Assuming TOKEN_RATE is available globally or replace 1000 with <?php echo TOKEN_RATE; ?>
            const usdValue = (numBalance * (1 / <?php echo TOKEN_RATE; ?>)).toFixed(2); 
            $('#usd-value').text(parseFloat(usdValue).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        } else {
            $('#usd-value').text('0.00'); 
        }
    }
    
    $(document).ready(function() {
        const totalBalanceEl = document.getElementById('total-balance'); // Use new ID
        if (totalBalanceEl) {
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    if (mutation.type === 'childList' || mutation.type === 'characterData') {
                        updateUSDValue();
                    }
                });
            });
            observer.observe(totalBalanceEl, { childList: true, characterData: true, subtree: true });
        }
        setInterval(updateUSDValue, 2000); 
    });
</script>

