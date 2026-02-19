<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" wire:ignore x-data="{
    pieData: @entangle('pieData'),
    charts: [],
    init() {
        this.$nextTick(() => this.renderAll());
        this.$watch('pieData', () => {
            this.charts.forEach(c => c.destroy());
            this.charts = [];
            this.$nextTick(() => this.renderAll());
        });
    },
    buildOptions(d) {
        const total = d.expenses + d.taxes + d.withdrawals;
        const fmt = (val) => 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return {
            series: [d.expenses, d.taxes, d.withdrawals],
            labels: ['Despesas', 'Impostos', 'Retiradas'],
            colors: ['#ef4444', '#f59e0b', '#8b5cf6'],
            chart: {
                type: 'donut',
                height: 260,
                background: 'transparent',
                toolbar: { show: false },
                animations: { enabled: true },
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '62%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total',
                                color: 'rgba(255,255,255,0.5)',
                                formatter: () => fmt(total),
                            },
                            value: {
                                color: '#ffffff',
                                formatter: fmt,
                            },
                        },
                    },
                },
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val.toFixed(1) + '%',
                style: { fontSize: '11px', fontWeight: 'bold', colors: ['#fff'] },
                dropShadow: { enabled: false },
            },
            legend: { show: true, position: 'bottom', fontSize: '11px' },
            tooltip: { y: { formatter: fmt } },
            theme: { mode: 'dark' },
        };
    },
    renderAll() {
        [
            [this.$refs.pie0, this.pieData[0]],
            [this.$refs.pie1, this.pieData[1]],
            [this.$refs.pie2, this.pieData[2]],
        ].forEach(([el, d]) => {
            if (!el || !d) return;
            const chart = new ApexCharts(el, this.buildOptions(d));
            chart.render();
            this.charts.push(chart);
        });
    },
}">

    {{-- Card 0 --}}
    <div class="card bg-base-200 shadow-xl border border-base-300 overflow-hidden">
        <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
            <span class="font-bold text-sm uppercase tracking-wider opacity-70" x-text="pieData[0]?.label ?? ''"></span>
            <span class="text-[10px] font-bold uppercase opacity-40">Composição</span>
        </div>
        <div class="card-body p-4">
            <div x-ref="pie0"></div>
        </div>
    </div>

    {{-- Card 1 --}}
    <div class="card bg-base-200 shadow-xl border border-base-300 overflow-hidden">
        <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
            <span class="font-bold text-sm uppercase tracking-wider opacity-70" x-text="pieData[1]?.label ?? ''"></span>
            <span class="text-[10px] font-bold uppercase opacity-40">Composição</span>
        </div>
        <div class="card-body p-4">
            <div x-ref="pie1"></div>
        </div>
    </div>

    {{-- Card 2 --}}
    <div class="card bg-base-200 shadow-xl border border-base-300 overflow-hidden">
        <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
            <span class="font-bold text-sm uppercase tracking-wider opacity-70" x-text="pieData[2]?.label ?? ''"></span>
            <span class="text-[10px] font-bold uppercase opacity-40">Composição</span>
        </div>
        <div class="card-body p-4">
            <div x-ref="pie2"></div>
        </div>
    </div>

</div>
