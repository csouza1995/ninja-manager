<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Notas Fiscais</h2>
        <p class="mt-1 text-sm text-base-content/60">Controle a emissão e as chaves de acesso de NFs</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1 max-w-md">
                    <input type="text" wire:model.live="search" placeholder="Buscar por número, chave ou observações..."
                        class="input input-bordered w-full" />
                </div>
                <button wire:click="openCreateModal" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova NF
                </button>
            </div>

            <div class="overflow-hidden">
                <table class="table table-zebra w-full">
                    <thead>
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
                                <td class="text-right">
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $invoice->id }})"
                                            class="btn btn-square btn-ghost btn-xs" title="Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </button>
                                        <button wire:click="delete({{ $invoice->id }})"
                                            wire:confirm="Tem certeza que deseja excluir esta NF?"
                                            class="btn btn-square btn-ghost btn-xs text-error" title="Excluir">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.34 7m-4.74 0-.34-7m10 4.634V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6.634m12 0a2 2 0 0 0-2-2h-3.366a2.268 0 0 0-1.268.464L9.08 6.634a2 2 0 0 0-2 2h10.74Z" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-8 opacity-50">Nenhuma NF registrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
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
                <button wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                <button wire:click="save" class="btn btn-primary px-8">
                    {{ $editingId ? 'Atualizar NF' : 'Salvar NF' }}
                </button>
            </div>
        </div>
    </div>
</div>
