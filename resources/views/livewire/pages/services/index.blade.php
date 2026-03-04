<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Gestão de Serviços</h2>
        <p class="mt-1 text-sm text-base-content/60">Acompanhe e gerencie ordens de serviço e status</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <livewire:components.services.table />
        </div>
    </div>

    <livewire:components.services.form />
    <livewire:components.services.delete-dialog />
    <livewire:components.receipts.viewer />
</div>
