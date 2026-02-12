<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-primary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Impostos
            </h1>
            <button wire:click="create" class="btn btn-primary">Novo Imposto</button>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-base-100 rounded-box shadow overflow-x-auto">
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
                        <input type="number" step="0.01" wire:model="percentage"
                            class="input input-bordered w-full" />
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
