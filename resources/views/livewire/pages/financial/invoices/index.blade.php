<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-primary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
                Controle de Notas Fiscais (NF)
            </h1>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nova NF
            </button>
        </div>

        <div class="bg-base-100 rounded-box shadow-xl overflow-hidden border border-base-200">
            <table class="table table-zebra w-full">
                <thead class="bg-base-200">
                    <tr>
                        <th>Emissão</th>
                        <th>Número</th>
                        <th>Cliente / Serviço</th>
                        <th>Valor</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td class="font-mono text-xs">{{ $invoice->issued_at->format('d/m/Y') }}</td>
                            <td>
                                @if ($invoice->number)
                                    <span class="badge badge-outline badge-sm">{{ $invoice->number }}</span>
                                @else
                                    <span class="opacity-30">-</span>
                                @endif
                                @if ($invoice->access_key)
                                    <div class="text-[10px] opacity-50 truncate max-w-[150px]"
                                        title="{{ $invoice->access_key }}">
                                        {{ $invoice->access_key }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($invoice->service)
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm">{{ $invoice->service->client->name }}</span>
                                        <span class="text-xs opacity-50">Sérvico #{{ $invoice->service->id }} -
                                            {{ $invoice->service->description }}</span>
                                    </div>
                                @else
                                    <span class="text-xs italic opacity-40">Avulsa</span>
                                @endif
                            </td>
                            <td class="font-bold font-mono text-primary">
                                R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                            </td>
                            <td class="text-right flex justify-end gap-2">
                                <button wire:click="edit({{ $invoice->id }})" class="btn btn-ghost btn-xs text-info">
                                    Editar
                                </button>
                                <button wire:confirm="Tem certeza que deseja excluir esta NF?"
                                    wire:click="delete({{ $invoice->id }})" class="btn btn-ghost btn-xs text-error">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 opacity-50">Nenhuma NF registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 bg-base-200">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    <div @class(['modal', 'modal-open' => $showModal])>
        <div class="modal-box max-w-2xl border border-primary/20">
            <h3 class="font-bold text-lg mb-6 flex items-center gap-2 text-primary">
                {{ $editingId ? 'Editar Nota Fiscal' : 'Registrar Nova Nota Fiscal' }}
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
                    <input type="text" wire:model="access_key" class="input input-bordered w-full font-mono text-sm"
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
                                ({{ str_limit($service->description, 20) }})</option>
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
                <button wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                <button wire:click="save" class="btn btn-primary px-8">
                    {{ $editingId ? 'Atualizar NF' : 'Salvar NF' }}
                </button>
            </div>
        </div>
    </div>
</div>
