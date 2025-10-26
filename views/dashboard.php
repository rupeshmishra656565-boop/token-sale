<?php
// Premium Dashboard View - ENHANCED UI
?>

<div id="payment-modal"
    class="fixed inset-0 bg-gradient-to-br from-gray-900 via-purple-900/80 to-gray-900 backdrop-blur-lg z-50 flex justify-center items-center hidden transition-opacity duration-300 px-4 py-8"
    data-usd-amount="0" data-bonus-percent="0" data-transaction-id="">
    <div
        class="glass-card bg-gray-900/70 border border-purple-500/30 p-6 sm:p-8 w-full max-w-md mx-auto relative overflow-hidden scale-95 transition-all duration-300 shadow-2xl shadow-purple-500/20 max-h-full overflow-y-auto rounded-3xl">
        <div
            class="absolute -top-1/4 -left-1/4 w-1/2 h-1/2 bg-purple-600/30 rounded-full blur-3xl opacity-50 animate-pulse">
        </div>
        <div
            class="absolute -bottom-1/4 -right-1/4 w-1/2 h-1/2 bg-cyan-600/30 rounded-full blur-3xl opacity-50 animate-pulse animation-delay-4000">
        </div>

        <div class="relative z-10 mb-6 text-center">
            <svg class="w-12 h-12 mx-auto mb-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                </path>
            </svg>
            <h3 id="modal-title" class="text-2xl font-bold font-heading">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">
                    Confirm Purchase
                </span>
            </h3>
        </div>

        <div class="relative z-10">
            <div id="payment-summary" class="bg-black/30 border border-white/10 rounded-xl p-4 mb-5 text-center">
                <div class="flex justify-around items-center">
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Amount (USD)</p>
                        <p id="mock-payment-amount" class="text-2xl font-bold text-white">$0.00</p>
                    </div>
                    <div class="border-l border-white/10 h-10"></div>
                    <div>
                        <p class="text-gray-400 text-xs uppercase tracking-wider mb-1">Tokens Received</p>
                        <p id="mock-payment-tokens" class="text-2xl font-bold text-yellow-400">0 PITHOS</p>
                    </div>
                </div>
            </div>
            <div id="payment-options" class="space-y-3">
                <p class="text-center text-gray-400 text-sm pb-1">Select payment currency:</p>
                <button onclick="confirmTokenPurchase('usdttrc20')"
                    class="w-full flex items-center justify-between p-3 bg-gradient-to-r from-gray-800/50 to-purple-900/30 hover:to-purple-900/50 border border-white/10 hover:border-purple-400/50 rounded-lg transition-all duration-200 group text-left shadow-sm hover:shadow-md">
                    <div class="flex items-center gap-3"><img src="https://nowpayments.io/images/coins/usdttrc20.svg"
                            alt="TRC20" class="w-7 h-7">
                        <div>
                            <p class="font-semibold text-white">USDT <span class="text-xs text-gray-400">TRC20</span>
                            </p>
                            <p class="text-xs text-gray-500">Tron Network</p>
                        </div>
                    </div><svg
                        class="w-5 h-5 text-purple-400 opacity-60 group-hover:opacity-100 group-hover:translate-x-1 transition-all"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button onclick="confirmTokenPurchase('usdtbsc')"
                    class="w-full flex items-center justify-between p-3 bg-gradient-to-r from-gray-800/50 to-cyan-900/30 hover:to-cyan-900/50 border border-white/10 hover:border-cyan-400/50 rounded-lg transition-all duration-200 group text-left shadow-sm hover:shadow-md">
                    <div class="flex items-center gap-3"><img src="https://nowpayments.io/images/coins/usdtbsc.svg"
                            alt="BSC" class="w-7 h-7">
                        <div>
                            <p class="font-semibold text-white">USDT <span class="text-xs text-gray-400">BSC
                                    (BEP20)</span></p>
                            <p class="text-xs text-gray-500">Binance Smart Chain</p>
                        </div>
                    </div><svg
                        class="w-5 h-5 text-cyan-400 opacity-60 group-hover:opacity-100 group-hover:translate-x-1 transition-all"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button onclick="confirmTokenPurchase('trx')"
                    class="w-full flex items-center justify-between p-3 bg-gradient-to-r from-gray-800/50 to-red-900/30 hover:to-red-900/50 border border-white/10 hover:border-red-400/50 rounded-lg transition-all duration-200 group text-left shadow-sm hover:shadow-md">
                    <div class="flex items-center gap-3"><img src="https://nowpayments.io/images/coins/trx.svg"
                            alt="TRX" class="w-7 h-7">
                        <div>
                            <p class="font-semibold text-white">TRX</p>
                            <p class="text-xs text-gray-500">Tron Network</p>
                        </div>
                    </div><svg
                        class="w-5 h-5 text-red-400 opacity-60 group-hover:opacity-100 group-hover:translate-x-1 transition-all"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button onclick="closePaymentModal()"
                    class="w-full py-2.5 text-sm text-gray-400 hover:text-white bg-transparent hover:bg-white/5 border border-transparent rounded-lg transition mt-3">
                    Cancel </button>
            </div>
            <div id="payment-processing-status" class="text-center py-8 hidden">
                <div id="payment-spinner"
                    class="animate-spin inline-block w-10 h-10 border-4 border-purple-500/30 border-t-purple-500 rounded-full mb-4">
                </div>
                <p id="payment-status-text" class="text-gray-400 text-sm">Creating payment request...</p>
            </div>
            <div id="payment-details-display" class="hidden space-y-4 pt-2">
                <p class="text-center text-sm font-medium text-gray-300"> Please send the exact amount within the time
                    limit.</p>
                <div class="bg-black/40 border border-purple-500/30 rounded-lg p-4 text-center">
                    <label
                        class="text-xs text-purple-400 uppercase tracking-wider font-semibold block mb-1">Amount</label>
                    <div class="flex items-center justify-center gap-2">
                        <span id="payment-details-amount"
                            class="text-xl font-bold font-mono text-white break-all">0.000000</span>
                        <span id="payment-details-currency" class="text-lg font-semibold text-cyan-400">USDT</span>
                        <button id="copy-amount-btn" title="Copy Amount"
                            class="p-1.5 text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-md transition duration-150"><svg
                                class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg></button>
                    </div>
                </div>
                <div
                    class="flex flex-col sm:flex-row items-center gap-4 bg-black/40 border border-white/10 rounded-lg p-4">
                    <div id="payment-qr-code"
                        class="p-1 bg-white rounded inline-block border-2 border-white/30 flex-shrink-0 w-[110px] h-[110px]">
                    </div>
                    <div class="flex-grow text-center sm:text-left w-full">
                        <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold block mb-1">Payment
                            Address</label>
                        <div
                            class="relative bg-gray-800/50 px-3 py-2 rounded group border border-transparent hover:border-purple-500/50">
                            <span id="payment-details-address"
                                class="block text-xs font-mono text-white break-all pr-8">XXXXXXXXXXXXXXXXXX</span>
                            <button id="copy-address-btn" title="Copy Address"
                                class="absolute top-1/2 right-1 transform -translate-y-1/2 p-1.5 text-gray-400 hover:text-purple-400 bg-transparent hover:bg-purple-900/50 rounded transition duration-150 opacity-0 group-hover:opacity-100"><svg
                                    class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                    </path>
                                </svg></button>
                        </div>
                    </div>
                </div>
                <div id="payment-instructions" class="text-center text-xs text-cyan-400 font-medium mt-3 px-1">
                    <span id="polling-status">Waiting for confirmation...</span>
                    <span class="inline-block animate-pulse delay-100">.</span><span
                        class="inline-block animate-pulse delay-200">.</span><span
                        class="inline-block animate-pulse delay-300">.</span>
                </div>
                <button onclick="closePaymentModal()"
                    class="w-full py-2.5 text-sm text-gray-400 hover:text-white bg-transparent hover:bg-white/5 border border-transparent rounded-lg transition mt-3">
                    Close </button>
            </div>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8">

    <div class="glass-card p-6 md:p-8 mb-8 md:mb-10 relative overflow-hidden animate-fadeIn"
        style="animation-delay: 100ms;">
        <div class="absolute top-0 right-0 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl opacity-60"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold font-heading mb-2">
                Welcome back, <span
                    class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            </h1>
            <p class="text-base md:text-lg text-gray-400">Manage your PITHOS portfolio and explore opportunities</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8 md:mb-10 animate-fadeIn"
        style="animation-delay: 200ms;">

        <div
            class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-purple-500/50">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Total Balance</span>
                <div class="p-2 bg-purple-500/20 rounded-xl text-purple-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-white mt-3 font-heading"><span id="token-balance"
                    class="animate-pulse">---</span></div>
            <div class="text-xs text-gray-400">PITHOS Tokens</div>
        </div>

        <div
            class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-yellow-500/50">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Referral Profit</span>
                <div class="p-2 bg-yellow-500/20 rounded-xl text-yellow-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-yellow-400 mt-3 font-heading"><span
                    id="dashboard-referral-earnings" class="animate-pulse">0.00</span></div>
            <div class="text-xs text-gray-400">Total PITHOS Earned</div>
        </div>

        <div
            class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-cyan-500/50">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Token Price</span>
                <div class="p-2 bg-cyan-500/20 rounded-xl text-cyan-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-cyan-400 mt-3 font-heading">
                $<?php echo rtrim(rtrim(sprintf('%.8f', 1 / TOKEN_RATE), '0'), '.'); ?></div>
            <div class="text-xs text-gray-400">USD per PITHOS</div>
        </div>

        <div
            class="glass-card p-5 shadow-lg group transition-all duration-300 hover:scale-[1.03] hover:border-green-500/50">
            <div class="flex items-center justify-between">
                <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Market Cap</span>
                <div class="p-2 bg-green-500/20 rounded-xl text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941">
                        </path>
                    </svg>
                </div>
            </div>
            <div class="text-3xl md:text-4xl font-bold text-green-400 mt-3 font-heading">$1M+</div>
            <div class="text-xs text-gray-400">Estimated Value</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-10">

        <div class="lg:col-span-8 space-y-8 md:space-y-12">
            <section id="plans" class="animate-fadeIn" style="animation-delay: 300ms;">
                <h2 class="text-3xl md:text-4xl font-bold font-heading mb-6">Purchase Token Plans</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">


                    <div
                        class="glass-card p-6 group transition-all duration-300 hover:shadow-2xl hover:shadow-purple-900/50 hover:-translate-y-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2.5 bg-purple-500/20 rounded-xl text-purple-400"><svg class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                    </path>
                                </svg></div>
                            <span class="font-semibold text-purple-400">STARTER</span>
                        </div>

                        <h3 class="text-4xl font-extrabold font-heading mb-2 text-white">$10</h3>
                        <p class="text-gray-400 mb-6 text-sm">Perfect to get started</p>

                        <div class="text-3xl font-extrabold font-heading mb-4"><span class="text-white">10,000</span>
                            <div class="text-sm text-gray-400 font-normal mt-1">PITHOS Tokens</div>
                        </div>

                        <button onclick="showPaymentOptions(10, 0)" class="btn-secondary w-full py-3 text-base"> Buy Now
                        </button>
                    </div>

                    <div
                        class="glass-card p-6 group transition-all duration-300 hover:shadow-2xl hover:shadow-cyan-900/50 hover:-translate-y-1 border-2 border-cyan-500/50 relative">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <div
                                class="px-5 py-1 rounded-full bg-gradient-to-r from-cyan-500 to-cyan-600 text-white text-xs font-bold shadow-lg shadow-cyan-500/50 uppercase tracking-wider">
                                Most Popular</div>
                        </div>
                        <div class="flex items-center gap-3 mb-4 mt-5">
                            <div class="p-2.5 bg-cyan-500/20 rounded-xl text-cyan-400"><svg class="w-5 h-5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg></div>
                            <span class="font-semibold text-cyan-400">PRO</span>
                        </div>
                        <h3 class="text-4xl font-extrabold font-heading mb-2 text-white">$100</h3>
                        <p class="text-gray-400 mb-6 text-sm">Best value for serious investors</p>
                        <div class="mb-4">
                            <div class="text-xl line-through text-gray-500 font-semibold">100,000</div>
                            <div
                                class="text-3xl font-extrabold font-heading text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                                125,000</div>
                            <div class="text-sm text-yellow-400 font-semibold mt-1">+25% BONUS</div>
                        </div>
                        <button onclick="showPaymentOptions(100, 0.25)" class="btn-primary w-full py-3 text-base"> Buy
                            Now </button>
                    </div>

                    <div
                        class="glass-card p-6 group transition-all duration-300 hover:shadow-2xl hover:shadow-green-900/50 hover:-translate-y-1">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2.5 bg-green-500/20 rounded-xl text-green-400"><svg class="w-5 h-5"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg></div>
                            <span class="font-semibold text-green-400">ENTERPRISE</span>
                        </div>
                        <h3 class="text-4xl font-extrabold font-heading mb-2 text-white">$50</h3>
                        <p class="text-gray-400 mb-6 text-sm">Great value package</p>
                        <div class="mb-4">
                            <div class="text-3xl font-extrabold font-heading text-white">55,000</div>
                            <div class="text-sm text-green-400 font-semibold mt-1">+10% BONUS</div>
                        </div>
                        <button onclick="showPaymentOptions(50, 0.10)" class="btn-secondary w-full py-3 text-base"> Buy
                            Now </button>
                    </div>
                </div>
            </section>

            <section id="price-prediction" class="animate-fadeIn" style="animation-delay: 350ms;">
                <h2 class="text-3xl md:text-4xl font-bold font-heading mb-6 flex items-center gap-3">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Future Potential
                </h2>
                <div
                    class="glass-card p-6 md:p-8 relative overflow-hidden border-2 border-yellow-500/30 shadow-xl shadow-yellow-900/30">
                    <div
                        class="absolute -bottom-16 -right-16 w-48 h-48 bg-gradient-to-tl from-yellow-500/20 to-transparent rounded-full blur-3xl opacity-70 animate-pulse">
                    </div>
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                            <div>
                                <h3
                                    class="text-2xl font-bold font-heading text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-1">
                                    Price Outlook</h3>
                                <p class="text-gray-400 text-sm">Illustrative potential based on growth milestones.*</p>
                            </div>
                            <div class="flex-shrink-0">
                                <span
                                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-800 border border-white/10 text-xs text-gray-300 font-medium">
                                    <svg class="w-4 h-4 text-yellow-400 animate-spin" style="animation-duration: 3s;"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                                        </path>
                                    </svg>
                                    Speculative Targets
                                </span>
                            </div>
                        </div>

                        <div class="flex items-end justify-between gap-4 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-xs text-gray-500 uppercase tracking-wider mb-1">Current</div>
                                <div class="font-bold text-lg text-cyan-400">
                                    $<?php echo rtrim(rtrim(sprintf('%.8f', 1 / TOKEN_RATE), '0'), '.'); ?></div>
                                <div class="w-1 h-3 bg-cyan-500/50 rounded-full mt-1"></div>
                            </div>

                            <div class="flex-grow border-b-2 border-dashed border-gray-700 mb-2 relative -bottom-1">
                            </div>

                            <div class="flex flex-col items-center group">
                                <div
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-1 group-hover:text-yellow-400 transition-colors">
                                    Target 1</div>
                                <div
                                    class="font-bold text-2xl text-yellow-400 group-hover:scale-110 transition-transform">
                                    $0.50</div>
                                <div
                                    class="w-1.5 h-6 bg-yellow-500/60 rounded-full mt-1 group-hover:bg-yellow-400 transition-colors">
                                </div>
                            </div>

                            <div class="flex-grow border-b-2 border-dashed border-gray-700 mb-2 relative -bottom-1">
                            </div>

                            <div class="flex flex-col items-center group">
                                <div
                                    class="text-xs text-gray-500 uppercase tracking-wider mb-1 group-hover:text-orange-400 transition-colors">
                                    Target 2</div>
                                <div
                                    class="font-bold text-3xl text-orange-400 group-hover:scale-110 transition-transform">
                                    $5.00</div>
                                <div
                                    class="w-2 h-10 bg-orange-500/70 rounded-full mt-1 group-hover:bg-orange-400 transition-colors flex items-center justify-center relative">
                                    <svg class="w-4 h-4 text-orange-100 absolute -top-5 opacity-0 group-hover:opacity-100 group-hover:-top-7 transition-all duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="m5 10 7-7 7 7M12 17V3"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mt-4 text-center">*This is not financial advice. Token price
                            depends on market conditions, adoption, and project execution.</p>
                    </div>
                </div>
            </section>
            <section id="activity" class="animate-fadeIn" style="animation-delay: 400ms;">
                <h2 class="text-3xl md:text-4xl font-bold font-heading mb-6">Recent Activity (Last 5)</h2>
                <div class="glass-card p-0 md:p-0 overflow-hidden">
                    <div class="min-h-[200px] relative">
                        <div id="activity-loader"
                            class="absolute inset-0 flex items-center justify-center bg-[var(--bg-card)] z-10">
                            <div class="spinner !w-8 !h-8 !border-4"></div>
                        </div>
                        <div id="activity-empty" class="text-center py-16 px-4 text-gray-500 text-sm hidden">
                            <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            No recent transactions found.
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-[var(--border-color)] text-xs sm:text-sm">
                                        <th
                                            class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">
                                            Type</th>
                                        <th
                                            class="text-right py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">
                                            Amount</th>
                                        <th
                                            class="text-center py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">
                                            Status</th>
                                        <th
                                            class="text-left py-3 px-4 text-purple-400 uppercase tracking-wider font-semibold">
                                            Date</th>
                                    </tr>
                                </thead>
                                <tbody id="recent-tx-body" class="divide-y divide-[var(--border-color)]">
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="text-center p-4 border-t border-[var(--border-color)]">
                        <a href="index.php?p=wallet"
                            class="text-purple-400 hover:text-purple-300 font-semibold inline-flex items-center gap-2 transition-colors text-sm group">
                            View Full History
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </section>
        </div>

        <div class="lg:col-span-4 space-y-8">
            <section id="referral" class="glass-card p-6 relative overflow-hidden animate-fadeIn"
                style="animation-delay: 500ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/20 rounded-full blur-3xl opacity-60"></div>
                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                        </div>
                        <h2
                            class="text-xl font-bold font-heading text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                            Referral System </h2>
                    </div>
                    <p class="text-gray-300 mb-4 text-sm">Earn <strong
                            class="text-yellow-400"><?php echo number_format(REFERRAL_BONUS); ?> PITHOS</strong> for
                        each friend who joins!</p>
                    <div class="space-y-3">
                        <label for="referral-link-input" class="text-sm font-semibold text-gray-400">Your Unique
                            Link:</label>
                        <input type="text" id="referral-link-input" class="input-field !text-xs !py-3 !bg-black/40"
                            readonly
                            value="<?php echo 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . strtok($_SERVER['REQUEST_URI'], '?') . '?ref=' . ($_SESSION['user_id'] ?? '1'); ?>">
                        <button onclick="copyReferralLink()"
                            class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 shadow-lg hover:shadow-yellow-500/50 text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z">
                                </path>
                            </svg>
                            Copy Link
                        </button>
                    </div>
                </div>
            </section>

            <div class="glass-card p-6 animate-fadeIn" style="animation-delay: 600ms;">
                <h3 class="text-xl font-bold font-heading mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    <a href="index.php?p=wallet"
                        class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-200 group">
                        <div
                            class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:scale-105 transition-transform flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg></div>
                        <div>
                            <div class="font-semibold text-white text-sm">Withdraw Tokens</div>
                            <div class="text-xs text-gray-400">Transfer to your wallet</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 ml-auto group-hover:text-purple-400 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                    <a href="index.php?p=referrals"
                        class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-200 group">
                        <div
                            class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center group-hover:scale-105 transition-transform flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg></div>
                        <div>
                            <div class="font-semibold text-white text-sm">Referral Dashboard</div>
                            <div class="text-xs text-gray-400">View your earnings</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 ml-auto group-hover:text-yellow-400 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                    <a href="index.php?p=profile"
                        class="flex items-center gap-3 p-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all duration-200 group">
                        <div
                            class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center group-hover:scale-105 transition-transform flex-shrink-0">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg></div>
                        <div>
                            <div class="font-semibold text-white text-sm">Profile Settings</div>
                            <div class="text-xs text-gray-400">Manage your account</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-500 ml-auto group-hover:text-cyan-400 transition-colors"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.input-field {
    width: 100%;
    padding: 1rem 1.5rem;
    background: rgba(0, 0, 0, 0.4);
    border: 2px solid rgba(153, 69, 255, 0.3);
    border-radius: 12px;
    color: #fff;
    outline: none;
    transition: all 0.3s ease;
    font-size: 1rem;
}

.input-field:focus {
    border-color: var(--primary);
    box-shadow: 0 0 15px rgba(153, 69, 255, 0.3);
}

/* Animation delay helper */
.animation-delay-4000 {
    animation-delay: 4s;
}

/* Status Badges for Recent Activity */
.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.2rem 0.6rem;
    border-radius: 99px;
    font-size: 0.7rem;
    font-weight: 600;
    line-height: 1;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-complete {
    background-color: rgba(16, 185, 129, 0.2);
    color: #10b981;
}

.status-pending,
.status-processing {
    background-color: rgba(245, 158, 11, 0.2);
    color: #f59e0b;
}

.status-failed {
    background-color: rgba(239, 68, 68, 0.2);
    color: #ef4444;
}

/* Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.5s ease-out forwards;
}

/* Responsive adjustments for modal */
@media (max-width: 640px) {
    #payment-modal>div {
        padding: 1.25rem;
    }

    #modal-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }

    #payment-summary {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    #payment-summary p:first-child {
        font-size: 0.65rem;
    }

    #payment-summary p:nth-child(2) {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    #payment-summary p:nth-child(3) {
        font-size: 0.65rem;
        margin-top: 0.5rem;
    }

    #payment-summary p:nth-child(4) {
        font-size: 1.25rem;
    }

    #payment-options button {
        padding: 0.75rem;
    }

    #payment-options img {
        width: 1.5rem;
        height: 1.5rem;
    }

    #payment-options p:first-of-type {
        font-size: 0.875rem;
    }

    #payment-options p:last-of-type {
        font-size: 0.75rem;
    }

    #payment-details-display {
        space-y: 0.75rem;
    }

    #payment-details-display>p:first-child {
        font-size: 0.875rem;
    }

    #payment-details-display>div {
        padding: 0.75rem;
    }

    #payment-details-amount {
        font-size: 1.125rem;
    }

    #payment-details-currency {
        font-size: 1rem;
    }

    #payment-details-address {
        font-size: 0.65rem;
    }

    #payment-qr-code {
        width: 90px;
        height: 90px;
        padding: 0.25rem;
    }
}
</style>

<script>
// Pass PHP constants to JavaScript
window.TOKEN_RATE = <?php echo TOKEN_RATE; ?>;
window.TOKEN_SYMBOL = '<?php echo TOKEN_SYMBOL; ?>';
</script>
<script src="assets/main.js"></script>
<script src="assets/dashboard.js"></script>