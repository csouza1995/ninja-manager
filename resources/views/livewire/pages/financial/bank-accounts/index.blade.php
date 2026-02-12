<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-primary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                </svg>
                Contas Bancárias
            </h1>
            <button wire:click="create" class="btn btn-primary">Nova Conta</button>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-base-100 rounded-box shadow overflow-x-auto">
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
