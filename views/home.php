<?php
// Ultra-Premium Home Page View - Next-Level UI
?>
<section class="relative pt-20 pb-12 md:pt-28 md:pb-20 overflow-hidden">
    <!-- Enhanced Background Effects -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-15%] left-[-10%] w-[600px] h-[600px] bg-gradient-to-br from-purple-600/40 via-purple-500/20 to-transparent rounded-full blur-[140px] animate-pulse" style="animation-duration: 8s;"></div>
        <div class="absolute bottom-[-20%] right-[-15%] w-[500px] h-[500px] bg-gradient-to-tl from-cyan-500/30 via-blue-500/15 to-transparent rounded-full blur-[120px] animate-pulse" style="animation-duration: 10s; animation-delay: 2s;"></div>
        <div class="absolute top-[30%] right-[20%] w-[400px] h-[400px] bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-[100px] animate-pulse" style="animation-duration: 12s; animation-delay: 4s;"></div>
    </div>
    
    <div class="container relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16 items-center">
            <!-- Left Column - Hero Content -->
            <div class="lg:pr-8 animate-fadeIn" style="animation-delay: 100ms;">
                <!-- Premium Badge -->
                <div class="inline-flex items-center gap-2.5 mb-5 md:mb-7 px-5 py-2.5 rounded-full bg-gradient-to-r from-purple-500/15 via-cyan-500/15 to-purple-500/15 border border-purple-400/40 backdrop-blur-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/30 transition-all duration-300 hover:scale-105">
                    <div class="w-2 h-2 rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400 animate-pulse"></div>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 via-cyan-300 to-purple-300 text-sm md:text-base font-bold tracking-wide flex items-center gap-2">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        Next-Gen Solana Protocol
                    </span>
                </div>
                
                <!-- Hero Title -->
                <h1 class="font-heading text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-black leading-[0.95] tracking-tighter mb-6 md:mb-8">
                    <span class="block text-white mb-2">The Future of</span>
                    <span class="block gradient-text bg-gradient-to-r from-white via-purple-300 to-cyan-300" style="background-size: 200% auto;">Immutable</span>
                    <span class="block text-white mt-2">Digital Assets</span>
                </h1>
                
                <!-- Enhanced Description -->
                <div class="mb-8 md:mb-10 space-y-4">
                    <p class="text-lg md:text-xl lg:text-2xl text-gray-300 leading-relaxed">
                        <?php echo TOKEN_NAME; ?> delivers <span class="text-white font-bold">unparalleled security</span> with permanently locked smart contracts on Solana.
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                            <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-emerald-300 font-semibold text-sm">Zero Rug Risk</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-500/10 border border-purple-500/30">
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            <span class="text-purple-300 font-semibold text-sm">25% Max APY</span>
                        </div>
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-cyan-500/10 border border-cyan-500/30">
                            <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            <span class="text-cyan-300 font-semibold text-sm">100% Immutable</span>
                        </div>
                    </div>
                </div>

                <!-- Premium CTA Buttons -->
                <div class="flex flex-col sm:flex-row flex-wrap gap-4 mb-10 md:mb-12">
                    <a href="#auth" onclick="showAuthForm('register')" class="group relative btn-primary py-4 px-8 text-base md:text-lg font-bold overflow-hidden !from-emerald-500 !via-cyan-500 !to-emerald-500 !border-emerald-400 !shadow-emerald-500/40 hover:!shadow-emerald-500/60">
                        <span class="relative z-10 flex items-center gap-3">
                            <svg class="w-6 h-6 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Claim <?php echo number_format(KYC_BONUS, 0); ?> FREE Tokens
                        </span>
                    </a>
                    
                    <a href="assets/PITHOS_Protocol_Whitepaper.pdf" download target="_blank" class="btn-secondary py-4 px-8 text-base md:text-lg font-bold group hover:!border-purple-400">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Whitepaper
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="group relative p-5 bg-gradient-to-br from-emerald-500/5 to-emerald-500/0 border border-emerald-500/20 rounded-2xl hover:border-emerald-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-emerald-500/20 cursor-pointer">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-white text-base mb-1">Contract Immutable</div>
                                <div class="text-xs text-gray-400">Authority Permanently Revoked</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="group relative p-5 bg-gradient-to-br from-purple-500/5 to-purple-500/0 border border-purple-500/20 rounded-2xl hover:border-purple-500/40 transition-all duration-300 hover:shadow-lg hover:shadow-purple-500/20 cursor-pointer">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <div class="flex-1">
                                <div class="font-bold text-white text-base mb-1">Verified Secure</div>
                                <div class="text-xs text-gray-400">Audited Smart Contracts</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Auth Form -->
            <div id="auth" class="w-full lg:max-w-xl mx-auto animate-fadeIn" style="animation-delay: 200ms;">
                <?php require_once(ROOT_DIR . '/views/auth_form.php'); ?>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Features Section -->
<section class="py-20 md:py-28 relative">
    <div class="absolute inset-0 bg-gradient-to-b from-transparent via-purple-900/5 to-transparent"></div>
    
    <div class="container relative z-10">
        <div class="text-center mb-16 md:mb-20 animate-fadeIn" style="animation-delay: 300ms;">
            <div class="inline-flex items-center gap-2.5 mb-6 px-5 py-2.5 rounded-full bg-gradient-to-r from-cyan-500/15 via-purple-500/15 to-cyan-500/15 border border-cyan-400/40 backdrop-blur-xl shadow-lg">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                <span class="text-cyan-300 font-bold tracking-wide">Platform Features</span>
            </div>
            <h2 class="font-heading text-4xl md:text-5xl lg:text-6xl font-black mb-6">
                Why <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-purple-400 to-cyan-400"><?php echo TOKEN_NAME; ?></span> Leads
            </h2>
            <p class="text-lg md:text-xl text-gray-400 max-w-3xl mx-auto leading-relaxed">
                Built on Solana's high-performance blockchain with enterprise-grade security and innovative tokenomics
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Feature Card 1 -->
            <div class="group relative glass-card p-8 hover:shadow-2xl hover:shadow-emerald-900/40 hover:-translate-y-2 transition-all duration-500 animate-fadeIn overflow-hidden" style="animation-delay: 400ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-emerald-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 via-emerald-500 to-emerald-600 flex items-center justify-center mb-6 shadow-xl shadow-emerald-500/40 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.964Z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold font-heading mb-4 text-white group-hover:text-emerald-300 transition-colors">Zero Rug Risk</h3>
                    <p class="text-gray-400 leading-relaxed mb-6">
                        All administrative authorities permanently burned on-chain. Contract modifications are mathematically impossible, ensuring absolute security.
                    </p>
                    <div class="pt-4 border-t border-white/10">
                        <span class="inline-flex items-center gap-2 text-sm text-emerald-400 font-bold">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Verified On-Chain
                        </span>
                    </div>
                </div>
            </div>

            <!-- Feature Card 2 -->
            <div class="group relative glass-card p-8 hover:shadow-2xl hover:shadow-purple-900/40 hover:-translate-y-2 transition-all duration-500 animate-fadeIn overflow-hidden" style="animation-delay: 500ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-400 via-purple-500 to-purple-700 flex items-center justify-center mb-6 shadow-xl shadow-purple-500/40 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5m18 0v3.75A2.25 2.25 0 0 1 18.75 18H5.25A2.25 2.25 0 0 1 3 15.75v-3.75"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold font-heading mb-4 text-white group-hover:text-purple-300 transition-colors">Premium Staking</h3>
                    <p class="text-gray-400 leading-relaxed mb-6">
                        Earn up to 25% APY through our innovative staking vault. Passive income streams funded by protocol fees and ecosystem growth.
                    </p>
                    <div class="pt-4 border-t border-white/10">
                        <span class="inline-flex items-center gap-2 text-sm text-purple-400 font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Up to 25% APY
                        </span>
                    </div>
                </div>
            </div>

            <!-- Feature Card 3 -->
            <div class="group relative glass-card p-8 hover:shadow-2xl hover:shadow-orange-900/40 hover:-translate-y-2 transition-all duration-500 animate-fadeIn overflow-hidden" style="animation-delay: 600ms;">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-orange-500/20 to-transparent rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-yellow-400 via-orange-500 to-orange-600 flex items-center justify-center mb-6 shadow-xl shadow-orange-500/40 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A9.06 9.06 0 0 1 6 18.719M12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                    </div>
                    <h3 class="text-2xl font-bold font-heading mb-4 text-white group-hover:text-orange-300 transition-colors">Referral Rewards</h3>
                    <p class="text-gray-400 leading-relaxed mb-6">
                        Earn <?php echo number_format(REFERRAL_BONUS, 0); ?> <?php echo TOKEN_SYMBOL; ?> for every user you refer. Build your network and grow your portfolio simultaneously.
                    </p>
                    <div class="pt-4 border-t border-white/10">
                        <span class="inline-flex items-center gap-2 text-sm text-orange-400 font-bold">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h4a1 1 0 100-2H8z"></path></svg>
                            <?php echo number_format(REFERRAL_BONUS, 0); ?> <?php echo TOKEN_SYMBOL; ?> per Referral
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Trusted By Section (keeping your exchange badges but enhanced) -->
<section class="py-16 md:py-20 relative bg-gradient-to-b from-transparent via-black/20 to-transparent">
    <div class="max-w-5xl mx-auto px-6 animate-fadeIn" style="animation-delay: 700ms;">
        <h2 class="font-heading text-3xl md:text-4xl font-black text-white text-center mb-12">
            Trusted &amp; <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400">Supported</span> By Leading Exchanges
        </h2>

        <div class="backed-list glass-card p-8 md:p-10 rounded-3xl border-2 border-purple-500/20 shadow-2xl shadow-purple-900/30">
            <div class="flex flex-col sm:flex-row sm:justify-center sm:flex-wrap gap-6">
                <a href="https://www.binance.com" target="_blank" rel="noopener noreferrer" class="exchange-pill">
                    <span class="exchange-icon">
                        <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/270.png" alt="Binance" loading="lazy">
                    </span>
                    <span class="exchange-text">
                        <span class="exchange-title">Binance</span>
                        <span class="exchange-sub">Global Leader</span>
                    </span>
                </a>

                <a href="https://www.coinbase.com" target="_blank" rel="noopener noreferrer" class="exchange-pill">
                    <span class="exchange-icon">
                        <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/89.png" alt="Coinbase" loading="lazy">
                    </span>
                    <span class="exchange-text">
                        <span class="exchange-title">Coinbase</span>
                        <span class="exchange-sub">NASDAQ Listed</span>
                    </span>
                </a>

                <a href="https://www.kraken.com" target="_blank" rel="noopener noreferrer" class="exchange-pill">
                    <span class="exchange-icon">
                        <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/24.png" alt="Kraken" loading="lazy">
                    </span>
                    <span class="exchange-text">
                        <span class="exchange-title">Kraken</span>
                        <span class="exchange-sub">Since 2011</span>
                    </span>
                </a>

                <a href="https://www.okx.com" target="_blank" rel="noopener noreferrer" class="exchange-pill">
                    <span class="exchange-icon">
                        <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/294.png" alt="OKX" loading="lazy">
                    </span>
                    <span class="exchange-text">
                        <span class="exchange-title">OKX</span>
                        <span class="exchange-sub">Global Reach</span>
                    </span>
                </a>

                <a href="https://www.kucoin.com" target="_blank" rel="noopener noreferrer" class="exchange-pill">
                    <span class="exchange-icon">
                        <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/302.png" alt="KuCoin" loading="lazy">
                    </span>
                    <span class="exchange-text">
                        <span class="exchange-title">KuCoin</span>
                        <span class="exchange-sub">Top Listings</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced Stats Section -->
<section class="py-20 md:py-28 relative">
    <div class="container animate-fadeIn" style="animation-delay: 800ms;">
        <div class="glass-card p-10 md:p-16 border-2 border-purple-500/30 shadow-2xl shadow-purple-900/40 rounded-3xl overflow-hidden relative">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-purple-500/5 via-transparent to-cyan-500/5 pointer-events-none"></div>
            <div class="relative z-10 grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-center">
                <div class="group cursor-pointer">
                    <div class="font-heading text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-3 group-hover:scale-110 transition-transform duration-300">1B</div>
                    <div class="text-gray-400 text-sm md:text-base uppercase tracking-wider font-bold">Total Supply</div>
                </div>
                <div class="group cursor-pointer">
                    <div class="font-heading text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-500 mb-3 group-hover:scale-110 transition-transform duration-300">100%</div>
                    <div class="text-gray-400 text-sm md:text-base uppercase tracking-wider font-bold">Immutable</div>
                </div>
                <div class="group cursor-pointer">
                    <div class="font-heading text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-3 group-hover:scale-110 transition-transform duration-300">25%</div>
                    <div class="text-gray-400 text-sm md:text-base uppercase tracking-wider font-bold">Max APY</div>
                </div>
                <div class="group cursor-pointer">
                    <div class="font-heading text-5xl md:text-6xl lg:text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 mb-3 group-hover:scale-110 transition-transform duration-300">0</div>
                    <div class="text-gray-400 text-sm md:text-base uppercase tracking-wider font-bold">Rug Risk</div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Enhanced exchange pill styles */
.exchange-pill {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    padding: 12px 18px;
    border-radius: 16px;
    text-decoration: none;
    transition: all 250ms cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 220px;
    color: inherit;
    background: linear-gradient(135deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
    border: 1px solid rgba(255,255,255,0.08);
}

.exchange-pill .exchange-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    background: rgba(255,255,255,0.04);
    box-shadow: 0 4px 16px rgba(0,0,0,0.5);
    border: 1px solid rgba(255,255,255,0.1);
}

.exchange-pill .exchange-icon img {
    width: 32px;
    height: 32px;
    object-fit: contain;
    display: block;
}

.exchange-text {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.exchange-title {
    font-weight: 700;
    font-size: 16px;
    color: #ffffff;
    letter-spacing: 0.3px;
    margin-bottom: 2px;
}

.exchange-sub {
    font-size: 12px;
    color: rgba(255,255,255,0.6);
    font-weight: 600;
}

.exchange-pill:hover {
    background: linear-gradient(135deg, rgba(153, 69, 255, 0.08), rgba(20, 241, 149, 0.05));
    border-color: rgba(153, 69, 255, 0.3);
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(153, 69, 255, 0.2);
}

.exchange-pill:hover .exchange-icon {
    transform: scale(1.1);
    box-shadow: 0 8px 24px rgba(153, 69, 255, 0.3);
}

@media (max-width: 640px) {
    .exchange-pill {
        min-width: 100%;
        padding: 14px 16px;
    }
}
</style>