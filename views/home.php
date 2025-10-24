<?php
// Premium Home Page View
// Note: This file REQUIRES views/auth_form.php to be present.
// The primary authentication logic lives in the reusable auth_form.php component.
?>
<!-- Hero Section with Advanced Design -->
<section class="hero-container">
    <div class="container">
        <div class="grid lg:grid-cols-2 gap-10 md:gap-16 items-center">
            <!-- Left Column - Hero Content -->
            <div class="lg:pr-12">
                <div class="inline-block mb-4 md:mb-6 px-4 md:px-6 py-1 md:py-2 rounded-full bg-purple-500/10 border border-purple-500/30 backdrop-blur-sm">
                    <span class="text-purple-400 text-xs md:text-sm font-semibold">🚀 Next-Gen Solana Token</span>
                </div>
                
                <h1 class="hero-title">
                    The <span class="gradient-text">Immutable</span><br>
                    Digital Foundation
                </h1>
                
                <p class="text-lg md:text-xl text-gray-300 mb-6 md:mb-8 leading-relaxed max-w-xl">
                    PITHOS is a <strong class="text-white">security-first</strong> utility token with permanently locked contract authority, 
                    built for the Solana ecosystem. <span class="text-cyan-400">Trust guaranteed</span>, yield optimized.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-8 md:mb-12">
                    <a href="#auth" onclick="showAuthForm('register')" class="btn-primary py-3 px-6 md:py-4 md:px-8 inline-flex items-center justify-center gap-3 text-center group text-base md:text-lg">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Get 1,000 FREE Tokens
                    </a>
                    <a href="assets/docs/roadmap.pdf" target="_blank" class="btn-secondary py-3 px-6 md:py-4 md:px-8 inline-flex items-center justify-center gap-3 text-center group text-base md:text-lg">
                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Whitepaper
                    </a>
                </div>

                <!-- Trust Badges with Icons -->
                <div class="trust-badges">
                    <div class="badge group cursor-pointer">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Contract Immutability</div>
                            <div class="text-xs text-gray-400">Permanently Locked</div>
                        </div>
                    </div>
                    
                    <div class="badge group cursor-pointer">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Authorities Revoked</div>
                            <div class="text-xs text-gray-400">Zero Rug Risk</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Premium Auth Card -->
            <div id="auth" class="w-full lg:max-w-lg mx-auto">
                <?php require_once(ROOT_DIR . '/views/auth_form.php'); ?>
            </div>
        </div>
    </div>
</section>

<!-- Features Section with Premium Design -->
<section class="py-16 md:py-24 relative">
    <div class="container">
        <div class="text-center mb-12 md:mb-20">
            <div class="inline-block mb-4 md:mb-6 px-4 md:px-6 py-1 md:py-2 rounded-full bg-cyan-500/10 border border-cyan-500/30 backdrop-blur-sm">
                <span class="text-cyan-400 text-sm font-semibold">✨ Why Choose PITHOS</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-bold mb-4 md:mb-6">
                Trust Built In. <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">Yield</span> Optimized.
            </h2>
            <p class="text-base md:text-xl text-gray-400 max-w-3xl mx-auto">
                Experience the next generation of secure, immutable tokenomics on Solana
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <!-- Feature 1 -->
            <div class="glass-card p-6 md:p-8 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.964Z" />
                    </svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold mb-3 text-white">Zero Rug Risk</h3>
                <p class="text-gray-400 leading-relaxed text-sm">
                    Minting keys and administrative authority are permanently burned on-chain, guaranteeing
                    that the contract cannot be modified or exploited by the founders.
                </p>
                <div class="mt-4 pt-4 border-t border-white/10">
                    <span class="text-sm text-green-400 font-semibold">✓ Verified on-chain</span>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="glass-card p-6 md:p-8 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5m18 0v3.75A2.25 2.25 0 0 1 18.75 18H5.25A2.25 2.25 0 0 1 3 15.75v-3.75" />
                    </svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold mb-3 text-white">Passive APY Staking</h3>
                <p class="text-gray-400 leading-relaxed text-sm">
                    The Staking Vault offers high-yield passive income, rewarding long-term holders with
                    collected protocol fees to ensure sustained ecosystem growth.
                </p>
                <div class="mt-4 pt-4 border-t border-white/10">
                    <span class="text-sm text-purple-400 font-semibold">↗ Up to 25% APY</span>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="glass-card p-6 md:p-8 group">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A9.06 9.06 0 0 1 6 18.719M12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    </svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold mb-3 text-white">Community Rewards</h3>
                <p class="text-gray-400 leading-relaxed text-sm">
                    Instantly claim your token bonus upon sign-up and earn substantial referral rewards for
                    every new user you onboard to the ecosystem.
                </p>
                <div class="mt-4 pt-4 border-t border-white/10">
                    <span class="text-sm text-yellow-400 font-semibold">🎁 <?php echo number_format(REFERRAL_BONUS); ?> tokens per referral</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 md:py-24 relative">
    <div class="container">
        <div class="glass-card p-6 md:p-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 text-center">
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-2">1B</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider">Max Supply</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500 mb-2">100%</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider">Immutable</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-2">25%</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider">Max APY</div>
                </div>
                <div>
                    <div class="text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 mb-2">0</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider">Rug Risk</div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Input Group Styles */
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