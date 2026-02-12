<div>
@if ($showModal)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-primary mb-4">
                {{ $executorId ? 'Editar Executante' : 'Novo Executante' }}
            </h3>

            <form wire:submit="save">
                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text">Nome</span>
                    </label>
                    <input type="text" wire:model="name"
                        class="input input-bordered w-full @error('name') input-error @enderror" />
                    @error('name')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text">CPF</span>
                    </label>
                    <input type="text" wire:model="document"
                        class="input input-bordered w-full @error('document') input-error @enderror" />
                    @error('document')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control w-full mb-4">
                    <label class="label">
                        <span class="label-text">Funções</span>
                    </label>
                    <select wire:model="selectedRoles" multiple
                        class="select select-bordered w-full h-32 @error('selectedRoles') select-error @enderror">
                        @foreach ($this->roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <label class="label">
                        <span class="label-text-alt">Segure Ctrl/Cmd para selecionar múltiplas funções</span>
                    </label>
                    @error('selectedRoles')
                        <span class="label-text-alt text-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-action">
                    <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop" wire:click="closeModal"></div>
    </div>
@endif
</div>
