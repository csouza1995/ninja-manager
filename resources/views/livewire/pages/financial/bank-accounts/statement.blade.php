<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
            <div>
                <nav class="text-sm breadcrumbs mb-1 opacity-60">
                    <ul>
                        <li><a href="{{ route('financial.bank-accounts') }}">Contas Bancárias</a></li>
                        <li>Extrato</li>
                    </ul>
                </nav>
                <div class="flex items-center gap-4">
                    <h1 class="text-3xl font-bold text-primary">{{ $account->name }}</h1>
                    <span class="badge badge-lg badge-outline opacity-50 font-mono">
                        R$ {{ number_format($account->opening_balance, 2, ',', '.') }} (Início)
                    </span>
                </div>
            </div>

            <div class="stats shadow bg-primary/5 border border-primary/10 w-fit">
                <div class="stat py-2 px-6">
                    <div class="stat-title text-[10px] uppercase font-black opacity-50">Saldo Consolidado Estimado</div>
                    <div class="stat-value text-xl font-mono {{ $this->consolidatedBalance >= 0 ? 'text-success' : 'text-error' }}">
                        R$ {{ number_format($this->consolidatedBalance, 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-4 mb-8 bg-base-200/50 p-4 rounded-2xl border border-base-300">
            <!-- Row 1: Search -->
            <div class="relative w-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por descrição ou pessoa..." class="input input-bordered w-full pl-10 bg-base-100" />
                @if($search || $dateFrom || $dateTo)
                    <button wire:click="clearFilters" class="btn btn-ghost btn-xs absolute right-2 top-1/2 -translate-y-1/2 text-error">
                        Limpar
                    </button>
                @endif
            </div>

            <!-- Row 2: Shortcuts and Custom Date -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-[10px] font-black uppercase opacity-40 mr-1">Atalhos:</span>
                    <button wire:click="setFilter('this_month')" class="btn btn-xs {{ $dateFrom == now()->startOfMonth()->format('Y-m-d') ? 'btn-primary shadow-sm' : 'btn-ghost bg-base-200 border-base-300' }}">Este Mês</button>
                    <button wire:click="setFilter('last_month')" class="btn btn-xs {{ $dateFrom == now()->subMonth()->startOfMonth()->format('Y-m-d') ? 'btn-primary shadow-sm' : 'btn-ghost bg-base-200 border-base-300' }}">Mês Passado</button>
                    <button wire:click="setFilter('today')" class="btn btn-xs {{ $dateFrom == now()->format('Y-m-d') ? 'btn-primary shadow-sm' : 'btn-ghost bg-base-200 border-base-300' }}">Hoje</button>
                    <button wire:click="setFilter('all')" class="btn btn-xs {{ !$dateFrom ? 'btn-primary shadow-sm' : 'btn-ghost bg-base-200 border-base-300' }}">Tudo</button>
                </div>

                <div class="flex flex-col md:flex-row md:items-center gap-2">
                    <span class="text-[10px] font-black uppercase opacity-40 md:mr-1">Período:</span>
                    <div class="join border border-base-300 shadow-sm overflow-hidden rounded-lg">
                        <input type="date" wire:model.live="dateFrom" class="input input-xs h-8 input-ghost join-item bg-base-100 border-r border-base-300 focus:bg-base-100 md:w-32" />
                        <input type="date" wire:model.live="dateTo" class="input input-xs h-8 input-ghost join-item bg-base-100 focus:bg-base-100 md:w-32" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="relative pl-8 md:pl-10">
            <!-- Vertical Line -->
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-base-300 rounded-full"></div>

            <div class="space-y-5">
                @forelse($groupedTransactions as $dayGroup)

                    {{-- Month divider — first day group of each month in DESC = month-end closing --}}
                    @if($dayGroup['is_month_end_row'])
                        <div class="-ml-8 md:-ml-10">
                            <div class="flex items-center gap-3 px-4 py-2.5 bg-primary/10 border border-primary/20 rounded-xl">
                                <span class="text-[11px] font-black uppercase tracking-widest text-primary/80">
                                    {{ \Carbon\Carbon::parse($dayGroup['date'])->translatedFormat('F Y') }}
                                </span>
                                <div class="flex-1 h-px bg-primary/20"></div>
                                <span class="text-[11px] font-mono font-black {{ $dayGroup['month_closing'] >= 0 ? 'text-success' : 'text-error' }}">
                                    Fechamento Mês: R$ {{ number_format($dayGroup['month_closing'], 2, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Day group --}}
                    <div class="space-y-3">
                        {{-- Day header --}}
                        <div class="relative flex items-center gap-3">
                            <div class="absolute -left-8 md:-left-10 translate-x-1.5 z-10">
                                <div class="w-5 h-5 rounded-full bg-base-200 border-2 border-base-300 flex items-center justify-center">
                                    <div class="w-1.5 h-1.5 rounded-full bg-base-content/30"></div>
                                </div>
                            </div>
                            <span class="text-[11px] font-bold text-base-content/40 uppercase tracking-wide">
                                {{ \Carbon\Carbon::parse($dayGroup['date'])->translatedFormat('l, d/m/Y') }}
                            </span>
                            <div class="flex-1 border-t border-dashed border-base-300/60"></div>
                            <span class="text-[11px] font-mono text-base-content/50">
                                Fechamento: <strong class="{{ $dayGroup['day_closing'] >= 0 ? 'text-success' : 'text-error' }}">R$ {{ number_format($dayGroup['day_closing'], 2, ',', '.') }}</strong>
                            </span>
                        </div>

                        {{-- Transactions for this day --}}
                        @foreach($dayGroup['transactions'] as $transaction)
                            <div class="relative flex items-start group">
                                <!-- Icon on the line -->
                                <div class="absolute -left-8 md:-left-10 translate-x-1.5 mt-1.5 z-10">
                                    @if($transaction->color === 'secondary')
                                        <div class="w-7 h-7 rounded-full text-white flex items-center justify-center shadow-md border-2 border-base-100 ring-2 ring-base-200"
                                             style="background-color: #8b5cf6">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-{{ $transaction->color }} text-{{ $transaction->color }}-content flex items-center justify-center shadow-md border-2 border-base-100 ring-2 ring-base-200">
                                    @endif
                                            @if($transaction->icon === 'plus')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            @elseif($transaction->icon === 'withdraw')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 19.5 15-15m0 0H8.25m11.25 0v11.25" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                                </svg>
                                            @endif
                                        </div>
                                </div>

                                <!-- Card Content -->
                                <div class="flex-1 bg-base-100 p-4 rounded-xl border border-base-300 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-2">
                                        <div>
                                            <div class="flex items-center gap-2 mb-1">
                                                <span class="badge badge-xs badge-ghost uppercase text-[9px] font-black opacity-50">
                                                    {{ $transaction->type }}
                                                </span>
                                            </div>
                                            <h3 class="font-bold text-base leading-tight">{{ $transaction->description }}</h3>
                                            <p class="text-xs opacity-60 mt-0.5">{{ $transaction->counterparty }}</p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <div class="font-black font-mono text-lg @if($transaction->color !== 'secondary') text-{{ $transaction->color }} @endif"
                                                 @if($transaction->color === 'secondary') style="color: #8b5cf6" @endif>
                                                {{ $transaction->icon === 'plus' ? '+' : '-' }} R$ {{ number_format($transaction->amount, 2, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @empty
                    <div class="text-center py-20 opacity-30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 0h6" />
                        </svg>
                        <p class="text-xl">Nenhuma movimentação encontrada no período.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
