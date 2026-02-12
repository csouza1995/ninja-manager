<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-error">Controle de Despesas</h2>
        <p class="mt-1 text-sm text-base-content/60">Gerencie os gastos e pagamentos da empresa</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search"
                        placeholder="Buscar por descrição, destino ou classificação..."
                        class="input input-bordered w-full" />
                </div>
                <button wire:click="create" class="btn btn-error text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Despesa
                </button>
            </div>

            <div class="overflow-x-auto">
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
                                        <span class="badge badge-success">Pago</span>
                                    @else
                                        <span class="badge badge-warning">Pendente</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $exp->id }})"
                                            class="btn btn-square btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $exp->id }})" wire:confirm="Tem certeza?"
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
                                <td colspan="7" class="text-center py-8 opacity-50">Nenhuma despesa registrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $expenditures->links() }}
            </div>
        </div>
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
