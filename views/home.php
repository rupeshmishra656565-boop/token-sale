<?php
// Premium Home Page View - ENHANCED UI
// Includes views/auth_form.php
?>
<section class="relative pt-24 pb-16 md:pt-32 md:pb-24 overflow-hidden">
    <div class="absolute inset-0 z-0 opacity-30">
        <div class="absolute top-[-10%] left-[-10%] w-[500px] h-[500px] bg-gradient-to-br from-purple-600/50 to-transparent rounded-full blur-[120px]"></div>
        <div class="absolute bottom-[-15%] right-[-15%] w-[400px] h-[400px] bg-gradient-to-tl from-cyan-600/40 to-transparent rounded-full blur-[100px]"></div>
    </div>
    
    <div class="container relative z-10">
        <div class="grid lg:grid-cols-2 gap-10 md:gap-16 items-center">
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
                    <?php echo TOKEN_NAME; ?> delivers <strong class="text-white font-semibold">unshakeable security</strong> with permanently locked contract authority on Solana. <span class="text-cyan-400">Trust guaranteed</span>, yield optimized.
                </p>

                <div class="flex flex-col sm:flex-row flex-wrap gap-4 mb-10 md:mb-12">
                    <a href="#auth" onclick="showAuthForm('register')" class="btn-primary py-3 px-6 md:py-3.5 md:px-7 text-base group">
                        <svg class="w-5 h-5 group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/> </svg>
                        <span>Get <?php echo number_format(KYC_BONUS, 0); ?> FREE Tokens</span>
                    </a>
                    
                    <a href="assets/PITHOS_Protocol_Whitepaper.pdf" download="PITHOS_Protocol_Whitepaper.pdf" target="_blank" class="btn-secondary py-3 px-6 md:py-3.5 md:px-7 text-base group">
                         <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/> </svg>
                        <span>View Whitepaper</span>
                    </a>
                    
                    <a href="index.php?p=tokenomics" class="btn-secondary py-3 px-6 md:py-3.5 md:px-7 text-base group"> 
                         <svg class="w-5 h-5 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        <span>Tokenomics</span>
                    </a>
                </div>

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

            <div id="auth" class="w-full lg:max-w-md mx-auto animate-fadeIn" style="animation-delay: 200ms;">
                <?php require_once(ROOT_DIR . '/views/auth_form.php'); ?>
            </div>
        </div>
    </div>
</section>

<section class="py-16 md:py-24 relative bg-black/10">
    <div class="container">
        <div class="text-center mb-12 md:mb-16 animate-fadeIn" style="animation-delay: 300ms;">
            <div class="inline-block mb-4 md:mb-5 px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/30 backdrop-blur-sm shadow-md">
                <span class="text-cyan-300 text-sm font-semibold tracking-wide flex items-center gap-1.5">
                     <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    Why Choose <?php echo TOKEN_NAME; ?>
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
                        <?php echo number_format(REFERRAL_BONUS, 0); ?> <?php echo TOKEN_SYMBOL; ?> per referral
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Glass card wrapper (keeps parity with your theme) */
.backed-list.glass-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
  border: 1px solid rgba(255,255,255,0.06);
  backdrop-filter: blur(8px);
  box-shadow: 0 6px 30px rgba(10, 10, 12, 0.5);
  border-radius: 18px;
}

/* Exchange pill (icon + text) */
.exchange-pill {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 12px;
  text-decoration: none;
  transition: all 180ms ease;
  min-width: 210px;
  color: inherit;
}

/* Keep colors / logos intact — no color manipulation */
.exchange-pill .exchange-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 10px;
  overflow: hidden;
  flex-shrink: 0;
  background: rgba(255,255,255,0.02); /* soft base so very light logos read well on dark bg */
  box-shadow: 0 4px 14px rgba(2,6,23,0.6);
}

/* Logo sizing: small, sharp, consistent */
.exchange-pill .exchange-icon img {
  width: 28px;
  height: 28px;
  object-fit: contain;
  display: block;
}

/* Text block */
.exchange-text {
  display: flex;
  flex-direction: column;
  line-height: 1;
}

/* Premium typography for the exchange name */
.exchange-title {
  font-weight: 700;
  font-size: 15px;
  color: #ffffff;
  letter-spacing: 0.2px;
}

/* Subtext — small, muted, profession look */
.exchange-sub {
  font-size: 12px;
  color: rgba(255,255,255,0.65);
  margin-top: 3px;
  font-weight: 600;
}

/* Hover: subtle soft background for clickable affordance — NO color changes to the logos */
.exchange-pill:hover {
  background: linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.02));
  cursor: pointer;
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

/* Responsiveness: stack tight on small screens */
@media (max-width: 640px) {
  .exchange-pill {
    min-width: 100%;
    padding: 12px;
  }
  .exchange-pill .exchange-icon {
    width: 44px;
    height: 44px;
  }
  .exchange-pill .exchange-icon img {
    width: 32px;
    height: 32px;
  }
}
</style>
<section class="py-16 md:py-20">
  <div class="max-w-4xl mx-auto px-6 animate-fadeIn" style="animation-delay: 700ms;">
    <h2 class="font-heading text-3xl md:text-4xl font-extrabold text-white text-center mb-8">
      Trusted &amp; Supported By
    </h2>

    <div class="backed-list glass-card p-6 md:p-8 rounded-2xl">
      <div class="flex flex-col sm:flex-row sm:justify-center sm:gap-6 gap-4">
        
        <a href="https//www.binance.com" target="_blank" rel="noopener noreferrer" class="exchange-pill" title="Binance">
          <span class="exchange-icon">
            <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/270.png" alt="Binance" loading="lazy">
          </span>
          <span class="exchange-text">
            <span class="exchange-title">Binance</span>
            <span class="exchange-sub">Global Exchange</span>
          </span>
        </a>

        <a href="https//www.coinbase.com" target="_blank" rel="noopener noreferrer" class="exchange-pill" title="Coinbase">
          <span class="exchange-icon">
            <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/89.png" alt="Coinbase" loading="lazy">
          </span>
          <span class="exchange-text">
            <span class="exchange-title">Coinbase</span>
            <span class="exchange-sub">NASDAQ Listed</span>
          </span>
        </a>

        <a href="https//www.kraken.com" target="_blank" rel="noopener noreferrer" class="exchange-pill" title="Kraken">
          <span class="exchange-icon">
            <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/24.png" alt="Kraken" loading="lazy">
          </span>
          <span class="exchange-text">
            <span class="exchange-title">Kraken</span>
            <span class="exchange-sub">Trusted Since 2011</span>
          </span>
        </a>

        <a href="https//www.okx.com" target="_blank" rel="noopener noreferrer" class="exchange-pill" title="OKX">
          <span class="exchange-icon">
            <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/294.png" alt="OKX" loading="lazy">
          </span>
          <span class="exchange-text">
            <span class="exchange-title">OKX</span>
            <span class="exchange-sub">Global Liquidity</span>
          </span>
        </a>

        <a href="https//www.kucoin.com" target="_blank" rel="noopener noreferrer" class="exchange-pill" title="KuCoin">
          <span class="exchange-icon">
            <img src="https://s2.coinmarketcap.com/static/img/exchanges/64x64/302.png" alt="KuCoin" loading="lazy">
          </span>
          <span class="exchange-text">
            <span class="exchange-title">KuCoin</span>
            <span class="exchange-sub">Top Token Listings</span>
          </span>
        </a>

      </div>
    </div>
  </div>
</section>


<section class="py-16 md:py-24 relative bg-black/10">
    <div class="container animate-fadeIn" style="animation-delay: 800ms;">
        <div class="glass-card p-8 md:p-12 grid md:grid-cols-2 gap-10 md:gap-16 items-center">
            <div>
                 <div class="inline-block mb-4 md:mb-5 px-4 py-1.5 rounded-full bg-purple-500/10 border border-purple-500/30 backdrop-blur-sm shadow-md">
                    <span class="text-purple-300 text-sm font-semibold tracking-wide flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Experienced Builders
                    </span>
                </div>
                <h2 class="font-heading text-3xl md:text-4xl font-bold mb-4 md:mb-5">
                    From a Proven Team
                </h2>
                <p class="text-base md:text-lg text-gray-400 leading-relaxed">
                    The <?php echo TOKEN_NAME; ?> Protocol team comprises seasoned developers and strategists with a track record of launching successful blockchain projects. We bring expertise and a commitment to long-term value.
                </p>
            </div>
            <div class="flex justify-center items-center opacity-40">
                 <svg class="w-40 h-40 md:w-56 md:h-56 text-purple-400 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.539 1.118l-3.975-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
            </div>
        </div>
    </div>
</section>

<section class="py-16 md:py-24 relative">
    <div class="container animate-fadeIn" style="animation-delay: 900ms;">
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