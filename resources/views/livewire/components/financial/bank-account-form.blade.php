<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">
                    @if ($readOnly)
                        Visualizar Conta
                    @else
                        {{ $bankAccountId ? 'Editar' : 'Nova' }} Conta
                    @endif
                </h3>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Banco (Nome)</span></label>
                    <input type="text" wire:model="bank_name" class="input input-bordered w-full"
                        placeholder="Ex: Banco do Brasil, Nubank..." @disabled($readOnly) />
                    @error('bank_name')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Titular (Pessoa)</span></label>
                    <input type="text" wire:model="owner_name" class="input input-bordered w-full"
                        placeholder="Nome do Titular" @disabled($readOnly) />
                    @error('owner_name')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Apelido (Opcional)</span></label>
                    <input type="text" wire:model="nickname" class="input input-bordered w-full"
                        placeholder="Ex: Conta Principal, PJ..." @disabled($readOnly) />
                    @error('nickname')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Saldo Inicial</span></label>
                        <input type="number" step="0.01" wire:model="opening_balance" class="input input-bordered w-full"
                            placeholder="0.00" @disabled($readOnly) />
                        @error('opening_balance')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text">Data do Saldo</span></label>
                        <input type="date" wire:model="opening_balance_date" class="input input-bordered w-full"
                             @disabled($readOnly) />
                        @error('opening_balance_date')
                            <span class="text-error text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="close" class="btn">{{ $readOnly ? 'Fechar' : 'Cancelar' }}</button>
                    @if (!$readOnly)
                        <button wire:click="save" class="btn btn-primary">Salvar</button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
