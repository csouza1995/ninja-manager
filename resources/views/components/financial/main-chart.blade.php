<div class="card bg-base-200 border border-base-300 shadow-xl mb-12" wire:ignore x-data="{
    chartData: @entangle('chartData'),
    flowData: @entangle('flowChartData'),
    chartType: @entangle('chartType'),
    chart: null,
    init() {
        this.chart = new ApexCharts(this.$refs.mainChart, this.getOptions());
        this.chart.render();

        this.$watch('chartData', () => { this.chart.updateOptions(this.getOptions(), true, false); });
        this.$watch('flowData', () => { this.chart.updateOptions(this.getOptions(), true, false); });
        this.$watch('chartType', () => { this.chart.updateOptions(this.getOptions(), true, false); });
    },
    getOptions() {
        const fmt = (val) => 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const base = {
            chart: { type: 'bar', height: 350, stacked: false, toolbar: { show: false }, background: 'transparent', animations: { enabled: true } },
            plotOptions: { bar: { columnWidth: '60%', borderRadius: 4, dataLabels: { position: 'top' } } },
            dataLabels: { enabled: false },
            stroke: { show: true, width: 2, colors: ['transparent'] },
            yaxis: { labels: { formatter: fmt } },
            grid: { borderColor: 'rgba(255,255,255,0.05)' },
            tooltip: { shared: true, intersect: false, y: { formatter: fmt } },
            theme: { mode: 'dark' },
            legend: { show: true, position: 'top' }
        };

        if (this.chartType === 'comparison') {
            return {
                ...base,
                series: [
                    { name: 'Receitas', data: this.chartData.revenues },
                    { name: 'Despesas', data: this.chartData.expenses },
                ],
                colors: ['#22c55e', '#ef4444'],
                xaxis: { categories: this.chartData.labels },
                plotOptions: { bar: { columnWidth: '60%', borderRadius: 4, dataLabels: { position: 'top' }, colors: { ranges: [] } } },
            };
        }

        if (this.chartType === 'flow') {
            return {
                ...base,
                chart: { ...base.chart, stacked: true },
                series: [
                    { name: 'Entradas', data: this.flowData.inflows, group: 'entradas' },
                    { name: 'Despesas', data: this.flowData.expenses, group: 'saidas' },
                    { name: 'Impostos', data: this.flowData.taxes, group: 'saidas' },
                    { name: 'Retiradas', data: this.flowData.withdrawals, group: 'saidas' },
                ],
                colors: ['#22c55e', '#ef4444', '#f59e0b', '#8b5cf6'],
                xaxis: { categories: this.flowData.labels },
                plotOptions: { bar: { columnWidth: '55%', borderRadius: 4, dataLabels: { position: 'top' }, colors: { ranges: [] } } },
            };
        }

        // result (default)
        return {
            ...base,
            series: [{ name: 'Resultado', data: this.chartData.results }],
            colors: ['#22c55e'],
            xaxis: { categories: this.chartData.labels },
            plotOptions: {
                bar: {
                    columnWidth: '60%',
                    borderRadius: 4,
                    dataLabels: { position: 'top' },
                    colors: {
                        ranges: [
                            { from: -999999999, to: -0.01, color: '#ef4444' },
                            { from: 0, to: 999999999, color: '#22c55e' },
                        ],
                    },
                },
            },
        };
    },
}">
    <div class="card-body p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                <span
                    x-text="{
                    result: 'Resultado Operacional (Líquido - Despesas)',
                    comparison: 'Comparativo Receitas x Despesas',
                    flow: 'Fluxo Geral – Entradas vs Saídas',
                }[chartType]"></span>
            </h2>

            <div class="join border border-base-300 shadow-sm">
                <button wire:click="$set('chartType', 'result')" class="join-item btn btn-sm"
                    :class="{ 'btn-active btn-primary': chartType === 'result' }">
                    Resultado
                </button>
                <button wire:click="$set('chartType', 'comparison')" class="join-item btn btn-sm"
                    :class="{ 'btn-active btn-primary': chartType === 'comparison' }">
                    Rec x Desp
                </button>
                <button wire:click="$set('chartType', 'flow')" class="join-item btn btn-sm"
                    :class="{ 'btn-active btn-primary': chartType === 'flow' }">
                    Fluxo Geral
                </button>
            </div>
        </div>

        <div x-ref="mainChart"></div>
    </div>
</div>
