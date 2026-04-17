<div x-data="{ count: @entangle('periodCount') }" x-init="$watch('count', v => $dispatch('ultrawide', v === 7));
setTimeout(() => $dispatch('ultrawide', count === 7), 50)">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary flex items-center gap-2">
            Painel Financeiro
        </h2>
        <p class="mt-1 text-sm text-base-content/60">Visão geral da saúde financeira e balanço de contas</p>
    </div>

    <!-- Balanço nos Bancos -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-12">
        @foreach ($bankBalances as $bank)
            <div class="stats shadow bg-base-200 border border-base-300 overflow-visible z-10 hover:z-50 transition-all">
                <div class="stat p-4 overflow-visible">
                    <div class="stat-title text-[10px] font-bold uppercase opacity-50 flex justify-between items-center">
                        {{ $bank['name'] }}
                        <a href="{{ route('financial.bank-accounts.statement', $bank['id']) }}"
                            class="btn btn-ghost btn-xs btn-square text-info -mt-1 -mr-1" title="Ver Extrato"
                            wire:navigate>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-3.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </a>
                    </div>
                    <div
                        class="stat-value text-xl font-mono {{ $bank['balance'] >= 0 ? 'text-success' : 'text-error' }}">
                        R$ {{ number_format($bank['balance'], 2, ',', '.') }}
                    </div>
                    <div class="flex flex-col gap-0.5 mt-2 pt-2 border-t border-base-300/50">
                        <div class="flex justify-between text-[11px] font-black opacity-80 overflow-visible">
                            <div class="dropdown dropdown-hover dropdown-bottom dropdown-center">
                                <label tabindex="0" class="cursor-help flex items-center gap-1">
                                    <span>SALDO BLOQUEADO:</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2.5" stroke="currentColor" class="w-3 h-3 text-info">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                    </svg>
                                </label>
                                <div tabindex="0"
                                    class="dropdown-content z-[50] card card-compact w-72 p-2 shadow-2xl bg-base-300 text-base-content border border-primary/20">
                                    <div class="card-body">
                                        <h3
                                            class="font-bold text-[10px] uppercase opacity-60 border-b border-base-content/10 pb-1 mb-1">
                                            Resumo de Impostos (Dívida)</h3>
                                        <div class="flex justify-between text-[10px] items-center">
                                            <span>DAS Acumulado:</span>
                                            <span class="font-mono">R$
                                                {{ number_format($bank['tax_provision_details']['das_accrued'], 2, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-[10px] items-center mt-0.5">
                                            <span>GPS / INSS Acumulado:</span>
                                            <span class="font-mono">R$
                                                {{ number_format($bank['tax_provision_details']['inss_accrued'], 2, ',', '.') }}</span>
                                        </div>
                                        <div
                                            class="flex justify-between text-[10px] text-success border-t border-base-content/10 mt-1.5 pt-1.5 font-bold items-center">
                                            <span>Tributos Já Pagos:</span>
                                            <span class="font-mono">- R$
                                                {{ number_format($bank['tax_provision_details']['taxes_paid'], 2, ',', '.') }}</span>
                                        </div>
                                        <p class="text-[9px] mt-2 opacity-50 leading-tight italic">O valor "Bloqueado" representa sua dívida fiscal total (Faturado + Recebido + Prolabores) menos o que você já pagou.</p>
                                    </div>
                                </div>
                            </div>
                            <span class="font-mono text-error">
                                R$ {{ number_format($bank['tax_provision'], 2, ',', '.') }}
                            </span>
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

        <div class="stats shadow bg-primary/10 border-2 border-primary/20 text-base-content">
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


    <!-- Controles de Período Fixados (Sticky) -->
    <div
        class="sticky top-[65px] z-40 bg-base-100/90 backdrop-blur pb-4 pt-4 -mx-4 px-4 border-b border-base-200 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 shadow-sm">

        {{-- Navigation Controls (Left) --}}
        <div class="join shadow-sm">
            <button wire:click="previousPeriod" class="join-item btn btn-sm bg-base-100 hover:bg-base-200"
                title="Período Anterior">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
                Anterior
            </button>
            <div
                class="join-item btn btn-sm px-4 bg-base-100 border-base-300 pointer-events-none font-bold text-primary">
                {{ $currentPeriodLabel }}
            </div>
            <button wire:click="nextPeriod" class="join-item btn btn-sm bg-base-100 hover:bg-base-200"
                title="Próximo Período">
                Próximo
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        </div>

        <div class="flex items-center gap-4">
            {{-- Card Counts --}}
            <div class="join shadow-sm">
                <button wire:click="$set('periodCount', 3)" @class(['join-item btn btn-sm', 'btn-primary' => $periodCount === 3])>3</button>
                <button wire:click="$set('periodCount', 5)" @class(['join-item btn btn-sm', 'btn-primary' => $periodCount === 5])>5</button>
                <button wire:click="$set('periodCount', 7)" @class([
                    'hidden 2xl:inline-flex join-item btn btn-sm',
                    'btn-primary' => $periodCount === 7,
                ])>7</button>
            </div>

            {{-- Filter Types --}}
            <div class="join shadow-sm">
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
    </div>

    {{-- Gráfico Principal --}}
    <x-financial.main-chart />

    <!-- Tabelas de Períodos -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 mt-8">
        <h2 class="text-2xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            Análise de Fluxo
        </h2>
    </div>


    @php
        $gridColsClass =
            [3 => 'lg:grid-cols-3', 5 => 'lg:grid-cols-5', 7 => 'lg:grid-cols-7'][$periodCount] ?? 'lg:grid-cols-3';
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 {{ $gridColsClass }} gap-6 mb-8"
        wire:loading.class="opacity-50 transition-opacity duration-300">
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
                    <div class="card-body p-4 gap-3">
                        <!-- RECEBIMENTOS (CAIXA) -->
                        <div>
                            <div
                                class="flex justify-between items-center bg-success/10 p-2 rounded-t-lg border-x border-t border-success/20">
                                <span class="text-[11px] font-black uppercase text-success"
                                    title="Entradas no Caixa">Recebimentos</span>
                                <span class="font-mono text-success font-bold text-sm">R$
                                    {{ number_format($data['billing_paid'] + $data['billing_pending'], 2, ',', '.') }}</span>
                            </div>
                            <div class="bg-base-300/20 rounded-b-lg border border-base-300/30 p-2 space-y-1">
                                <div class="flex justify-between items-center text-[11px] opacity-40">
                                    <span>Realizado</span>
                                    <span class="font-mono">R$
                                        {{ number_format($data['billing_paid'], 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] opacity-40">
                                    <span>A receber</span>
                                    <span class="font-mono">R$
                                        {{ number_format($data['billing_pending'], 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- ENCARGOS PARA FATURAMENTO (RESERVAR) -->
                        <div class="bg-warning/5 rounded-lg border border-warning/15 p-2 space-y-1">
                            <div class="flex justify-between items-center text-[11px] font-bold uppercase">
                                <span class="text-warning/80"
                                    title="Impostos que serão cobrados no mês seguinte. Separe esse valor!">Encargos
                                    para Faturamento (reservar)</span>
                                <span class="font-mono text-warning font-bold">R$
                                    {{ number_format($data['reserve_billing_total'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] opacity-40">
                                <span>Provisionado</span>
                                <span class="font-mono">R$
                                    {{ number_format($data['reserve_provisioned'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] opacity-40">
                                <span>Em Aberto</span>
                                <span class="font-mono">R$
                                    {{ number_format($data['reserve_pending'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- DESPESAS -->
                        <div class="bg-base-300/20 rounded-lg p-2 space-y-1">
                            <div class="flex justify-between items-center text-[11px] font-bold uppercase">
                                <span class="opacity-60">Despesas</span>
                                <span class="font-mono text-error">- R$
                                    {{ number_format($data['expenses_paid'] + $data['expenses_pending'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] opacity-40">
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
                        <div class="bg-base-300/20 rounded-lg p-2 space-y-1 mt-1">
                            <div class="flex justify-between items-center text-[11px] font-bold uppercase">
                                <span class="opacity-60">Retiradas</span>
                                <span class="font-mono" style="color: #8b5cf6">- R$
                                    {{ number_format($data['outflows_paid'] + $data['outflows_pending'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] opacity-40">
                                <span>Realizada</span>
                                <span class="font-mono">- R$
                                    {{ number_format($data['outflows_paid'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] opacity-40">
                                <span>Em Aberto</span>
                                <span class="font-mono">- R$
                                    {{ number_format($data['outflows_pending'], 2, ',', '.') }}</span>
                            </div>

                            <!-- Sugestão de Pró-labore -->
                            <div class="border-t border-base-content/10 pt-1.5 mt-1.5 space-y-1">
                                <div class="flex justify-between items-center text-[11px]">
                                    <span class="flex items-center gap-1">
                                        @php
                                            $pStatus = $data['prolabore_status'] ?? 'error';
                                            $pColor = match ($pStatus) {
                                                'success' => 'text-success',
                                                'warning' => 'text-warning',
                                                'error' => 'text-error',
                                                default => 'text-error',
                                            };
                                        @endphp
                                        @if ($pStatus === 'success')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-success">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @elseif ($pStatus === 'warning')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-warning">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-error">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        @endif
                                        <span class="{{ $pColor }} font-bold"
                                            title="28% do Faturamento ou Salário Mínimo (o que for maior)">Prolabore
                                            Sugerido</span>
                                    </span>
                                    <span class="font-mono {{ $pColor }} font-bold">R$
                                        {{ number_format($data['suggested_prolabore'], 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between items-center text-[11px] opacity-50 pl-5">
                                    <span>Realizado</span>
                                    @if ($data['prolabore_paid'] > 0)
                                        <span class="font-mono">R$
                                            {{ number_format($data['prolabore_paid'], 2, ',', '.') }}</span>
                                    @else
                                        <span class="font-mono italic">Não Realizado</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- ENCARGOS PARA RETIRADAS (RESERVAR) -->
                        <div class="bg-warning/5 rounded-lg border border-warning/15 p-2 space-y-1">
                            <div class="flex justify-between items-center text-[11px] font-bold uppercase">
                                <span class="text-warning/80"
                                    title="Reserva de 11% (INSS) sobre o valor do Prolabore. Separe esse valor!">Encargos
                                    para Retiradas (reservar)</span>
                                <span class="font-mono text-warning font-bold">R$
                                    {{ number_format($data['reserve_outflow_total'], 2, ',', '.') }}</span>
                            </div>
                        </div>

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

                            <div class="border-t border-base-content/5 pt-2 mt-1 space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold opacity-30 uppercase">Status Previsto</span>
                                    <span class="font-mono text-xs opacity-50 italic">
                                        R$ {{ number_format($data['final_balance_predicted'], 2, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] opacity-50">Entradas Previstas</span>
                                    <span class="font-mono text-[11px] opacity-60 text-success">
                                        R$ {{ number_format($data['total_predicted_inflows'], 2, ',', '.') }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[11px] opacity-50">Saídas Previstas</span>
                                    <span class="font-mono text-[11px] opacity-60 text-error">
                                        - R$ {{ number_format($data['total_predicted_outflows'], 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="divider my-0 mb-1 opacity-20"></div>

                            <div class="flex justify-between items-center">
                                <span class="text-xs opacity-80 font-bold">Saldo Final Previsto</span>
                                <span
                                    class="font-mono text-sm font-bold {{ $data['final_balance_predicted'] >= 0 ? 'text-success' : 'text-error' }}">
                                    R$ {{ number_format($data['final_balance_predicted'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- DIVISOR -->
                        <div class="border-t border-dashed border-base-content/10 my-1"></div>

                        <!-- FATURAMENTO DO MÊS (informativo, isolado) -->
                        <div class="bg-secondary/5 rounded-lg border border-secondary/15 p-2 space-y-1">
                            <div class="flex justify-between items-center text-[11px] font-bold uppercase">
                                <span class="text-secondary/80"
                                    title="Total faturado (NFs emitidas) neste mês">Faturamento do Mês</span>
                                <span class="font-mono text-secondary font-bold">R$
                                    {{ number_format($data['invoiced_total'], 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] opacity-40">
                                <span>Imposto s/ Faturamento
                                    ({{ number_format($data['invoiced_tax_rate'], 1, ',', '.') }}%)
                                </span>
                                <span class="font-mono">R$
                                    {{ number_format($data['invoiced_tax_total'], 2, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- SEÇÃO DE ENCARGOS (A pagar este mês - ref. anterior) -->
                        <div class="bg-base-300/20 rounded-lg p-2 space-y-2">
                            <div
                                class="flex justify-between items-center text-[10px] font-black uppercase text-base-content/40">
                                <span>Encargos (A pagar este mês)</span>
                                <span class="font-mono">R$
                                    {{ number_format($data['billing_tax_total'] + $data['outflow_tax_total'], 2, ',', '.') }}</span>
                            </div>

                            <!-- Subseção INSS -->
                            <div class="space-y-1 border-t border-base-content/5 pt-1">
                                <div class="flex justify-between items-center text-[11px] font-bold">
                                    <span class="opacity-70">INSS (ref. anterior)</span>
                                    <span class="font-mono text-warning">R$
                                        {{ number_format($data['outflow_tax_total'], 2, ',', '.') }}</span>
                                </div>
                                <div class="space-y-0.5 px-1">
                                    <div class="flex justify-between items-center text-[10px] opacity-40">
                                        <span>Pago</span>
                                        <span>R$ {{ number_format($data['outflow_tax_paid'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] opacity-40">
                                        <span>Provisionado</span>
                                        <span>R$
                                            {{ number_format($data['outflow_tax_provisioned'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] opacity-40">
                                        <span>A pagar</span>
                                        <span>R$ {{ number_format($data['outflow_tax_pending'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Subseção DAS -->
                            <div class="space-y-1 border-t border-base-content/5 pt-1">
                                <div class="flex justify-between items-center text-[11px] font-bold">
                                    <span class="opacity-70">DAS (ref. anterior)</span>
                                    <span class="font-mono text-warning">R$
                                        {{ number_format($data['billing_tax_total'], 2, ',', '.') }}</span>
                                </div>
                                <div class="space-y-0.5 px-1">
                                    <div class="flex justify-between items-center text-[10px] opacity-40">
                                        <span>Pago</span>
                                        <span>R$ {{ number_format($data['billing_tax_paid'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] opacity-40">
                                        <span>Provisionado</span>
                                        <span>R$
                                            {{ number_format($data['billing_tax_provisioned'], 2, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-[10px] opacity-40">
                                        <span>A pagar</span>
                                        <span>R$ {{ number_format($data['billing_tax_pending'], 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
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

        <div class="grid grid-cols-1 md:grid-cols-2 {{ $gridColsClass }} gap-6"
            wire:loading.class="opacity-50 transition-opacity duration-300">
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

    {{-- Composição das Saídas --}}
    <div class="mb-8">
        <h2 class="text-2xl font-bold flex items-center gap-2 mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
            </svg>
            Composição das Saídas
        </h2>

        <x-financial.expense-breakdown :gridColsClass="$gridColsClass" />
    </div>

</div>
</div>
