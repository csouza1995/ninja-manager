<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl border border-primary/20">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                    <span class="text-primary">
                        @if ($readOnly)
                            Visualizar Registro
                        @else
                            {{ $outflowId ? 'Editar Registro' : 'Registrar Nova Saída' }}
                        @endif
                    </span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Tipo de Saída</span></label>
                        <select wire:model.live="type" class="select select-bordered w-full" @disabled($readOnly)>
                            <option value="Prolabore">Pró-labore</option>
                            <option value="Lucro/Dividendos">Distribuição de Lucros</option>
                            <option value="Investimento">Investimento</option>
                            <option value="Transferência">Transferência entre Contas</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold text-primary">Valor Bruto</span></label>
                        <div class="join">
                            <span class="join-item btn btn-disabled">R$</span>
                            <input type="number" step="0.01" wire:model.live="amount"
                                class="input input-bordered w-full join-item font-mono" placeholder="0.00"
                                @disabled($readOnly) />
                        </div>
                        @error('amount')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    @if ($type === 'Prolabore')
                        <div class="form-control md:col-span-2 bg-error/5 p-3 rounded-lg border border-error/10 mb-2">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-bold text-error uppercase">Provisão de Imposto (11% cap R$
                                    932)</span>
                                <span class="font-mono text-error font-black">R$
                                    {{ number_format($tax_amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="label py-0"><span class="text-[10px] opacity-60">Alíquota
                                            (%)</span></label>
                                    <input type="number" step="0.5" wire:model.live="tax_percentage"
                                        class="input input-bordered input-xs w-full font-mono"
                                        @disabled($readOnly) />
                                </div>
                                <div class="flex-1 text-right">
                                    <label class="label py-0 justify-end"><span class="text-[10px] opacity-60">Valor
                                            Imposto</span></label>
                                    <div class="text-sm font-mono font-bold mt-1">R$
                                        {{ number_format($tax_amount, 2, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text">Descrição</span></label>
                        <input type="text" wire:model="description" class="input input-bordered w-full"
                            placeholder="Ex: Retirada mensal para despesas pessoais" @disabled($readOnly) />
                        @error('description')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Conta Bancária</span></label>
                        <select wire:model="origin_bank_account_id"
                            class="select select-bordered w-full font-mono text-sm" @disabled($readOnly)>
                            <option value="">--- Selecione a Conta ---</option>
                            @foreach ($bankAccounts as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nickname ?: $bank->bank_name }}</option>
                            @endforeach
                        </select>
                        @error('origin_bank_account_id')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">
                                {{ $type === 'Transferência' ? 'Banco Destino' : 'Favorecido (Pessoa)' }}
                            </span></label>

                        @if ($type === 'Transferência')
                            <select wire:model="destination_bank_account_id"
                                class="select select-bordered w-full font-mono text-sm" @disabled($readOnly)>
                                <option value="">--- Selecione Destino ---</option>
                                @foreach ($bankAccounts as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->nickname ?: $bank->bank_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('destination_bank_account_id')
                                <span class="text-error text-xs mt-1">{{ $message }}</span>
                            @enderror
                        @else
                            <input type="text" wire:model="person_name" list="people-suggestions"
                                class="input input-bordered w-full" placeholder="Ninja Admin"
                                @disabled($readOnly) />
                            <datalist id="people-suggestions">
                                @foreach ($peopleSuggestions as $name)
                                    <option value="{{ $name }}">
                                @endforeach
                            </datalist>
                            @error('person_name')
                                <span class="text-error text-xs mt-1">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Data Prevista</span></label>
                        <input type="date" wire:model="due_date" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        @error('due_date')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Data Realizada (Efetivação)</span></label>
                        <input type="date" wire:model="paid_at" class="input input-bordered w-full"
                            @disabled($readOnly) />
                        <span class="label-text-alt opacity-50 mt-1">Deixe vazio se for apenas agendamento</span>
                    </div>

                    <div class="form-control md:col-span-2 bg-base-200 p-3 rounded-lg border border-base-300 mt-2">
                        <label class="label pt-0"><span class="label-text font-bold">Informações Adicionais</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-3">
                                <label class="label py-1"><span class="label-text text-xs">Data de Referência</span></label>
                                <input type="date" wire:model="reference_date" class="input input-bordered w-full" @disabled($readOnly) />
                            </div>
                            <div>
                                <label class="label py-1"><span class="label-text text-xs">Ajuste de Valor</span></label>
                                <input type="number" step="0.01" wire:model.live="adjustment_amount" class="input input-bordered w-full" placeholder="0.00" @disabled($readOnly) />
                            </div>
                            <div class="md:col-span-2">
                                <label class="label py-1"><span class="label-text text-xs">Motivo do Ajuste</span></label>
                                <input type="text" wire:model="adjustment_reason" class="input input-bordered w-full" placeholder="Ex: Taxa bancária" @disabled($readOnly) />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 md:col-span-2 mt-4">
                        <button wire:click="close" class="btn btn-ghost">{{ $readOnly ? 'Fechar' : 'Cancelar' }}</button>
                        @if (!$readOnly)
                            <button wire:click="save" class="btn btn-primary px-8">Salvar Saída</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
