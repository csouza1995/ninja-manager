<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Clientes</h2>
        <p class="mt-1 text-sm text-base-content/60">Gerencie clientes PF e PJ</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Procurar..."
                        class="input input-bordered w-full" />
                </div>
                <button wire:click="$dispatch('create-client')" type="button" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Cliente
                </button>
            </div>

            <livewire:components.clients.table :search="$search" />
        </div>
    </div>

    <livewire:components.clients.form />
    <livewire:components.clients.delete-dialog />
</div>
