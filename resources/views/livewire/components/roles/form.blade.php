<div>
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg text-primary mb-4">
                    @if ($readOnly)
                        Visualizar Função
                    @else
                        {{ $roleId ? 'Editar Função' : 'Nova Função' }}
                    @endif
                </h3>

                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Nome</span>
                        </label>
                        <input type="text" wire:model="name"
                            class="input input-bordered w-full @error('name') input-error @enderror"
                            @disabled($readOnly) />
                        @error('name')
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
