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
        init() {
            let chart = new ApexCharts(this.$refs.mainChart, {
                series: [{
                    name: 'Resultado',
                    data: this.chartData.results
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: { show: false },
                    background: 'transparent'
                },
                plotOptions: {
                    bar: {
                        colors: {
                            ranges: [{
                                from: -999999999,
                                to: 0,
                                color: '#ef4444'
                            }, {
                                from: 0.1,
                                to: 999999999,
                                color: '#22c55e'
                            }]
                        },
                        columnWidth: '60%',
                        borderRadius: 4
                    }
                },
                dataLabels: { enabled: false },
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
                theme: { mode: 'dark' }
            });
            chart.render();
        }
    }">
        <div class="card-body p-6">
            <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                Resultado Operacional (Líquido - Despesas)
            </h2>
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

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-24">
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
</div>
