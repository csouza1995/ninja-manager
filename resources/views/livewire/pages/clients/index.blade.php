<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Clientes</h2>
        <p class="mt-1 text-sm text-base-content/60">Gerencie clientes PF e PJ</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <livewire:components.clients.table />
        </div>
    </div>

    <livewire:components.clients.form />
    <livewire:components.clients.delete-dialog />
</div>
