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
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Buscar por banco, titular ou apelido..."
                        class="input input-bordered w-full" />
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
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $account->id }})"
                                            class="btn btn-square btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $account->id }})" wire:confirm="Tem certeza?"
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
