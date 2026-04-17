<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl overflow-x-hidden">
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

                    <div class="form-control w-full md:col-span-2 bg-secondary/5 p-4 rounded-lg border border-secondary/20">
                        <label class="label pt-0 pb-1">
                            <span class="label-text font-black text-secondary uppercase text-xs">Vincular Devolução (Estorno/Saída)</span>
                        </label>
                        <select wire:model.live="linkable_id" class="select select-bordered select-sm w-full font-mono"
                            @disabled($readOnly)>
                            <option value="">--- Selecione se for uma Devolução/Repagamento ---</option>
                            @foreach ($this->linkableOutflows as $outflow)
                                <option value="{{ $outflow->id }}">
                                    [{{ $outflow->paid_at?->format('d/m/Y') ?: $outflow->due_date?->format('d/m/Y') }}]
                                    {{ $outflow->description }} (R$ {{ number_format($outflow->amount, 2, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <span class="label-text-alt opacity-70 mt-1">
                            Utilize para registrar quando um valor que saiu da empresa (ex: empréstimo) está retornando.
                        </span>
                        @if ($linkable_id)
                            <input type="hidden" wire:model="linkable_type" value="App\Models\Outflow">
                        @endif
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

                    <div class="col-span-1 md:col-span-2 flex flex-col sm:flex-row gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Valor Imposto</span></label>
                            <div class="p-3 bg-base-200 rounded-lg text-error font-mono font-bold text-sm truncate">
                                R$ {{ number_format($gross_amount * ($tax_percentage / 100), 2, ',', '.') }}
                            </div>
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-bold">Valor Líquido Efetivo</span></label>
                            <div class="p-3 bg-base-200 rounded-lg text-success font-mono font-bold text-sm truncate">
                                R$ {{ number_format(((float) $gross_amount + (float) $adjustment_amount) - ($gross_amount * ($tax_percentage / 100)), 2, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="divider md:col-span-2 italic text-warning/40 text-sm font-bold uppercase">Ajuste de Fluxo (Correção)</div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text group inline-flex items-center gap-1.5">
                            Valor do Ajuste
                            <div class="tooltip tooltip-right" data-tip="Valor positivo (+) para aumentar ou negativo (-) para diminuir o total real. Não afeta impostos.">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </span></label>
                        <div class="join w-full">
                            <span class="join-item btn btn-disabled">R$</span>
                            <input type="number" step="0.01" wire:model.live="adjustment_amount"
                                class="input input-bordered w-full join-item" @disabled($readOnly) />
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Justificativa do Ajuste</span></label>
                        <input type="text" wire:model="adjustment_reason" class="input input-bordered w-full"
                            placeholder="Ex: Diferença de câmbio, tarifa bancária..." @disabled($readOnly) />
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text">Anotações</span></label>
                        <textarea wire:model="notes" class="textarea textarea-bordered w-full " placeholder="Algum detalhe extra?"
                            @disabled($readOnly)></textarea>
                    </div>
                </div>

                <div class="modal-action">
                    @if ($readOnly && $revenueId && $invoice_id && $gross_amount * ($tax_percentage / 100) > 0 && ! $hasExpenditure)
                        <a href="{{ route('financial.expenditures', [
                            'launch' => 'tax',
                            'fromModelType' => 'App\Models\Invoice',
                            'fromModelId' => $invoice_id,
                            'launchAmount' => $gross_amount * ($tax_percentage / 100),
                            'launchDescription' => 'Imposto Ref. ' . $description . ' ' . \Carbon\Carbon::parse($paid_at ?? ($due_date ?? now()))->addMonth()->format('m/Y') . ' (' . \Carbon\Carbon::parse($paid_at ?? ($due_date ?? now()))->format('m/Y') . ')'
                        ]) }}"
                            wire:navigate class="btn btn-warning gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                            </svg>
                            Lançar DAS
                        </a>
                    @endif
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
