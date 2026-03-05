<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">
                    @if ($readOnly)
                        Visualizar Imposto
                    @else
                        {{ $taxId ? 'Editar' : 'Novo' }} Imposto
                    @endif
                </h3>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Nome do Imposto</span></label>
                    <input type="text" wire:model="name" class="input input-bordered w-full"
                        placeholder="Ex: ISS, Simples Nacional..." @disabled($readOnly) />
                    @error('name')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label"><span class="label-text">Alíquota (%)</span></label>
                    <input type="number" step="0.01" wire:model="percentage" class="input input-bordered w-full"
                        @disabled($readOnly) />
                    @error('percentage')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control mb-4">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="checkbox" wire:model="is_active" class="checkbox checkbox-primary"
                            @disabled($readOnly) />
                        <span class="label-text">Ativo</span>
                    </label>
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
