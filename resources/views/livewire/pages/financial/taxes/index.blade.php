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
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Buscar por nome do imposto..."
                        class="input input-bordered w-full" />
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
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $tax->id }})"
                                            class="btn btn-square btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $tax->id }})" wire:confirm="Tem certeza?"
                                            class="btn btn-square btn-ghost btn-xs text-error" title="Excluir">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.34 7m-4.74 0-.34-7m10 4.634V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6.634m12 0a2 2 0 0 0-2-2h-3.366a2.268 0 0 0-1.268.464L9.08 6.634a2 2 0 0 0-2 2h10.74Z" />
                                            </svg>
                                        </button>
                                    </div>
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
