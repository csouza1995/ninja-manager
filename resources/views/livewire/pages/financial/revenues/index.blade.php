<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Controle de Receitas</h2>
        <p class="mt-1 text-sm text-base-content/60">Gerencie o faturamento e recebimentos da empresa</p>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mb-6">{{ session('success') }}</div>
    @endif

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1">
                    <!-- Espaço para filtros futuros se necessário -->
                </div>
                <button wire:click="create" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Receita
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Vencimento</th>
                            <th>Descrição / Origem</th>
                            <th>Classificação</th>
                            <th>Valor Bruto</th>
                            <th>Imposto</th>
                            <th>Valor Liq.</th>
                            <th>Banco</th>
                            <th>Status</th>
                            <th class="text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $revenue)
                            <tr>
                                <td class="whitespace-nowrap">{{ $revenue->due_date->format('d/m/Y') }}</td>
                                <td>
                                    <div class="font-bold">{{ $revenue->description }}</div>
                                    <div class="text-xs opacity-50">
                                        {{ $revenue->origin_name ?: ($revenue->service ? $revenue->service->client->name : '---') }}
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">
                                    <span class="badge badge-outline">{{ $revenue->classification }}</span>
                                </td>
                                <td class="font-mono font-bold opacity-70">
                                    R$ {{ number_format($revenue->gross_amount, 2, ',', '.') }}
                                </td>
                                <td class="font-mono font-bold text-error/70">
                                    R$ {{ number_format($revenue->tax_amount, 2, ',', '.') }}
                                </td>
                                <td class="font-mono font-bold text-success">
                                    R$ {{ number_format($revenue->net_amount, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap">
                                    {{ $revenue->bankAccount->nickname ?: $revenue->bankAccount->bank_name }}</td>
                                <td>
                                    @if ($revenue->paid_at)
                                        <span class="badge badge-success">Pago</span>
                                    @else
                                        <span class="badge badge-warning">Pendente</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button wire:click="edit({{ $revenue->id }})"
                                        class="btn btn-ghost btn-xs">Editar</button>
                                    <button wire:click="delete({{ $revenue->id }})" wire:confirm="Tem certeza?"
                                        class="btn btn-ghost btn-xs text-error">Excluir</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 opacity-50">Nenhuma receita registrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $revenues->links() }}
            </div>
        </div>
    </div>

    @if ($isFormOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg mb-6 border-b pb-2">{{ $revenueId ? 'Editar' : 'Nova' }} Receita</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Vincular Serviço (Opcional)</span></label>
                        <select wire:model.live="service_id" class="select select-bordered w-full">
                            <option value="">--- Selecione um Serviço Finalizado ---</option>
                            @foreach ($services as $s)
                                <option value="{{ $s->id }}">#{{ $s->id }} - {{ $s->client->name }}
                                    (R$ {{ number_format($s->total, 2) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Origem</span></label>
                        <input type="text" wire:model="origin_name" class="input input-bordered w-full"
                            placeholder="Nome do Cliente ou Fonte" />
                        @error('origin_name')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text">Descrição (Obrigatória)</span></label>
                        <input type="text" wire:model="description" class="input input-bordered w-full"
                            placeholder="O que é esta receita?" />
                        @error('description')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Classificação</span></label>
                        <input type="text" wire:model="classification" class="input input-bordered w-full"
                            list="class-suggestions" placeholder="Ex: Venda, Consultoria..." />
                        <datalist id="class-suggestions">
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
                            <option value="">--- Selecione a Conta ---</option>
                            @foreach ($bankAccounts as $ba)
                                <option value="{{ $ba->id }}">{{ $ba->nickname ?: $ba->bank_name }}
                                    ({{ $ba->owner_name }})
                                </option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Data Prevista (Vencimento)</span></label>
                        <input type="date" wire:model="due_date" class="input input-bordered w-full" />
                        @error('due_date')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Data de Recebimento (Opcional)</span></label>
                        <input type="date" wire:model="paid_at" class="input input-bordered w-full" />
                    </div>

                    <div class="divider md:col-span-2 italic text-base-content/40 text-sm font-bold">VALORES E
                        IMPOSTOS</div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Valor Bruto</span></label>
                        <div class="join w-full">
                            <span class="join-item btn btn-disabled">R$</span>
                            <input type="number" step="0.01" wire:model.live="gross_amount"
                                class="input input-bordered w-full join-item" />
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Alíquota de Imposto (%)</span></label>
                        <div class="join w-full">
                            <input type="number" step="0.01" wire:model.live="tax_percentage"
                                class="input input-bordered w-full join-item" />
                            <div class="dropdown dropdown-end join-item">
                                <div tabindex="0" role="button"
                                    class="btn btn-neutral btn-sm rounded-none h-full">Sugerir</div>
                                <ul tabindex="0"
                                    class="dropdown-content z-[1] menu p-2 shadow bg-base-200 rounded-box w-52">
                                    @foreach ($availableTaxes as $tax)
                                        <li><a wire:click.prevent="applyTax({{ $tax->id }})">{{ $tax->name }}
                                                ({{ $tax->percentage }}%)
                                            </a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text text-error font-bold">Valor
                                Imposto</span></label>
                        <div class="p-3 bg-base-200 rounded-lg text-error font-mono font-bold">
                            R$ {{ number_format($tax_amount, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text text-success font-bold">Valor
                                Líquido</span></label>
                        <div class="p-3 bg-base-200 rounded-lg text-success font-mono font-bold">
                            R$ {{ number_format($net_amount, 2, ',', '.') }}
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text text-secondary font-bold">ID da Nota Fiscal
                                (NF)</span></label>
                        <input type="text" wire:model="nf_id"
                            class="input input-bordered w-full border-secondary/50" placeholder="Ex: NF-0001" />
                    </div>
                </div>

                <div class="form-control w-full mt-4">
                    <label class="label"><span class="label-text">Anotações</span></label>
                    <textarea wire:model="notes" class="textarea textarea-bordered h-20" placeholder="Algum detalhe extra?"></textarea>
                </div>

                <div class="modal-action">
                    <button wire:click="closeForm" class="btn">Cancelar</button>
                    <button wire:click="save" class="btn btn-primary px-8">Salvar Receita</button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
