<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Recibos Gerados</h2>
        <p class="mt-1 text-sm text-base-content/60">Histórico de documentos e downloads de PDF</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <livewire:components.receipts.table />
        </div>
    </div>

    <livewire:components.receipts.viewer />
</div>
