<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-6 border-b pb-2">
                    @if ($readOnly)
                        Visualizar Despesa
                    @else
                        {{ $expenditureId ? 'Editar' : 'Nova' }} Despesa
                    @endif
                </h3>

                <div class="grid grid-cols-1 gap-4">

                    <!-- Vínculo (Opcional) -->
                    <div class="grid grid-cols-5 gap-4">
                        <div class="form-control col-span-2">
                            <label class="label"><span class="label-text">Típo de Vínculo</span></label>
                            <select wire:model.live="model_type" class="select select-bordered w-full"
                                @disabled($readOnly)>
                                <option value="">Não Vinculado</option>
                                <option value="App\Models\Revenue">Receita (NF)</option>
                                <option value="App\Models\Outflow">Saída/Retirada</option>
                            </select>
                        </div>
                        <div class="form-control col-span-3">
                            <label class="label"><span class="label-text">Lançamento</span></label>
                            <select wire:model="model_id" class="select select-bordered w-full"
                                {{ !$model_type || $readOnly ? 'disabled' : '' }}>
                                <option value="">Selecione o Item...</option>
                                @foreach ($linkables as $linkable)
                                    @if ($linkable['type'] === $model_type)
                                        <option value="{{ $linkable['id'] }}">{{ $linkable['label'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Favorecido / Destino</span></label>
                        <input type="text" wire:model="destination" class="input input-bordered w-full"
                            list="dest-suggestions" placeholder="Quem recebe?" @disabled($readOnly) />
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
                            placeholder="Do que se trata?" @disabled($readOnly) />
                        @error('description')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Classificação</span></label>
                        <input type="text" wire:model="classification" class="input input-bordered w-full"
                            list="class-suggestions" placeholder="Ex: Material, Aluguel..."
                            @disabled($readOnly) />
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
                        <label class="label"><span class="label-text">Conta de Saída</span></label>
                        <select wire:model="bank_account_id" class="select select-bordered w-full"
                            @disabled($readOnly)>
                            <option value="">--- Selecione ---</option>
                            @foreach ($bankAccounts as $ba)
                                <option value="{{ $ba->id }}">{{ $ba->nickname ?: $ba->bank_name }}</option>
                            @endforeach
                        </select>
                        @error('bank_account_id')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Valor (R$)</span></label>
                            <input type="number" step="0.01" wire:model="amount" class="input input-bordered w-full"
                                @disabled($readOnly) />
                            @error('amount')
                                <span class="text-error text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Vencimento</span></label>
                            <input type="date" wire:model="due_date" class="input input-bordered w-full"
                                @disabled($readOnly) />
                            @error('due_date')
                                <span class="text-error text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Pago em (Opcional)</span></label>
                            <input type="date" wire:model="paid_at" class="input input-bordered w-full"
                                @disabled($readOnly) />
                        </div>

                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Data de Referência</span></label>
                            <input type="date" wire:model="reference_date" class="input input-bordered w-full"
                                @disabled($readOnly) />
                        </div>
                    </div>

                    <div class="border-t pt-4 mt-2">
                        <h4 class="font-semibold text-sm mb-2">Ajustes</h4>
                        <div class="form-control w-full mb-4">
                            <label class="label"><span class="label-text">Valor de Ajuste (Opcional)</span></label>
                            <input type="number" step="0.01" wire:model="adjustment_amount" class="input input-bordered w-full"
                                placeholder="Valor de ajuste ou multa" @disabled($readOnly) />
                        </div>
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text">Justificativa do Ajuste</span></label>
                            <input type="text" wire:model="adjustment_reason" class="input input-bordered w-full"
                                placeholder="Motivo do ajuste" @disabled($readOnly) />
                        </div>
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="close" class="btn">{{ $readOnly ? 'Fechar' : 'Cancelar' }}</button>
                    @if (!$readOnly)
                        <button wire:click="save" class="btn btn-primary px-8">Salvar</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
