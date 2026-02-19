<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl border border-primary/20">
                <h3 class="font-bold text-lg mb-6 flex items-center gap-2 text-primary">
                    {{ $invoiceId ? 'Editar Nota Fiscal' : 'Registrar Nova Nota Fiscal' }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-control">
                        <label class="label"><span class="label-text">Número da NF</span></label>
                        <input type="text" wire:model="number" class="input input-bordered w-full"
                            placeholder="Ex: 000123" />
                        @error('number')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Data de Emissão</span></label>
                        <input type="date" wire:model="issued_at" class="input input-bordered w-full" />
                        @error('issued_at')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text">Chave de Acesso (44 dígitos)</span></label>
                        <input type="text" wire:model="access_key"
                            class="input input-bordered w-full font-mono text-sm"
                            placeholder="Chave de acesso da NF-e" />
                        @error('access_key')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text font-bold">Valor da Nota</span></label>
                        <div class="join">
                            <span class="join-item btn btn-disabled">R$</span>
                            <input type="number" step="0.01" wire:model="amount"
                                class="input input-bordered w-full join-item" placeholder="0.00" />
                        </div>
                        @error('amount')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label"><span class="label-text">Vincular Serviço</span></label>
                        <select wire:model="service_id" class="select select-bordered w-full">
                            <option value="">NF Avulsa (Sem vínculo)</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">#{{ $service->id }} - {{ $service->client->name }}
                                    ({{ str_limit($service->description, 20) }})
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label"><span class="label-text">Observações</span></label>
                        <textarea wire:model="notes" class="textarea textarea-bordered h-20 w-full" placeholder="Detalhes adicionais..."></textarea>
                        @error('notes')
                            <span class="text-error text-xs p-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="close" class="btn btn-ghost">Cancelar</button>
                    <button wire:click="save" class="btn btn-primary px-8">
                        {{ $invoiceId ? 'Atualizar NF' : 'Salvar NF' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
