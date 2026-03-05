<div>
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg text-primary mb-4">
                    @if ($readOnly)
                        Visualizar Item
                    @else
                        {{ $itemId ? 'Editar Item' : 'Novo Item' }}
                    @endif
                </h3>

                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Código</span>
                        </label>
                        <input type="text" wire:model="code"
                            class="input input-bordered w-full @error('code') input-error @enderror"
                            @disabled($readOnly) />
                        @error('code')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Descrição</span>
                        </label>
                        <textarea wire:model="description"
                            class="textarea textarea-bordered w-full @error('description') textarea-error @enderror"
                            @disabled($readOnly)></textarea>
                        @error('description')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Valor</span>
                        </label>
                        <input type="number" step="0.01" wire:model="unit_price"
                            class="input input-bordered w-full @error('unit_price') input-error @enderror"
                            @disabled($readOnly) />
                        @error('unit_price')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModal"
                            class="btn btn-ghost">{{ $readOnly ? 'Fechar' : 'Cancelar' }}</button>
                        @if (!$readOnly)
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        @endif
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
