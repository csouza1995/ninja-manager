@props([
    'gridColsClass' => 'lg:grid-cols-3',
])

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8"
    :class="{ 'lg:grid-cols-3': count === 3, 'lg:grid-cols-5': count === 5, 'lg:grid-cols-7': count === 7 }" wire:ignore
    x-data="{
        pieData: @entangle('pieData'),
        count: @entangle('periodCount'),
        charts: [],
        init() {
            this.$nextTick(() => this.renderAll());
            this.$watch('pieData', () => {
                this.charts.forEach(c => c.destroy());
                this.charts = [];
                setTimeout(() => this.renderAll(), 150);
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
            this.pieData.forEach((d, index) => {
                const el = document.getElementById('pie-chart-' + index);
                if (!el || !d) return;
                el.innerHTML = '';
                const chart = new ApexCharts(el, this.buildOptions(d));
                chart.render();
                this.charts.push(chart);
            });
        },
    }">

    <template x-for="(data, index) in pieData" :key="index">
        <div class="card bg-base-200 shadow-xl border border-base-300 overflow-hidden">
            <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
                <span class="font-bold text-sm uppercase tracking-wider opacity-70" x-text="data?.label ?? ''"></span>
                <span class="text-[10px] font-bold uppercase opacity-40">Composição</span>
            </div>
            <div class="card-body p-4">
                <div :id="'pie-chart-' + index"></div>
            </div>
        </div>
    </template>
</div>
