<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Impostos</h2>
        <p class="mt-1 text-sm text-base-content/60">Configure as alíquotas de impostos aplicáveis</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md flex items-center gap-2">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Buscar por nome do imposto..." class="input input-bordered w-full" />
                
                    <button wire:click="$dispatch('toggle-filters')" class="btn btn-ghost btn-sm" title="Alternar Filtros">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                        </svg>
                    </button>
                </div>
                <button wire:click="create" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Imposto
                </button>
            </div>

            <livewire:components.financial.tax-table :search="$search" />
        </div>
    </div>

    <livewire:components.financial.tax-form />
</div>
