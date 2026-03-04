<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-error">Controle de Despesas</h2>
        <p class="mt-1 text-sm text-base-content/60">Gerencie os gastos e pagamentos da empresa</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por descrição, destino ou classificação..."
                        class="input input-bordered w-full" />
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
