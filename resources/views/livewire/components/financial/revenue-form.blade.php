<div>
    @if ($isOpen)
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

                    <div class="divider md:col-span-2 italic text-base-content/40 text-sm font-bold">VALOR DO RECEBIMENTO</div>

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
                        <label class="label pb-1.5 flex justify-between items-center">
                            <span class="label-text group inline-flex items-center gap-1.5">
                                % Imposto Esperado
                                <div class="tooltip tooltip-right" data-tip="Selecione um anexo para aplicar a alíquota automaticamente">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </span>
                            @if (!$readOnly)
                                <select class="select select-bordered select-xs w-40 bg-base-200 border-base-300 font-normal" 
                                    wire:change="applyTax($event.target.value)">
                                    <option value="">Atalhos (Anexos)...</option>
                                    @foreach ($availableTaxes as $tax)
                                        <option value="{{ $tax->id }}" {{ $tax_percentage == $tax->percentage ? 'selected' : '' }}>
                                            {{ $tax->name }} ({{ number_format($tax->percentage, 2) }}%)
                                        </option>
                                    @endforeach
                                    <option value="0" {{ $tax_percentage == 0 ? 'selected' : '' }}>Não Aplicável (0%)</option>
                                </select>
                            @endif
                        </label>
                        <div class="join w-full">
                            <input type="number" step="0.01" wire:model.live="tax_percentage"
                                class="input input-bordered w-full join-item h-10" @disabled($readOnly) />
                            <span class="join-item btn btn-disabled px-2 h-10 min-h-0">%</span>
                        </div>
                        @error('tax_percentage')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 flex gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Valor Imposto</span></label>
                            <div class="p-3 bg-base-200 rounded-lg text-error font-mono font-bold text-sm truncate">
                                R$ {{ number_format($gross_amount * ($tax_percentage / 100), 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Valor Líquido</span></label>
                            <div class="p-3 bg-base-200 rounded-lg text-success font-mono font-bold text-sm truncate">
                                R$ {{ number_format($gross_amount - ($gross_amount * ($tax_percentage / 100)), 2, ',', '.') }}
                            </div>
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
                    <button wire:click="close" class="btn">
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
