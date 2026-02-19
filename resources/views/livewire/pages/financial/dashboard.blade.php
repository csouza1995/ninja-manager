<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary flex items-center gap-2">
            Painel Financeiro
        </h2>
        <p class="mt-1 text-sm text-base-content/60">Visão geral da saúde financeira e balanço de contas</p>
    </div>

    <!-- Balanço nos Bancos -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
        @foreach ($bankBalances as $bank)
            <div class="stats shadow bg-base-200 border border-base-300">
                <div class="stat p-4">
                    <div class="stat-title text-[10px] font-bold uppercase opacity-50">{{ $bank['name'] }}</div>
                    <div
                        class="stat-value text-xl font-mono {{ $bank['balance'] >= 0 ? 'text-success' : 'text-error' }}">
                        R$ {{ number_format($bank['balance'], 2, ',', '.') }}
                    </div>
                    <div class="flex flex-col gap-0.5 mt-2 pt-2 border-t border-base-300/50">
                        <div class="flex justify-between text-[11px] font-black opacity-80">
                            <span>SALDO BLOQUEADO:</span>
                            <span class="font-mono text-error">- R$
                                {{ number_format($bank['tax_provision'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-[11px] font-black">
                            <span class="text-primary/70">SALDO LIVRE:</span>
                            <span class="font-mono text-primary">R$
                                {{ number_format($bank['free_balance'], 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="stats shadow bg-primary/10 border-2 border-primary/20 text-base-content overflow-hidden">
            <div class="stat p-4">
                <div class="stat-title opacity-70 uppercase font-bold text-[10px]">Total Consolidado</div>
                <div class="stat-value text-2xl font-black text-primary">
                    R$ {{ number_format(collect($bankBalances)->sum('balance'), 2, ',', '.') }}
                </div>
                @php
                    $totalTax = collect($bankBalances)->sum('tax_provision');
                    $totalFree = collect($bankBalances)->sum('balance') - $totalTax;
                @endphp
                <div class="flex flex-col gap-0.5 mt-2 pt-2 border-t border-primary/10">
                    <div class="flex justify-between text-[11px] font-black opacity-80">
                        <span class="italic">TOTAL BLOQUEADO:</span>
                        <span class="font-mono text-error font-bold">- R$
                            {{ number_format($totalTax, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[11px] font-black">
                        <span class="text-primary uppercase">CAPITAL LIVRE:</span>
                        <span class="font-mono text-primary">R$ {{ number_format($totalFree, 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráfico Principal -->
    <div class="card bg-base-200 border border-base-300 shadow-xl mb-12" wire:ignore x-data="{
        chartData: @entangle('chartData'),
        chartType: @entangle('chartType'),
        chart: null,
        init() {
            this.chart = new ApexCharts(this.$refs.mainChart, this.getOptions());
            this.chart.render();
    
            this.$watch('chartData', () => {
                this.chart.updateOptions(this.getOptions(), true, false);
            });
    
            this.$watch('chartType', () => {
                this.chart.updateOptions(this.getOptions(), true, false);
            });
        },
        getOptions() {
            const isComparison = this.chartType === 'comparison';
    
            const baseOptions = {
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    background: 'transparent',
                    animations: { enabled: true }
                },
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                        borderRadius: 4,
                        dataLabels: { position: 'top' }
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: this.chartData.labels,
                },
                yaxis: {
                    labels: {
                        formatter: (val) => 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    }
                },
                grid: {
                    borderColor: 'rgba(255, 255, 255, 0.05)',
                },
                tooltip: {
                    y: {
                        formatter: (val) => 'R$ ' + val.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                    }
                },
                theme: { mode: 'dark' },
                legend: {
                    show: true,
                    position: 'top'
                }
            };
    
            if (isComparison) {
                return {
                    ...baseOptions,
                    series: [
                        { name: 'Receitas', data: this.chartData.revenues },
                        { name: 'Despesas', data: this.chartData.expenses }
                    ],
                    colors: ['#22c55e', '#ef4444'],
                    plotOptions: {
                        bar: {
                            columnWidth: '60%',
                            borderRadius: 4,
                            dataLabels: { position: 'top' },
                            colors: { ranges: [] }
                        }
                    },
                };
            }
    
            return {
                ...baseOptions,
                series: [{ name: 'Resultado', data: this.chartData.results }],
                colors: ['#22c55e'],
                plotOptions: {
                    bar: {
                        columnWidth: '60%',
                        borderRadius: 4,
                        dataLabels: { position: 'top' },
                        colors: {
                            ranges: [
                                { from: -999999999, to: -0.01, color: '#ef4444' },
                                { from: 0, to: 999999999, color: '#22c55e' }
                            ]
                        }
                    }
                },
            };
        }
    }">
        <div class="card-body p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <span class="w-2 h-6 bg-primary rounded-full"></span>
                    @if ($chartType === 'result')
                        Resultado Operacional (Líquido - Despesas)
                    @else
                        Comparativo Receitas x Despesas
                    @endif
                </h2>

                <div class="join border border-base-300 shadow-sm">
                    <button wire:click="$set('chartType', 'result')" @class([
                        'join-item btn btn-sm',
                        'btn-active btn-primary' => $chartType === 'result',
                    ])>
                        Resultado
                    </button>
                    <button wire:click="$set('chartType', 'comparison')" @class([
                        'join-item btn btn-sm',
                        'btn-active btn-primary' => $chartType === 'comparison',
                    ])>
                        Rec x Desp
                    </button>
                </div>
            </div>
            <div x-ref="mainChart"></div>
        </div>
    </div>

    <!-- Tabelas de Períodos -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <h2 class="text-2xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            Análise de Fluxo
        </h2>

        <div class="join border border-base-300 shadow-sm">
            <button wire:click="setFilter('month')" @class([
                'join-item btn btn-sm',
                'btn-primary' => $activeFilter === 'month',
            ])>Mensal</button>
            <button wire:click="setFilter('quarter')" @class([
                'join-item btn btn-sm',
                'btn-primary' => $activeFilter === 'quarter',
            ])>Trimestral</button>
            <button wire:click="setFilter('year')" @class([
                'join-item btn btn-sm',
                'btn-primary' => $activeFilter === 'year',
            ])>Anual</button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach ($periods as $label => $data)
            <div class="card bg-base-200 shadow-xl border border-base-300 overflow-hidden">
                <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
                    <span class="font-bold text-sm uppercase tracking-wider opacity-70">{{ $label }}</span>
                    <div class="flex gap-1">
                        <span
                            class="w-2 h-2 rounded-full {{ $data['final_balance_actual'] >= 0 ? 'bg-success' : 'bg-error' }}"></span>
                    </div>
                </div>
                <div class="card-body p-4 gap-3">
                    <!-- FATURAMENTO -->
                    <div>
                        <div
                            class="flex justify-between items-center bg-success/10 p-2 rounded-t-lg border-x border-t border-success/20">
                            <span class="text-[11px] font-black uppercase text-success">Faturamento</span>
                            <span class="font-mono text-success font-bold text-sm">R$
                                {{ number_format($data['billing_paid'] + $data['billing_pending'], 2, ',', '.') }}</span>
                        </div>
                        <div class="bg-base-300/20 rounded-b-lg border border-base-300/30 p-2 space-y-1">
                            <div class="flex justify-between items-center text-xs opacity-70">
                                <span>Realizado</span>
                                <span class="font-mono text-success font-bold">R$
                                    {{ number_format($data['billing_paid'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-xs opacity-50">
                                <span>A receber</span>
                                <span class="font-mono">R$
                                    {{ number_format($data['billing_pending'], 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- ENCARGOS FATURAMENTO -->
                    <div class="space-y-1 px-1">
                        <div class="flex justify-between items-center text-[10px] font-bold uppercase opacity-60">
                            <span>Encargos</span>
                            <span class="font-mono text-warning">- R$
                                {{ number_format($data['revenue_tax_paid'] + $data['revenue_tax_pending'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-60">
                            <span>Pagos</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['revenue_tax_paid'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-40">
                            <span>A pagar</span></span>
                            <span class="font-mono">- R$
                                {{ number_format($data['revenue_tax_pending'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- DESPESAS -->
                    <div class="space-y-1 px-1">
                        <div class="flex justify-between items-center text-[10px] font-bold uppercase opacity-60">
                            <span>Despesas</span>
                            <span class="font-mono text-error">- R$
                                {{ number_format($data['expenses_paid'] + $data['expenses_pending'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-60">
                            <span>Pagas</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['expenses_paid'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-40">
                            <span>Em Aberto</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['expenses_pending'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- SALDO PARCIAL -->
                    <div class="bg-primary/5 rounded-lg border border-primary/10 p-2 space-y-1">
                        <div class="text-[10px] font-black uppercase text-primary mb-1">Saldo Parcial</div>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="opacity-70">Atual</span>
                            <span
                                class="font-mono {{ $data['partial_balance_actual'] >= 0 ? 'text-primary' : 'text-error' }}">
                                R$ {{ number_format($data['partial_balance_actual'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center text-xs opacity-50">
                            <span>Previsto</span>
                            <span class="font-mono italic">
                                R$ {{ number_format($data['partial_balance_predicted'], 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- RETIRADAS -->
                    <div class="space-y-1 px-1 mt-1">
                        <div class="flex justify-between items-center text-[10px] font-bold uppercase opacity-60">
                            <span>Retiradas</span>
                            <span class="font-mono text-error">- R$
                                {{ number_format($data['outflows_paid'] + $data['outflows_pending'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-60">
                            <span>Realizada</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['outflows_paid'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-40">
                            <span>Em Aberto</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['outflows_pending'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- ENCARGOS RETIRADAS -->
                    <div class="space-y-1 px-1">
                        <div class="flex justify-between items-center text-[10px] font-bold uppercase opacity-60">
                            <span>Encargos</span>
                            <span class="font-mono text-warning">- R$
                                {{ number_format($data['outflow_tax_paid'] + $data['outflow_tax_pending'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-60">
                            <span>Pagos</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['outflow_tax_paid'], 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-[11px] opacity-40">
                            <span>A pagar</span>
                            <span class="font-mono">- R$
                                {{ number_format($data['outflow_tax_pending'], 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="divider my-0 mt-1"></div>

                    <!-- SALDO FINAL -->
                    <div class="bg-base-300 rounded-lg p-3 space-y-2 border border-base-content/10 shadow-inner">
                        <div class="text-[10px] font-black uppercase opacity-40 mb-1">Saldo Final</div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold opacity-70">Atual</span>
                            <span
                                class="font-mono text-xl font-black {{ $data['final_balance_actual'] >= 0 ? 'text-success' : 'text-error' }}">
                                R$ {{ number_format($data['final_balance_actual'], 2, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-base-content/5">
                            <span class="text-xs opacity-50">Previsto</span>
                            <span
                                class="font-mono text-sm opacity-60 {{ $data['final_balance_predicted'] >= 0 ? 'text-success' : 'text-error' }}">
                                R$ {{ number_format($data['final_balance_predicted'], 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Retiradas de Sócios & Colaboradores -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold flex items-center gap-2 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
            Retiradas de Sócios & Colaboradores
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-24">
            @foreach ($periods as $label => $data)
                <div class="card bg-base-200 shadow-xl border border-base-300 overflow-hidden">
                    <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
                        <span class="font-bold text-sm uppercase tracking-wider opacity-70">{{ $label }}</span>
                        <div class="badge badge-sm badge-ghost">Total: R$
                            {{ number_format($data['withdrawals_breakdown']->sum('amount'), 2, ',', '.') }}</div>
                    </div>
                    <div class="card-body p-0">
                        @if (count($data['withdrawals_breakdown']) > 0)
                            <table class="table table-sm w-full">
                                <tbody>
                                    @foreach ($data['withdrawals_breakdown'] as $item)
                                        <tr class="hover">
                                            <td class="text-xs font-bold opacity-80 pl-6">{{ $item['name'] }}</td>
                                            <td class="text-right font-mono text-xs pr-6">R$
                                                {{ number_format($item['amount'], 2, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="p-8 text-center text-xs opacity-40 italic">
                                Nenhuma retirada registrada.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
