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
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search"
                        placeholder="Buscar por origem, descrição, classificação ou NF..."
                        class="input input-bordered w-full" />
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
                                        @if ($revenue->invoice)
                                            <span class="badge badge-xs badge-ghost ml-2">NF:
                                                {{ $revenue->invoice->number }}</span>
                                        @endif
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
                                    <div class="flex justify-end gap-1">
                                        <!-- View Revenue -->
                                        <button wire:click="show({{ $revenue->id }})"
                                            class="btn btn-square btn-ghost btn-xs text-primary" title="Visualizar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            </svg>
                                        </button>

                                        <button wire:click="edit({{ $revenue->id }})"
                                            class="btn btn-square btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>

                                        <button wire:click="delete({{ $revenue->id }})" wire:confirm="Tem certeza?"
                                            class="btn btn-square btn-ghost btn-xs text-error" title="Excluir">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.34 7m-4.74 0-.34-7m10 4.634V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6.634m12 0a2 2 0 0 0-2-2h-3.366a2 2 0 0 0-1.268.464L9.08 6.634a2 2 0 0 0-2 2h10.74Z" />
                                            </svg>
                                        </button>
                                    </div>
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
                <h3 class="font-bold text-lg mb-6 border-b pb-2">
                    @if ($readOnly)
                        Visualizar Receita
                    @else
                        {{ $revenueId ? 'Editar' : 'Nova' }} Receita
                    @endif
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Vincular Serviço (Opcional)</span></label>
                        <select wire:model.live="service_id" class="select select-bordered w-full"
                            @disabled($readOnly)>
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
                            placeholder="Nome do Cliente ou Fonte" @disabled($readOnly) />
                        @error('origin_name')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text">Descrição (Obrigatória)</span></label>
                        <input type="text" wire:model="description" class="input input-bordered w-full"
                            placeholder="O que é esta receita?" @disabled($readOnly) />
                        @error('description')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Classificação</span></label>
                        <input type="text" wire:model="classification" class="input input-bordered w-full"
                            list="class-suggestions" placeholder="Ex: Venda, Consultoria..."
                            @disabled($readOnly) />
                        @if (!$readOnly)
                            <datalist id="class-suggestions">
                                @foreach ($classificationSuggestions as $sug)
                                    <option value="{{ $sug }}">
                                @endforeach
                            </datalist>
                        @endif
                        @error('classification')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Conta Bancária</span></label>
                        <select wire:model="bank_account_id" class="select select-bordered w-full"
                            @disabled($readOnly)>
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
                        <input type="date" wire:model="due_date" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        @error('due_date')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Data de Recebimento (Opcional)</span></label>
                        <input type="date" wire:model="paid_at" class="input input-bordered w-full"
                            @disabled($readOnly) />
                    </div>

                    <div class="divider md:col-span-2 italic text-base-content/40 text-sm font-bold">VALORES E
                        IMPOSTOS</div>

                    <div class="form-control w-full">
                        <label class="label w-full flex justify-between">
                            <span class="label-text">Valor Bruto</span>
                            @if ($service_id && !$readOnly)
                                <button type="button" wire:click="recalculateFromService"
                                    class="btn btn-xs btn-outline btn-success gap-1" title="Recalcular do Serviço">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Recalcular
                                </button>
                            @endif
                        </label>
                        <div class="join w-full">
                            <span class="join-item btn btn-disabled">R$</span>
                            <input type="number" step="0.01" wire:model.live="gross_amount"
                                class="input input-bordered w-full join-item" @disabled($readOnly) />
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Alíquota de Imposto (%)</span></label>
                        <div class="join w-full">
                            <input type="number" step="0.01" wire:model.live="tax_percentage"
                                class="input input-bordered w-full join-item" @disabled($readOnly) />
                            @if (!$readOnly)
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
                            @endif
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
                        <label class="label">
                            <span class="label-text text-secondary font-bold">Nota Fiscal (NF)</span>
                        </label>
                        <select wire:model="invoice_id" class="select select-bordered w-full border-secondary/50"
                            @disabled($readOnly)>
                            <option value="">--- Selecione a NF ---</option>
                            @foreach ($invoices as $inv)
                                <option value="{{ $inv->id }}">NF {{ $inv->number }} (R$
                                    {{ number_format($inv->amount, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text">Anotações</span></label>
                        <textarea wire:model="notes" class="textarea textarea-bordered w-full " placeholder="Algum detalhe extra?"
                            @disabled($readOnly)></textarea>
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="closeForm" class="btn">
                        {{ $readOnly ? 'Fechar' : 'Cancelar' }}
                    </button>
                    @if (!$readOnly)
                        <button wire:click="save" class="btn btn-primary px-8">Salvar Receita</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
</div>
