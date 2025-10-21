<?php
// Premium Wallet View
?>
<div class="container">
    <!-- Page Header -->
    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold mb-2">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">My Wallet</span>
            </h1>
            <p class="text-base md:text-xl text-gray-400">Manage your GALAXY balance and transactions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">
        <!-- Left Column - Wallet Summary & Withdrawal -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Balance Card -->
            <div class="glass-card p-6 md:p-8 border-2 border-purple-500/30 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-purple-500/30 rounded-full blur-2xl"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-cyan-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Available Balance</div>
                        </div>
                    </div>
                    
                    <div class="text-5xl font-extrabold mb-1">
                        <span id="token-balance" class="animate-pulse text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-purple-400">---</span>
                    </div>
                    <div class="text-base text-gray-400 font-semibold">GALAXY</div>
                    
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-400">USD Value (Approx)</span>
                            <span class="text-white font-semibold">≈ $<span id="usd-value">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Withdrawal Form -->
            <form id="withdrawal-form" class="glass-card p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold">Withdraw Tokens</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="input-group">
                        <input type="number" id="withdraw-amount" name="amount" placeholder=" " required step="0.01" min="1" class="input-field peer">
                        <label for="withdraw-amount" class="peer-label">Amount (GALAXY)</label>
                    </div>

                    <div class="input-group">
                        <input type="text" id="wallet-address" name="wallet_address" placeholder=" " required class="input-field peer">
                        <label for="wallet-address" class="peer-label">Wallet Address (Solana/EVM)</label>
                    </div>
                    
                    <p id="withdrawal-message" class="text-sm text-center"></p>

                    <button type="submit" id="withdraw-btn" class="btn-primary w-full py-3 flex justify-center items-center gap-3 text-base">
                        <span id="withdraw-text">Initiate Withdrawal</span>
                        <span id="withdraw-spinner" class="spinner hidden"></span>
                    </button>
                </div>
            </form>

            <!-- Security Notice -->
            <div class="glass-card p-4 bg-yellow-500/5 border-yellow-500/30">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-yellow-400 mb-1 text-sm">Important Notice</h3>
                        <p class="text-xs text-gray-400">All withdrawals are processed manually by the PITHOS team for security. Please allow 24-48 hours for processing.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Transaction History -->
        <div class="lg:col-span-8">
            <div class="glass-card p-6 md:p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold mb-1">Transaction History</h2>
                        <p class="text-gray-400 text-sm">All your wallet activities</p>
                    </div>
                    <button class="btn-secondary py-2 px-3 text-sm">
                        Export CSV
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-white/10 text-xs sm:text-sm">
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Type</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Amount</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Status</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Date</th>
                                <th class="text-left py-3 px-2 sm:px-4 text-purple-400 uppercase tracking-wider">Details</th>
                            </tr>
                        </thead>
                        <tbody id="transaction-history-body">
                            <!-- JS will populate -->
                        </tbody>
                    </table>
                    
                    <div id="history-loader" class="text-center py-10">
                        <div class="inline-block w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full animate-spin mb-3"></div>
                        <p class="text-gray-400 text-sm">Loading transaction history...</p>
                    </div>
                    
                    <div id="history-empty" class="text-center py-10 hidden">
                        <p class="text-gray-400 text-base">No transactions yet</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Update USD value based on balance
    function updateUSDValue() {
        const balance = $('#token-balance').text();
        if (balance !== '---' && balance !== 'Error') {
            // Strip commas before calculating
            const numBalance = parseFloat(balance.replace(/,/g, ''));
            const usdValue = (numBalance * 0.001).toFixed(2);
            $('#usd-value').text(parseFloat(usdValue).toLocaleString());
        }
    }
    
    // Check every second (in case fetch is slow)
    setInterval(updateUSDValue, 1000); 
</script>
