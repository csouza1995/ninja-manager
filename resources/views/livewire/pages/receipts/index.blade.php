<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Recibos Gerados</h2>
        <p class="mt-1 text-sm text-base-content/60">Histórico de documentos e downloads de PDF</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Buscar por número ou cliente..."
                        class="input input-bordered w-full" />
                </div>
            </div>

            <livewire:components.receipts.table :search="$search" />
        </div>
    </div>

    <livewire:components.receipts.viewer />
</div>
