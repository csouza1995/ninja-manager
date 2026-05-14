<div>
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="shrink-0">
            <h2 class="text-3xl font-bold text-error">Controle de Despesas</h2>
            <p class="mt-1 text-sm text-base-content/60">Gerencie os gastos e pagamentos da empresa</p>
        </div>

        <div class="flex flex-wrap gap-3 lg:justify-end ml-auto items-stretch">
            {{-- Card 1: Total pendente geral --}}
            <div class="w-64">
                <div class="stats shadow bg-base-200 border border-base-300 w-full h-full">
                    <div class="stat p-4">
                        <div class="stat-title text-[10px] font-bold uppercase opacity-50">Total Pendente</div>
                        <div class="stat-value text-xl font-mono text-error">
                            R$ {{ number_format((float) $totalPending, 2, ',', '.') }}
                        </div>
                        <div class="stat-desc opacity-40">em aberto (todos os períodos)</div>
                    </div>
                </div>
            </div>

            {{-- Card 2: Pendente mês atual --}}
            <div class="w-64">
                <div class="stats shadow bg-base-200 border border-base-300 w-full h-full">
                    <div class="stat p-4">
                        <div class="stat-title text-[10px] font-bold uppercase opacity-50">Mês Atual</div>
                        <div class="stat-value text-xl font-mono text-warning">
                            R$ {{ number_format((float) $monthPending, 2, ',', '.') }}
                        </div>
                        <div class="stat-desc opacity-40">vence / ref. {{ now()->translatedFormat('M/Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Card 3: Próximo mês --}}
            <div class="w-64">
                <div class="stats shadow bg-base-200 border border-base-300 w-full h-full">
                    <div class="stat p-4">
                        <div class="stat-title text-[10px] font-bold uppercase opacity-50">Próximo Mês</div>
                        <div class="stat-value text-xl font-mono text-info">
                            R$ {{ number_format((float) $nextMonthPending, 2, ',', '.') }}
                        </div>
                        <div class="stat-desc opacity-40">vence / ref. {{ now()->addMonth()->translatedFormat('M/Y') }}</div>
                    </div>
                </div>
            </div>

            {{-- Card 4: Breakdown por classe --}}
            <div class="w-64">
                <div class="stats shadow bg-base-200 border border-base-300 w-full h-full">
                    <div class="stat p-4">
                        <div class="stat-title text-[10px] font-bold uppercase opacity-50 mb-2">Pendente por Classe</div>
                        <div class="space-y-1">
                            <div class="flex justify-between items-center text-[11px] font-black opacity-80">
                                <span>Impostos NF / Receita</span>
                                <span class="font-mono text-error ml-4">R$ {{ number_format((float) $taxNf, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] font-black opacity-80">
                                <span>Pró-labore (INSS)</span>
                                <span class="font-mono text-warning ml-4">R$ {{ number_format((float) $taxProlabore, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-[11px] font-black border-t border-base-300/50 pt-1 mt-1">
                                <span class="opacity-60">Outros</span>
                                <span class="font-mono ml-4">R$ {{ number_format((float) $taxOther, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md flex items-center gap-2">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por descrição, destino ou classificação..."
                        class="input input-bordered w-full" />
                
                    <button wire:click="$dispatch('toggle-filters')" class="btn btn-ghost btn-sm" title="Alternar Filtros">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                        </svg>
                    </button>
                </div>
                <button wire:click="create" class="btn btn-error text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Despesa
                </button>
            </div>

            <livewire:components.financial.expenditure-table :search="$search" />

            <livewire:components.financial.expenditure-form />
        </div>
