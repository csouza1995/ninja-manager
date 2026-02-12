<div>
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg text-primary mb-4">
                    {{ $clientId ? 'Editar Cliente' : 'Novo Cliente' }}
                </h3>

                <form wire:submit="save">
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Tipo</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="label cursor-pointer gap-2">
                                <input type="radio" wire:model.live="type" value="individual"
                                    class="radio radio-primary" />
                                <span class="label-text">Pessoa Física</span>
                            </label>
                            <label class="label cursor-pointer gap-2">
                                <input type="radio" wire:model.live="type" value="company"
                                    class="radio radio-primary" />
                                <span class="label-text">Pessoa Jurídica</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Nome {{ $type === 'company' ? 'da Empresa' : '' }}</span>
                        </label>
                        <input type="text" wire:model="name"
                            class="input input-bordered w-full @error('name') input-error @enderror" />
                        @error('name')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Apelido</span>
                        </label>
                        <input type="text" wire:model="nickname"
                            class="input input-bordered w-full @error('nickname') input-error @enderror" />
                        @error('nickname')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">{{ $type === 'individual' ? 'CPF' : 'CNPJ' }}</span>
                        </label>
                        <input type="text" wire:model="document"
                            class="input input-bordered w-full @error('document') input-error @enderror" />
                        @error('document')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="divider">Endereço</div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">CEP</span>
                            </label>
                            <input type="text" wire:model="zip_code"
                                class="input input-bordered w-full @error('zip_code') input-error @enderror" />
                            @error('zip_code')
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Número</span>
                            </label>
                            <input type="text" wire:model="number"
                                class="input input-bordered w-full @error('number') input-error @enderror" />
                            @error('number')
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Rua</span>
                        </label>
                        <input type="text" wire:model="street"
                            class="input input-bordered w-full @error('street') input-error @enderror" />
                        @error('street')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Bairro</span>
                        </label>
                        <input type="text" wire:model="neighborhood"
                            class="input input-bordered w-full @error('neighborhood') input-error @enderror" />
                        @error('neighborhood')
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Cidade</span>
                            </label>
                            <input type="text" wire:model="city"
                                class="input input-bordered w-full @error('city') input-error @enderror" />
                            @error('city')
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-control w-full">
                            <label class="label">
                                <span class="label-text">Estado</span>
                            </label>
                            <input type="text" wire:model="state" maxlength="2"
                                class="input input-bordered w-full @error('state') input-error @enderror" />
                            @error('state')
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Complemento</span>
                        </label>
                        <input type="text" wire:model="complement" class="input input-bordered w-full" />
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
