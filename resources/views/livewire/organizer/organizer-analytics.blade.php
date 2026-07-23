<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="lg" level="2">{{ __('Organizer Analytics & Performance') }}</flux:heading>
            <flux:subheading>{{ __('Real-time ticket sales, participant growth, and monthly event metrics.') }}</flux:subheading>
        </div>
    </div>

    {{-- Top Row: Adaptive Stat Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Stat Card 1: Ticket Sales / Revenue --}}
        <div class="relative overflow-hidden p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white shadow-xs dark:shadow-xl flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Tickets Sold') }}</p>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">{{ number_format($totalTicketsSold) }}</span>
                    @if ($ticketsGrowth >= 0)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/50 px-2.5 py-0.5 rounded-full">
                            <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            {{ $ticketsGrowth }}% {{ __('increase') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800/50 px-2.5 py-0.5 rounded-full">
                            <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                            {{ abs($ticketsGrowth) }}% {{ __('decrease') }}
                        </span>
                    @endif
                </div>
            </div>
            {{-- Sparkline SVG --}}
            <div class="w-full pt-2">
                <svg class="w-full h-8 text-emerald-500/80" viewBox="0 0 100 25" fill="none" preserveAspectRatio="none">
                    <path d="M0 20 Q 25 15, 50 18 T 100 5 L 100 25 L 0 25 Z" fill="currentColor" fill-opacity="0.15"/>
                    <path d="M0 20 Q 25 15, 50 18 T 100 5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        {{-- Stat Card 2: Registered Participants --}}
        <div class="relative overflow-hidden p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white shadow-xs dark:shadow-xl flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Participants Joined') }}</p>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">{{ number_format($totalAttendees) }}</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/50 px-2.5 py-0.5 rounded-full">
                        <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        {{ $ticketsGrowth >= 0 ? $ticketsGrowth : 0 }}% {{ __('active') }}
                    </span>
                </div>
            </div>
            {{-- Sparkline SVG --}}
            <div class="w-full pt-2">
                <svg class="w-full h-8 text-purple-500/80" viewBox="0 0 100 25" fill="none" preserveAspectRatio="none">
                    <path d="M0 22 Q 25 20, 50 12 T 100 8 L 100 25 L 0 25 Z" fill="currentColor" fill-opacity="0.15"/>
                    <path d="M0 22 Q 25 20, 50 12 T 100 8" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>

        {{-- Stat Card 3: Events Hosted --}}
        <div class="relative overflow-hidden p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white shadow-xs dark:shadow-xl flex flex-col justify-between space-y-4">
            <div class="space-y-1">
                <p class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">{{ __('Events Published') }}</p>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white">{{ number_format($totalEvents) }}</span>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800/50 px-2.5 py-0.5 rounded-full">
                        {{ __('Active') }}
                    </span>
                </div>
            </div>
            {{-- Sparkline SVG --}}
            <div class="w-full pt-2">
                <svg class="w-full h-8 text-indigo-500/80" viewBox="0 0 100 25" fill="none" preserveAspectRatio="none">
                    <path d="M0 18 Q 25 10, 50 15 T 100 4 L 100 25 L 0 25 Z" fill="currentColor" fill-opacity="0.15"/>
                    <path d="M0 18 Q 25 10, 50 15 T 100 4" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Bottom Row: Adaptive Interactive Analytics Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6" x-data="{
        salesData: @js($monthlySales),
        attendeesData: @js($monthlyAttendees),
        labels: @js($monthLabels),
        salesChartInstance: null,
        attendeesChartInstance: null,
        initCharts() {
            if (typeof ApexCharts === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
                script.onload = () => {
                    this.renderApex();
                    this.observeDarkMode();
                };
                document.head.appendChild(script);
            } else {
                this.renderApex();
                this.observeDarkMode();
            }
        },
        observeDarkMode() {
            const observer = new MutationObserver(() => {
                this.renderApex();
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        },
        renderApex() {
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#a1a1aa' : '#52525b';
            const gridColor = isDark ? '#27272a' : '#e4e4e7';
            const themeMode = isDark ? 'dark' : 'light';

            if (this.salesChartInstance) this.salesChartInstance.destroy();
            if (this.attendeesChartInstance) this.attendeesChartInstance.destroy();

            // Line / Area Chart for Ticket Sales
            this.salesChartInstance = new ApexCharts(this.$refs.salesChart, {
                chart: {
                    type: 'area',
                    height: 310,
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                theme: { mode: themeMode },
                colors: ['#eab308'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.45,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                series: [{ name: 'Tickets Sold', data: this.salesData }],
                xaxis: {
                    categories: this.labels,
                    labels: { style: { colors: textColor } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { colors: textColor } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { theme: themeMode }
            });
            this.salesChartInstance.render();

            // Bar / Column Chart for Participants Joined
            this.attendeesChartInstance = new ApexCharts(this.$refs.attendeesChart, {
                chart: {
                    type: 'bar',
                    height: 310,
                    toolbar: { show: false },
                    background: 'transparent',
                    fontFamily: 'Inter, sans-serif'
                },
                theme: { mode: themeMode },
                colors: ['#eab308'],
                plotOptions: {
                    bar: {
                        columnWidth: '55%',
                        borderRadius: 4
                    }
                },
                dataLabels: { enabled: false },
                series: [{ name: 'Participants Joined', data: this.attendeesData }],
                xaxis: {
                    categories: this.labels,
                    labels: { style: { colors: textColor } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { colors: textColor } } },
                grid: { borderColor: gridColor, strokeDashArray: 4 },
                tooltip: { theme: themeMode }
            });
            this.attendeesChartInstance.render();
        }
    }" x-init="initCharts()">
        {{-- Chart Card 1: Ticket Sales Trend --}}
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white shadow-xs dark:shadow-xl space-y-4">
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Ticket Sales Trend') }}</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Monthly tickets issued throughout the current year') }}</p>
            </div>
            <div x-ref="salesChart" class="w-full min-h-[300px]"></div>
        </div>

        {{-- Chart Card 2: Participants Per Month --}}
        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-white shadow-xs dark:shadow-xl space-y-4">
            <div>
                <h3 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Participants Per Month') }}</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Unique attendees registered per calendar month') }}</p>
            </div>
            <div x-ref="attendeesChart" class="w-full min-h-[300px]"></div>
        </div>
    </div>
</div>
