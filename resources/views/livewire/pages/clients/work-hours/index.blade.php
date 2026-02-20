<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Horas de Trabalho</h2>
            <button wire:click="openForm" class="btn btn-primary btn-sm">+ Lançar Horas</button>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">

        {{-- Filtro por cliente --}}
        <div class="flex flex-wrap items-center gap-3">
            <select wire:model.live="clientId" class="select select-bordered select-sm min-w-[220px]">
                <option value="">Todos os clientes</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>
            @if ($clientId)
                <button wire:click="$set('clientId', null)" class="btn btn-ghost btn-sm">Limpar</button>
            @endif
        </div>

        {{-- Card de saldo pendente --}}
        <div class="card bg-base-200 border border-base-300 shadow-xl">
            <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
                <span class="font-bold text-sm uppercase tracking-wider opacity-70">
                    Saldo Pendente a Pagar
                    @if ($this->selectedClient)
                        — {{ $this->selectedClient->name }}
                    @endif
                </span>
                <span class="text-[10px] font-bold uppercase opacity-40">Executado − Pago</span>
            </div>
            <div class="card-body p-6">
                <div class="flex items-end gap-4">
                    <div
                        class="text-5xl font-mono font-black
                        {{ $this->pendingMinutes > 0 ? 'text-warning' : 'text-success' }}">
                        {{ $this->pendingFormatted }}
                    </div>
                    <div class="text-base-content/50 text-sm pb-1">
                        {{ $this->pendingMinutes > 0 ? 'horas pendentes de faturamento' : 'sem horas pendentes 🎉' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabela --}}
        <div class="card bg-base-200 border border-base-300 shadow-xl overflow-hidden">
            <div class="bg-base-200 px-4 py-3 border-b border-base-300 flex justify-between items-center">
                <span class="font-bold text-sm uppercase tracking-wider opacity-70">Lançamentos</span>
                @if ($clientId)
                    <button wire:click="openForm({{ $clientId }})" class="btn btn-primary btn-xs">+ Lançar para este
                        cliente</button>
                @endif
            </div>
            <div class="card-body p-0">
                <livewire:components.client-work-hours.table :client-id="$clientId" />
            </div>
        </div>

    </div>

    <livewire:components.client-work-hours.form />
</div>
