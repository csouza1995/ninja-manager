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
                <div class="flex-1">
                </div>
                <button wire:click="create" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Novo Imposto
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Percentual</th>
                            <th>Status</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($taxes as $tax)
                            <tr>
                                <td class="font-medium">{{ $tax->name }}</td>
                                <td>{{ number_format($tax->percentage, 2, ',', '.') }}%</td>
                                <td>
                                    <span class="badge {{ $tax->is_active ? 'badge-success' : 'badge-ghost' }}">
                                        {{ $tax->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <button wire:click="edit({{ $tax->id }})"
                                        class="btn btn-ghost btn-xs">Editar</button>
                                    <button wire:click="delete({{ $tax->id }})" wire:confirm="Tem certeza?"
                                        class="btn btn-ghost btn-xs text-error">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 opacity-50">Nenhum imposto cadastrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $taxes->links() }}
    </div>

    @if ($isFormOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $taxId ? 'Editar' : 'Novo' }} Imposto</h3>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Nome do Imposto</span></label>
                    <input type="text" wire:model="name" class="input input-bordered w-full"
                        placeholder="Ex: ISS, Simples Nacional..." />
                    @error('name')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Alíquota (%)</span></label>
                    <input type="number" step="0.01" wire:model="percentage" class="input input-bordered w-full" />
                    @error('percentage')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="checkbox" wire:model="is_active" class="checkbox checkbox-primary" />
                        <span class="label-text">Ativo</span>
                    </label>
                </div>

                <div class="modal-action">
                    <button wire:click="closeForm" class="btn">Cancelar</button>
                    <button wire:click="save" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
