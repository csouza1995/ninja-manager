<div>
    @if ($isOpen)
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
                    <button wire:click="close" class="btn">Cancelar</button>
                    <button wire:click="save" class="btn btn-primary">Salvar</button>
                </div>
            </div>
        </div>
    @endif
</div>
