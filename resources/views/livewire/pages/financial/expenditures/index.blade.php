<div class="p-6">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-error flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                </svg>
                Controle de Despesas
            </h1>
            <button wire:click="create" class="btn btn-error text-white">Nova Despesa</button>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-success mb-6">{{ session('success') }}</div>
        @endif

        <div class="bg-base-100 rounded-box shadow overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vencimento</th>
                        <th>Descrição / Destino</th>
                        <th>Classificação</th>
                        <th>Valor</th>
                        <th>Banco</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenditures as $exp)
                        <tr>
                            <td class="whitespace-nowrap">{{ $exp->due_date->format('d/m/Y') }}</td>
                            <td>
                                <div class="font-bold">{{ $exp->description }}</div>
                                <div class="text-xs opacity-50">{{ $exp->destination }}</div>
                            </td>
                            <td class="whitespace-nowrap">
                                <span class="badge badge-outline">{{ $exp->classification }}</span>
                            </td>
                            <td class="font-mono font-bold text-error">
                                R$ {{ number_format($exp->amount, 2, ',', '.') }}
                            </td>
                            <td class="whitespace-nowrap">
                                {{ $exp->bankAccount->nickname ?: $exp->bankAccount->bank_name }}</td>
                            <td>
                                @if ($exp->paid_at)
                                    <span class="badge badge-success">Pago em
                                        {{ $exp->paid_at->format('d/m/Y') }}</span>
                                @else
                                    <span class="badge badge-warning">Pendente</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <button wire:click="edit({{ $exp->id }})"
                                    class="btn btn-ghost btn-xs">Editar</button>
                                <button wire:click="delete({{ $exp->id }})" wire:confirm="Tem certeza?"
                                    class="btn btn-ghost btn-xs text-error">Excluir</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 opacity-50">Nenhuma despesa registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $expenditures->links() }}
        </div>

        @if ($isFormOpen)
            <div class="modal modal-open">
                <div class="modal-box">
                    <h3 class="font-bold text-lg mb-6 border-b pb-2">{{ $expenditureId ? 'Editar' : 'Nova' }} Despesa
                    </h3>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Destino / Favorecido</span></label>
                            <input type="text" wire:model="destination" class="input input-bordered w-full"
                                list="dest-suggestions" placeholder="Pessoa, Empresa ou Fornecedor" />
                            <datalist id="dest-suggestions">
                                @foreach ($destinationSuggestions as $sug)
                                    <option value="{{ $sug }}">
                                @endforeach
                            </datalist>
                            @error('destination')
                                <span class="text-error text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Descrição</span></label>
                            <input type="text" wire:model="description" class="input input-bordered w-full"
                                placeholder="O que está sendo pago?" />
                            @error('description')
                                <span class="text-error text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Classificação</span></label>
                                <input type="text" wire:model="classification" class="input input-bordered w-full"
                                    list="class-exp-suggestions" placeholder="Ex: Insumo, Aluguel..." />
                                <datalist id="class-exp-suggestions">
                                    @foreach ($classificationSuggestions as $sug)
                                        <option value="{{ $sug }}">
                                    @endforeach
                                </datalist>
                                @error('classification')
                                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Conta Bancária</span></label>
                                <select wire:model="bank_account_id" class="select select-bordered w-full">
                                    <option value="">--- Selecione ---</option>
                                    @foreach ($bankAccounts as $ba)
                                        <option value="{{ $ba->id }}">{{ $ba->nickname ?: $ba->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('bank_account_id')
                                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Data Prevista</span></label>
                                <input type="date" wire:model="due_date" class="input input-bordered w-full" />
                                @error('due_date')
                                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Data Pagamento (Opcional)</span></label>
                                <input type="date" wire:model="paid_at" class="input input-bordered w-full" />
                            </div>

                            <div class="form-control w-full md:col-span-2">
                                <label class="label"><span class="label-text">Valor</span></label>
                                <div class="join w-full">
                                    <span class="join-item btn btn-disabled">R$</span>
                                    <input type="number" step="0.01" wire:model="amount"
                                        class="input input-bordered w-full join-item" />
                                </div>
                                @error('amount')
                                    <span class="text-error text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="modal-action">
                        <button wire:click="closeForm" class="btn">Cancelar</button>
                        <button wire:click="save" class="btn btn-error text-white px-8">Salvar Despesa</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
