<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight text-base-content/80">Horas de Trabalho</h2>
            <button wire:click="openForm" class="btn btn-primary btn-sm px-6">+ Lançar Horas</button>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        {{-- Filtro por cliente --}}
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="clientId" class="select select-bordered select-sm min-w-[250px] bg-base-200">
                <option value="">Todos os clientes</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
            @if ($clientId)
                <button wire:click="$set('clientId', null)" class="btn btn-ghost btn-sm text-xs opacity-50">Limpar
                    Filtro</button>
            @endif
        </div>

        {{-- Grid de Métricas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Card Saldo de Horas --}}
            <div class="card bg-base-200 border border-base-300 shadow-sm">
                <div class="px-4 py-3 border-b border-base-300 flex justify-between items-center opacity-70">
                    <span class="font-bold text-[10px] uppercase tracking-wider">Saldo Pendente (Horas)</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="card-body p-5">
                    <div class="flex items-end gap-2">
                        <div @class([
                            'text-3xl font-mono font-black',
                            'text-warning' => $this->metrics['pending_minutes'] > 0,
                            'text-success opacity-80' => $this->metrics['pending_minutes'] <= 0,
                        ])>
                            {{ $this->metrics['pending_formatted'] }}
                        </div>
                        <div class="text-[10px] uppercase font-bold opacity-30 pb-1">horas</div>
                    </div>
                </div>
            </div>

            {{-- Card Saldo Monetário --}}
            <div class="card bg-base-200 border border-base-300 shadow-sm">
                <div class="px-4 py-3 border-b border-base-300 flex justify-between items-center opacity-70">
                    <span class="font-bold text-[10px] uppercase tracking-wider">Saldo Pendente (Valor Est.)</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="card-body p-5">
                    <div class="flex items-end gap-2">
                        <div class="text-3xl font-mono font-black text-success">
                            R$ {{ number_format($this->metrics['pending_value'], 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Atingimento / Métricas --}}
            <div class="card bg-base-200 border border-base-300 shadow-sm">
                <div class="px-4 py-3 border-b border-base-300 flex justify-between items-center opacity-70">
                    <span class="font-bold text-[10px] uppercase tracking-wider">Atingimento de Contrato</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="card-body p-5">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2">
                            <div class="text-3xl font-mono font-black text-primary">
                                {{ $this->metrics['achievement'] }}%
                            </div>
                            <div class="w-full bg-base-300 h-2 rounded-full overflow-hidden">
                                <div class="bg-primary h-full rounded-full transition-all duration-500"
                                    style="width: {{ min(100, $this->metrics['achievement']) }}%"></div>
                            </div>
                        </div>
                        <div class="text-[9px] uppercase font-bold opacity-30 mt-1">
                            Eficiência de entrega
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Gap de Oportunidade --}}
            <div class="card bg-base-200 border border-base-300 shadow-sm">
                <div class="px-4 py-3 border-b border-base-300 flex justify-between items-center opacity-70">
                    <span class="font-bold text-[10px] uppercase tracking-wider">Potencial vs Realizado</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="card-body p-5">
                    <div class="flex flex-col">
                        <div class="text-2xl font-mono font-black text-secondary">
                            R$ {{ number_format($this->metrics['executed_value'], 2, ',', '.') }}
                        </div>
                        <div class="text-[10px] uppercase font-bold opacity-30">
                            de R$ {{ number_format($this->metrics['total_contract_value'], 2, ',', '.') }}
                        </div>
                        <div class="text-[9px] mt-2 italic opacity-40">
                            Gap: R$
                            {{ number_format(max(0, $this->metrics['total_contract_value'] - $this->metrics['executed_value']), 2, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card bg-base-200 border border-base-300 shadow-xl overflow-hidden">
            <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
                <span class="font-bold text-sm uppercase tracking-wider opacity-70">Histórico de Lançamentos</span>
                @if ($clientId)
                    <button wire:click="openForm({{ $clientId }})" class="btn btn-primary btn-xs px-4">+ Novo para
                        {{ $this->selectedClient->name }}</button>
                @endif
            </div>
            <div class="card-body p-0">
                <livewire:components.client-work-hours.table :client-id="$clientId" />
            </div>
        </div>

    </div>

    <livewire:components.client-work-hours.form />
</div>
