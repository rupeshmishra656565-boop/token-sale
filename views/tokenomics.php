<?php
$max_supply = 1000000000; 
$token_symbol = "PITH"; 

$distribution = [
    'Public Sale' => ['percentage' => 40, 'color' => '#a855f7', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>', 'description' => 'Tokens allocated for public offering phases.'],
    'Staking Rewards' => ['percentage' => 25, 'color' => '#14F195', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a2.25 2.25 0 0 1-2.25 2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 1 3 12m18 0v-1.5a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 00-6 0H5.25A2.25 2.25 0 003 10.5v1.5m18 0v3.75A2.25 2.25 0 0 1 18.75 18H5.25A2.25 2.25 0 0 1 3 15.75v-3.75"></path></svg>', 'description' => 'Reserved for rewarding users who stake their tokens.'],
    'Team & Advisors' => ['percentage' => 15, 'color' => '#3b82f6', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>', 'description' => 'Allocated to the core team and advisors, typically vested.'],
    'Marketing & Partnerships' => ['percentage' => 10, 'color' => '#f59e0b', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>', 'description' => 'Funds for promoting the project and forming strategic alliances.'],
    'Ecosystem Fund' => ['percentage' => 10, 'color' => '#6366f1', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>', 'description' => 'Dedicated to fostering growth, development, and community initiatives.'],
];

$chart_labels = json_encode(array_keys($distribution));
$chart_data = json_encode(array_column($distribution, 'percentage'));
$chart_colors = array_column($distribution, 'color'); 
?>
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="glass-card p-6 md:p-10 mb-10 md:mb-12 relative overflow-hidden animate-fadeIn" style="animation-delay: 100ms;">
        <div class="absolute top-0 left-0 w-72 h-72 bg-gradient-to-br from-purple-600/30 to-transparent rounded-full blur-3xl opacity-50 -translate-x-1/4 -translate-y-1/4"></div>
        <div class="relative z-10 text-center md:text-left">
            <h1 class="text-4xl md:text-5xl font-bold font-heading mb-3">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 via-cyan-400 to-secondary">Tokenomics</span>
            </h1>
            <p class="text-lg md:text-xl text-gray-400 max-w-3xl mx-auto md:mx-0">
                Understanding the distribution and utility of the <?php echo $token_symbol; ?> token.
            </p>
        </div>
    </div>

    <section class="mb-10 md:mb-16 animate-fadeIn" style="animation-delay: 200ms;">
         <h2 class="text-2xl md:text-3xl font-bold font-heading mb-6 text-center md:text-left">Key Metrics</h2>
         <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 md:gap-8">
             <div class="glass-card p-6 border-l-4 border-primary">
                 <div class="flex items-center gap-3 mb-2">
                     <div class="p-2 bg-primary/20 rounded-lg text-primary-light">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M13 7a4 4 0 11-8 0 4 4 0 018 0zM5 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                     </div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Total Supply</span>
                 </div>
                <div class="text-3xl font-bold text-white font-heading"><?php echo number_format($max_supply / 1000000000, 1); ?> Billion</div>
                <div class="text-sm text-gray-500 mt-1"><?php echo $token_symbol; ?></div>
             </div>
              <div class="glass-card p-6 border-l-4 border-secondary">
                 <div class="flex items-center gap-3 mb-2">
                     <div class="p-2 bg-secondary/20 rounded-lg text-secondary">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                     </div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Token Symbol</span>
                 </div>
                <div class="text-3xl font-bold text-white font-heading"><?php echo $token_symbol; ?></div>
                 <div class="text-sm text-gray-500 mt-1">Utility & Governance</div>
             </div>
             <div class="glass-card p-6 border-l-4 border-cyan-500">
                 <div class="flex items-center gap-3 mb-2">
                     <div class="p-2 bg-cyan-500/20 rounded-lg text-cyan-400">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"></path></svg>
                     </div>
                    <span class="text-xs text-gray-400 uppercase tracking-wider font-medium">Contract Address</span>
                 </div>
                <div class="text-lg font-mono text-cyan-400 break-all">Solana: Coming Soon</div>
                 <div class="text-sm text-gray-500 mt-1">Immutable & Verified</div>
             </div>
         </div>
    </section>

    <section class="animate-fadeIn" style="animation-delay: 300ms;">
        <h2 class="text-2xl md:text-3xl font-bold font-heading mb-8 text-center md:text-left">Token Distribution</h2>
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 md:gap-12 items-center">
            
            <div class="lg:col-span-2 flex justify-center">
                 <div class="w-full max-w-xs sm:max-w-sm aspect-square relative">
                    <canvas id="tokenomicsChart"></canvas>
                    <div id="chartCenterText" class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none transition-opacity duration-300">
                         <div class="text-center">
                              <div class="font-heading text-4xl font-bold text-white"><?php echo number_format($max_supply / 1000000000, 1); ?>B</div>
                              <div class="text-sm text-gray-400 mt-1">Total Supply</div>
                         </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3">
                <div class="space-y-4">
                    <?php 
                    $delay_increment = 80; 
                    $current_delay = 400; 
                    foreach ($distribution as $name => $details): 
                        $amount = number_format(($max_supply * $details['percentage']) / 100);
                        $color_style = $details['color']; 
                    ?>
                    <div class="glass-card p-4 hover:border-[var(--border-color)] transition-all duration-300 animate-fadeIn flex items-center gap-4" style="animation-delay: <?php echo $current_delay; ?>ms;">
                         <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg" style="background: linear-gradient(135deg, <?php echo $color_style; ?>30, <?php echo $color_style; ?>10);">
                             <span style="color: <?php echo $color_style; ?>;"><?php echo $details['icon']; ?></span>
                        </div>
                        <div class="flex-grow">
                             <div class="flex justify-between items-baseline mb-1">
                                <h4 class="font-semibold text-white text-base md:text-lg"><?php echo $name; ?></h4>
                                 <div class="font-bold text-lg md:text-xl font-heading" style="color: <?php echo $color_style; ?>;"><?php echo $details['percentage']; ?>%</div>
                            </div>
                            <p class="text-xs text-gray-400"><?php echo $details['description']; ?></p>
                            <p class="text-xs text-gray-500 font-mono mt-1 text-right"><?php echo $amount; ?> <?php echo $token_symbol; ?></p>
                        </div>
                        
                    </div>
                    <?php 
                        $current_delay += $delay_increment; 
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </section>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinycolor/1.6.0/tinycolor.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('tokenomicsChart');
    if (ctx) {
        const chartLabels = <?php echo $chart_labels; ?>;
        const chartData = <?php echo $chart_data; ?>;
        const baseColors = <?php echo json_encode($chart_colors); ?>;
        const chartCenterTextEl = document.getElementById('chartCenterText');

        // Check if tinycolor is loaded
        if (typeof tinycolor === 'undefined') {
            console.error('tinycolor.js library is not loaded.');
            return;
        }
        
        function createGradient(chart, color) {
            const { ctx, chartArea } = chart;
            if (!chartArea) {
                return color; 
            }
            const gradient = ctx.createLinearGradient(chartArea.left, chartArea.bottom, chartArea.right, chartArea.top);
            const colorEnd = tinycolor(color).lighten(15).toHexString(); 
            gradient.addColorStop(0.1, color); 
            gradient.addColorStop(0.9, colorEnd); 
            return gradient;
        }
        
        const backgroundGradients = (context) => {
             const chart = context.chart;
             if (!chart || !chart.chartArea) {
                 return baseColors; // Fallback to solid colors if chart is not ready
             }
             return baseColors.map(color => createGradient(chart, color));
        };

        const hoverCenterTextPlugin = {
            id: 'hoverCenterText',
            afterEvent: (chart, event, options) => {
                 const type = event.event.type;
                 if (type === 'mousemove' || type === 'touchstart' || type === 'touchmove') {
                    const points = chart.getElementsAtEventForMode(event.event, 'nearest', { intersect: true }, true);
                    if (points.length) {
                        const firstPoint = points[0];
                        const label = chart.data.labels[firstPoint.index];
                        const value = chart.data.datasets[0].data[firstPoint.index];
                        chartCenterTextEl.innerHTML = `
                            <div class="text-center transition-opacity duration-200">
                                 <div class="font-heading text-3xl font-bold text-white">${value}%</div>
                                 <div class="text-sm text-gray-400 mt-1">${label}</div>
                            </div>`;
                            chartCenterTextEl.style.opacity = '1';
                    } else if (chartCenterTextEl.innerHTML.includes('%')) { 
                         chartCenterTextEl.innerHTML = `
                            <div class="text-center transition-opacity duration-200">
                                 <div class="font-heading text-4xl font-bold text-white"><?php echo number_format($max_supply / 1000000000, 1); ?>B</div>
                                 <div class="text-sm text-gray-400 mt-1">Total Supply</div>
                            </div>`;
                         chartCenterTextEl.style.opacity = '1';
                    }
                } else if (type === 'mouseout') {
                     chartCenterTextEl.innerHTML = `
                        <div class="text-center transition-opacity duration-200">
                             <div class="font-heading text-4xl font-bold text-white"><?php echo number_format($max_supply / 1000000000, 1); ?>B</div>
                             <div class="text-sm text-gray-400 mt-1">Total Supply</div>
                        </div>`;
                     chartCenterTextEl.style.opacity = '1';
                }
            }
        };
        
        Chart.register(ChartDataLabels);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Token Distribution',
                    data: chartData,
                    backgroundColor: backgroundGradients,
                    borderColor: 'rgba(10, 10, 15, 0.6)', 
                    borderWidth: 2, 
                    spacing: 6, 
                    hoverOffset: 15, 
                    hoverBorderColor: 'rgba(255, 255, 255, 0.8)', 
                    hoverBorderWidth: 1,
                    borderRadius: 8, 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '75%', 
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true, 
                        backgroundColor: 'rgba(10, 10, 15, 0.9)', 
                        borderColor: 'var(--border-color)',
                        borderWidth: 1,
                        titleFont: { size: 14, weight: 'bold', family: 'var(--font-heading)' },
                        bodyFont: { size: 12, family: 'var(--font-body)' },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true, 
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed !== null) { label += context.parsed + '%'; }
                                const total = <?php echo $max_supply; ?>;
                                const amount = (total * context.parsed) / 100;
                                const formattedAmount = amount.toLocaleString('en-US', { maximumFractionDigits: 0 });
                                return [label, `(${formattedAmount} ${'<?php echo $token_symbol; ?>'})`]; 
                            }
                        }
                    },
                     datalabels: { display: false } 
                },
                 animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1500, 
                    easing: 'easeInOutQuart' 
                },
                 interaction: {
                    mode: 'nearest',
                    intersect: true,
                },
                 layout: { padding: 10 }
            },
            plugins: [hoverCenterTextPlugin] 
        });
        
    } else {
        console.error("Canvas element #tokenomicsChart not found.");
    }
});
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
    
    #tokenomicsChart { max-width: 100%; height: auto; } 
</style>

