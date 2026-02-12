<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Contas Bancárias</h2>
        <p class="mt-1 text-sm text-base-content/60">Gerencie as contas e saldos da empresa</p>
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
                    Nova Conta
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Banco</th>
                            <th>Titular</th>
                            <th>Apelido</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>{{ $account->bank_name }}</td>
                                <td>{{ $account->owner_name }}</td>
                                <td class="italic text-base-content/60">{{ $account->nickname ?? '---' }}</td>
                                <td class="text-right">
                                    <button wire:click="edit({{ $account->id }})"
                                        class="btn btn-ghost btn-xs">Editar</button>
                                    <button wire:click="delete({{ $account->id }})" wire:confirm="Tem certeza?"
                                        class="btn btn-ghost btn-xs text-error">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 opacity-50">Nenhuma conta cadastrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($isFormOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">{{ $bankAccountId ? 'Editar' : 'Nova' }} Conta</h3>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Banco (Nome)</span></label>
                    <input type="text" wire:model="bank_name" class="input input-bordered w-full"
                        placeholder="Ex: Banco do Brasil, Nubank..." />
                    @error('bank_name')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Titular (Pessoa)</span></label>
                    <input type="text" wire:model="owner_name" class="input input-bordered w-full"
                        placeholder="Nome do Titular" />
                    @error('owner_name')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Apelido (Opcional)</span></label>
                    <input type="text" wire:model="nickname" class="input input-bordered w-full"
                        placeholder="Ex: Conta Principal, PJ..." />
                    @error('nickname')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
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
