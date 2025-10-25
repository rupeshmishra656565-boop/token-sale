<?php
// Premium Home Page View - ENHANCED UI
// Includes views/auth_form.php
?>
<!-- Hero Section -->
<section class="relative pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
    <!-- Background Elements (can keep simple or add more complexity) -->
    <div class="absolute inset-0 z-0 opacity-30">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-gradient-to-br from-purple-600/50 to-transparent rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-15%] right-[-15%] w-[400px] h-[400px] bg-gradient-to-tl from-cyan-600/40 to-transparent rounded-full blur-[100px]"></div>
    </div>
    
    <div class="container relative z-10">
        <div class="grid lg:grid-cols-2 gap-10 md:gap-16 items-center">
            <!-- Left Column - Hero Content -->
            <div class="lg:pr-12 animate-fadeIn" style="animation-delay: 100ms;">
                <div class="inline-block mb-4 md:mb-6 px-4 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/30 backdrop-blur-sm shadow-md">
                    <span class="text-purple-300 text-xs md:text-sm font-semibold tracking-wide flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        Next-Gen Solana Token
                    </span>
                </div>
                
                <h1 class="font-heading text-5xl sm:text-6xl lg:text-7xl font-bold leading-tight tracking-tighter mb-6">
                    The <span class="gradient-text">Immutable</span><br>
                    Digital Foundation
                </h1>
                
                <p class="text-lg md:text-xl text-gray-300 mb-8 md:mb-10 leading-relaxed max-w-xl">
                    PITHOS delivers <strong class="text-white font-semibold">unshakeable security</strong> with permanently locked contract authority on Solana. <span class="text-cyan-400">Trust guaranteed</span>, yield optimized.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-10 md:mb-12">
                    <a href="#auth" onclick="showAuthForm('register')" class="btn-primary py-3 px-6 md:py-4 md:px-8 text-base md:text-lg group">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/> </svg>
                        <span>Get 1,000 FREE Tokens</span>
                    </a>
                    <a href="assets/docs/roadmap.pdf" target="_blank" class="btn-secondary py-3 px-6 md:py-4 md:px-8 text-base md:text-lg group">
                         <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/> </svg>
                        <span>View Whitepaper</span>
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="flex flex-col sm:flex-row gap-5">
                    <div class="flex items-center gap-3 p-3 bg-black/20 border border-white/10 rounded-lg group cursor-pointer transition-all hover:bg-black/30 hover:border-white/15">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/> </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Contract Immutable</div>
                            <div class="text-xs text-gray-400">Permanently Locked</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-black/20 border border-white/10 rounded-lg group cursor-pointer transition-all hover:bg-black/30 hover:border-white/15">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300">
                           <svg class="w-4 h-4 md:w-5 md:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/> </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-white text-sm">Authorities Revoked</div>
                            <div class="text-xs text-gray-400">Zero Rug Risk</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Auth Card -->
            <div id="auth" class="w-full lg:max-w-md mx-auto animate-fadeIn" style="animation-delay: 200ms;">
                <?php require_once(ROOT_DIR . '/views/auth_form.php'); ?>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 md:py-24 relative bg-black/10">
    <div class="container">
        <div class="text-center mb-12 md:mb-16 animate-fadeIn" style="animation-delay: 300ms;">
            <div class="inline-block mb-4 md:mb-5 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 backdrop-blur-sm shadow-md">
                <span class="text-cyan-300 text-sm font-semibold tracking-wide flex items-center gap-1.5">
                     <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Why Choose PITHOS
                </span>
            </div>
            <h2 class="font-heading text-4xl md:text-5xl font-bold mb-4 md:mb-5">
                Trust Built In. <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-purple-400">Yield</span> Optimized.
            </h2>
            <p class="text-base md:text-xl text-gray-400 max-w-3xl mx-auto">
                Experience the next generation of secure, immutable tokenomics on Solana.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
            <!-- Feature 1 -->
            <div class="glass-card p-6 md:p-8 group transition-all duration-300 hover:shadow-xl hover:shadow-emerald-900/40 hover:-translate-y-1 animate-fadeIn" style="animation-delay: 400ms;">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center mb-5 shadow-lg shadow-emerald-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-white"> <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.333 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751A11.959 11.959 0 0 1 12 2.964Z" /> </svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold font-heading mb-3 text-white">Zero Rug Risk</h3>
                <p class="text-gray-400 leading-relaxed text-sm mb-4">
                    Minting keys and administrative authority are permanently burned on-chain, guaranteeing the contract cannot be modified or exploited.
                </p>
                <div class="mt-4 pt-4 border-t border-[var(--border-color-light)]">
                    <span class="text-sm text-green-400 font-semibold flex items-center gap-1.5">
                       <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Verified On-Chain
                    </span>
                </div>
            </div>

            <!-- Feature 2 -->
             <div class="glass-card p-6 md:p-8 group transition-all duration-300 hover:shadow-xl hover:shadow-purple-900/40 hover:-translate-y-1 animate-fadeIn" style="animation-delay: 500ms;">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center mb-5 shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-white"> <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5m18 0v3.75A2.25 2.25 0 0 1 18.75 18H5.25A2.25 2.25 0 0 1 3 15.75v-3.75" /> </svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold font-heading mb-3 text-white">Passive APY Staking</h3>
                <p class="text-gray-400 leading-relaxed text-sm mb-4">
                    The Staking Vault offers high-yield passive income, rewarding long-term holders with collected protocol fees for sustained growth.
                </p>
                 <div class="mt-4 pt-4 border-t border-[var(--border-color-light)]">
                    <span class="text-sm text-purple-400 font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        Up to 25% APY
                    </span>
                </div>
            </div>

            <!-- Feature 3 -->
             <div class="glass-card p-6 md:p-8 group transition-all duration-300 hover:shadow-xl hover:shadow-orange-900/40 hover:-translate-y-1 animate-fadeIn" style="animation-delay: 600ms;">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-yellow-500 to-orange-600 flex items-center justify-center mb-5 shadow-lg shadow-orange-500/30 group-hover:scale-110 transition-transform duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7 text-white"> <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A9.06 9.06 0 0 1 6 18.719M12 12.75a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" /> </svg>
                </div>
                <h3 class="text-xl md:text-2xl font-bold font-heading mb-3 text-white">Community Rewards</h3>
                <p class="text-gray-400 leading-relaxed text-sm mb-4">
                    Instantly claim your bonus upon sign-up and earn substantial referral rewards for every new user you onboard.
                </p>
                 <div class="mt-4 pt-4 border-t border-[var(--border-color-light)]">
                    <span class="text-sm text-yellow-400 font-semibold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2h-8a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h4a1 1 0 100-2H8z"></path></svg>
                        <?php echo number_format(REFERRAL_BONUS); ?> GALAXY per referral
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-16 md:py-24 relative">
    <div class="container animate-fadeIn" style="animation-delay: 700ms;">
        <div class="glass-card p-8 md:p-12 border-2 border-purple-500/20 shadow-xl shadow-purple-900/30">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="font-heading text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-cyan-400 mb-2">1B</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider font-medium">Max Supply</div>
                </div>
                <div>
                    <div class="font-heading text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-400 to-emerald-500 mb-2">100%</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider font-medium">Immutable</div>
                </div>
                <div>
                    <div class="font-heading text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500 mb-2">25%</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider font-medium">Max APY</div>
                </div>
                <div>
                    <div class="font-heading text-4xl md:text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500 mb-2">0</div>
                    <div class="text-gray-400 text-xs md:text-sm uppercase tracking-wider font-medium">Rug Risk</div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Add animation style */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.6s ease-out forwards; opacity: 0; }
</style>
