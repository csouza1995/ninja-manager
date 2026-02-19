<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Notas Fiscais</h2>
        <p class="mt-1 text-sm text-base-content/60">Controle a emissão e as chaves de acesso de NFs</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search"
                        placeholder="Buscar por número, chave ou observações..." class="input input-bordered w-full" />
                </div>
                <button wire:click="create" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova NF
                </button>
            </div>

            <livewire:components.financial.invoice-table :search="$search" />
        </div>
    </div>

    <livewire:components.financial.invoice-form />
</div>
