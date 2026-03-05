<div>
    @if ($showFilters)
        <div class="flex flex-wrap items-center gap-2 mb-6 p-2 bg-base-100/50 rounded-box border border-base-200">
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
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
            </select>

            <div class="join">
                <span class="join-item btn btn-sm btn-disabled bg-base-100 border-base-300">Emissão</span>
                <input type="date" wire:model.live="dateRange"
                    class="input input-sm input-bordered join-item bg-base-100" />
            </div>
        </div>
    @endif
    <div class="overflow-hidden">
        <table class="table table-zebra w-full">
            <thead>
                <tr>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('issued_at')">
                        <div class="flex items-center gap-1">
                            Emissão
                            @if ($sortField === 'issued_at')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Número</th>
                    <th>Cliente</th>
                    <th>Serviço Vinculado</th>
                    <th>Chave de Acesso</th>
                    <th class="cursor-pointer hover:bg-base-200" wire:click="sortBy('amount')">
                        <div class="flex items-center gap-1">
                            Valor
                            @if ($sortField === 'amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr class="hover">
                        <td class="font-mono text-xs">{{ $invoice->issued_at->format('d/m/Y') }}</td>
                        <td>
                            @if ($invoice->number)
                                <div class="tooltip tooltip-right"
                                    data-tip="{{ \Illuminate\Support\Str::limit($invoice->description ?? 'Sem descrição', 50) }}">
                                    <span class="badge badge-outline badge-sm cursor-help">{{ $invoice->number }}</span>
                                </div>
                            @else
                                <span class="opacity-30">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($invoice->client)
                                <div class="flex flex-col">
                                    <span class="font-bold text-sm">{{ $invoice->client->name }}</span>
                                    <span
                                        class="text-xs opacity-50">{{ $invoice->client->document ?? 'Sem documento' }}</span>
                                </div>
                            @else
                                <span class="text-xs italic opacity-40">Cliente não associado</span>
                            @endif
                        </td>
                        <td>
                            @if ($invoice->service)
                                <a href="{{ route('services.index', ['service' => $invoice->service_id]) }}"
                                    class="link link-primary font-bold text-sm">
                                    #{{ str_pad($invoice->service->id, 5, '0', STR_PAD_LEFT) }}
                                </a>
                                <div class="text-[10px] opacity-70 w-32 truncate">
                                    {{ $invoice->service->role->name ?? 'Serviço' }}</div>
                            @else
                                <span class="text-xs italic opacity-40">Avulsa</span>
                            @endif
                        </td>
                        <td>
                            @if ($invoice->access_key)
                                <a href="https://www.nfse.gov.br/ConsultaPublica/?tpc=1&chave={{ $invoice->access_key }}"
                                    target="_blank"
                                    class="text-[10px] opacity-70 hover:opacity-100 truncate w-32 inline-block link link-primary font-mono"
                                    title="Consultar Chave no Portal Nacional">
                                    {{ $invoice->access_key }}
                                </a>
                            @else
                                <span class="text-[10px] opacity-30">-</span>
                            @endif
                        </td>
                        <td class="font-bold font-mono text-primary">
                            R$ {{ number_format($invoice->amount, 2, ',', '.') }}
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button
                                    wire:click="$dispatch('open-invoice-form', { id: {{ $invoice->id }}, readOnly: true })"
                                    class="btn btn-square btn-ghost btn-xs text-primary" title="Visualizar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>

                                <!-- PDF Button -->
                                @if ($invoice->hasMedia('pdf_files'))
                                    <a href="{{ $invoice->getFirstMediaUrl('pdf_files') }}" target="_blank"
                                        class="btn btn-square btn-ghost btn-xs text-error" title="Visualizar PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </a>
                                @endif

                                <!-- XML Button -->
                                @if ($invoice->hasMedia('xml_files'))
                                    <a href="{{ $invoice->getFirstMediaUrl('xml_files') }}" target="_blank"
                                        class="btn btn-square btn-ghost btn-xs text-info" title="Visualizar XML">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                                        </svg>
                                    </a>
                                @endif
                                <button wire:click="$dispatch('open-invoice-form', { id: {{ $invoice->id }} })"
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
                        <td colspan="7" class="text-center py-8 opacity-50">Nenhuma NF registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
