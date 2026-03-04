<div>
    <div class="flex flex-col gap-4 mb-6">
        <div class="flex justify-between items-center gap-4">
            <div class="flex-1 max-w-md flex items-center gap-2">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por número ou cliente..."
                    class="input input-bordered w-full" />
                <button wire:click="toggleFilters" class="btn btn-ghost btn-sm" title="Alternar Filtros">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                </button>
            </div>
        </div>

        @if ($showFilters)
            <div class="flex flex-wrap items-center gap-2 mt-4 p-2 bg-base-100/50 rounded-box border border-base-200">
                <span class="text-sm font-medium text-base-content/70 px-2 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    Filtros
                </span>

                <select wire:model.live="client_id" class="select select-sm select-bordered bg-base-100">
                    <option value="">Cliente</option>
                    @foreach ($settingsClients as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="status" class="select select-sm select-bordered bg-base-100">
                    <option value="">Status</option>
                    <option value="generated">Gerado (Não Assinado)</option>
                    <option value="signed">Assinado</option>
                    <option value="sent">Enviado</option>
                </select>

                <div class="flex items-center gap-1 ml-auto">
                    <span class="text-xs text-base-content/50">Criado:</span>
                    <input type="date" wire:model.live="start_date"
                        class="input input-sm input-bordered bg-base-100 placeholder-transparent"
                        title="Criado a partir de" />
                    <span class="text-xs text-base-content/50">até</span>
                    <input type="date" wire:model.live="end_date"
                        class="input input-sm input-bordered bg-base-100 placeholder-transparent" title="Até" />
                </div>
            </div>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('receipt_number')">
                        <div class="flex items-center gap-1">
                            Número
                            @if ($sortField === 'receipt_number')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Serviço / Cliente</th>
                    <th>Executante</th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('is_signed')">
                        <div class="flex items-center gap-1">
                            Status
                            @if ($sortField === 'is_signed')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('created_at')">
                        <div class="flex items-center gap-1">
                            Data
                            @if ($sortField === 'created_at')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                    <tr class="hover group">
                        <td class="font-mono font-bold">{{ $receipt->receipt_number }}</td>
                        <td>
                            <div class="font-semibold">Serviço
                                #{{ str_pad($receipt->service_id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs opacity-50">{{ $receipt->service->client->name }}</div>
                        </td>
                        <td>{{ $receipt->service->executor->name }}</td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                <span class="badge badge-info badge-sm">Gerado</span>

                                @if ($receipt->is_signed)
                                    <span class="badge badge-success badge-sm">Assinado</span>
                                @else
                                    <span class="badge badge-ghost badge-sm opacity-50">Não Assinado</span>
                                @endif

                                @if ($receipt->is_sent)
                                    <span class="badge badge-primary badge-sm">Enviado</span>
                                @else
                                    <span class="badge badge-ghost badge-sm opacity-50">Não Enviado</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <!-- Print/Original PDF -->
                                @if (!$receipt->is_signed)
                                    <a href="{{ route('receipts.print', $receipt) }}" target="_blank"
                                        class="btn btn-square btn-ghost btn-xs text-info" title="Imprimir Original">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6.72 13.821V21m0 0-3-3m3 3 3-3M3.36 4.41h17.28a1.157 1.157 0 0 1 1.157 1.157v14.422a1.157 1.157 0 0 1-1.157 1.157H3.36a1.157 1.157 0 0 1-1.157-1.157V5.567A1.157 1.157 0 0 1 3.36 4.41Zm9.566 2.04v5.223a1.157 1.157 0 0 0 1.157 1.157h5.223V6.45h-6.38Z" />
                                        </svg>
                                    </a>
                                @endif

                                <!-- Upload Signed PDF -->
                                @if (!$receipt->is_signed)
                                    <button wire:click="startUpload({{ $receipt->id }})"
                                        class="btn btn-square btn-ghost btn-xs text-warning" title="Upload Assinado">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                        </svg>
                                    </button>
                                @else
                                    <a href="{{ $receipt->getSignedPdfUrl() }}" target="_blank"
                                        class="btn btn-square btn-ghost btn-xs text-success" title="Ver Assinado">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </a>
                                @endif

                                <!-- Mark as Sent -->
                                @if (!$receipt->is_sent)
                                    <button wire:click="markAsSent({{ $receipt->id }})"
                                        class="btn btn-square btn-ghost btn-xs text-primary"
                                        title="Marcar como Enviado">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                                        </svg>
                                    </button>
                                @endif

                                @if (!$receipt->is_sent)
                                    <button wire:click="delete({{ $receipt->id }})"
                                        wire:confirm="Tem certeza que deseja excluir este recibo? O arquivo PDF também será removido."
                                        class="btn btn-square btn-ghost btn-xs text-error" title="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.34 7m-4.74 0-.34-7m10 4.634V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6.634m12 0a2 2 0 0 0-2-2h-3.366a2 2 0 0 0-1.268.464L9.08 6.634a2 2 0 0 0-2 2h10.74Z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8 opacity-50 italic">Nenhum recibo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Upload Modal -->
    @if ($uploadingReceiptId)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg mb-4">Upload de Recibo Assinado</h3>

                <div class="form-control w-full">
                    <label class="label">
                        <span class="label-text">Selecione o arquivo PDF</span>
                    </label>
                    <input type="file" wire:model="file" class="file-input file-input-bordered w-full"
                        accept="application/pdf" />
                    @error('file')
                        <span class="text-error text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <div class="modal-action">
                    <button wire:click="$set('uploadingReceiptId', null)" class="btn">Cancelar</button>
                    <button wire:click="saveUpload" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading wire:target="file" class="loading loading-spinner loading-xs"></span>
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-4">
        {{ $receipts->links() }}
    </div>
</div>
