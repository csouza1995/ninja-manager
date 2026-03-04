<div>
    <div class="flex flex-col gap-4 mb-6">
        <div class="flex justify-between items-center gap-4">
            <div class="flex-1 max-w-md flex items-center gap-2">
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por cliente ou executante..." class="input input-bordered w-full" />
                <button wire:click="toggleFilters" class="btn btn-ghost btn-sm" title="Alternar Filtros">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                </button>
            </div>
            <button wire:click="$dispatch('create-service')" type="button" class="btn btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Novo Serviço
            </button>
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
                    <option value="negotiating">Negociando</option>
                    <option value="approved">Aprovado</option>
                    <option value="in_progress">Em andamento</option>
                    <option value="delivered">Entregue</option>
                    <option value="finalized">Finalizado</option>
                    <option value="cancelled">Cancelado</option>
                </select>

                <div class="flex items-center gap-1 ml-auto">
                    <span class="text-xs text-base-content/50">Data:</span>
                    <input type="date" wire:model.live="start_date"
                        class="input input-sm input-bordered bg-base-100 placeholder-transparent" title="A partir de" />
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
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('id')">
                        <div class="flex items-center gap-1">
                            ID
                            @if ($sortField === 'id')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Cliente</th>
                    <th>Executante / Função</th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('status')">
                        <div class="flex items-center gap-1">
                            Status Principal
                            @if ($sortField === 'status')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('is_paid')">
                        <div class="flex items-center gap-1">
                            Status Extras
                            @if ($sortField === 'is_paid')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('total_amount')">
                        <div class="flex items-center gap-1">
                            Total
                            @if ($sortField === 'total_amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                    @php
                        $revenue = \App\Models\Revenue::where('service_id', $service->id)->first();
                    @endphp
                    <tr class="hover">
                        <td class="font-mono text-xs">#{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="font-bold">{{ $service->client->name }}</div>
                            <div class="text-xs opacity-50">{{ $service->client->document }}</div>
                        </td>
                        <td>
                            <div>{{ $service->executor->name }}</div>
                            <div class="text-xs badge badge-ghost">{{ $service->role->name }}</div>
                        </td>
                        <td>
                            @php
                                $statusClasses = [
                                    'negotiating' => 'badge-warning text-warning-content',
                                    'approved' => 'badge-secondary',
                                    'cancelled' => 'badge-error',
                                    'in_progress' => 'badge-info',
                                    'delivered' => 'badge-primary',
                                    'finalized' => 'badge-success',
                                ];
                                $statusLabels = [
                                    'negotiating' => 'Negociando',
                                    'approved' => 'Aprovado',
                                    'cancelled' => 'Cancelado',
                                    'in_progress' => 'Em andamento',
                                    'delivered' => 'Entregue',
                                    'finalized' => 'Finalizado',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$service->status->value] ?? '' }}">
                                {{ $statusLabels[$service->status->value] ?? $service->status->value }}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @if ($service->is_paid)
                                    @if ($service->revenue)
                                        <a href="{{ route('financial.revenues', ['showId' => $service->revenue->id]) }}"
                                            class="badge badge-success badge-sm hover:scale-105 transition-transform"
                                            title="Ver Receita">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Pago
                                        </a>
                                    @else
                                        <span class="badge badge-success badge-sm" title="Pago">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Pago
                                        </span>
                                    @endif
                                @elseif ($service->revenue)
                                    <a href="{{ route('financial.revenues', ['showId' => $service->revenue->id]) }}"
                                        class="badge badge-success badge-outline badge-sm hover:scale-105 transition-transform"
                                        title="Visualizar Receita">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Receita
                                    </a>
                                @endif
                                @if ($service->is_documented && $service->receipt)
                                    <a href="{{ route('receipts.print', $service->receipt) }}" target="_blank"
                                        class="badge badge-info badge-sm hover:scale-105 transition-transform"
                                        title="Ver Recibo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Recibo
                                    </a>
                                @endif
                                @if ($service->revenue?->invoice)
                                    <a href="{{ route('financial.invoices', ['search' => $service->revenue->invoice->number]) }}"
                                        class="badge badge-primary badge-sm" title="Faturado (NF)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        NF
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td class="font-bold">R$ {{ number_format($service->total, 2, ',', '.') }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <!-- Launch Revenue (Shortcut) -->
                                @if (in_array($service->status->value, ['approved', 'in_progress', 'delivered', 'finalized']) && !$service->revenue)
                                    <a href="{{ route('financial.revenues', ['createFromService' => $service->id]) }}"
                                        class="btn btn-square btn-ghost btn-xs text-success" title="Lançar Receita">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </a>
                                @endif

                                <!-- Generate Receipt (First Action) -->
                                @if ($service->status->value !== 'cancelled' && $service->is_paid && !$service->receipt)
                                    <button
                                        wire:click="$dispatch('generate-receipt', { serviceId: {{ $service->id }} })"
                                        class="btn btn-square btn-ghost btn-xs text-info" title="Gerar Recibo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </button>
                                @endif

                                <!-- View Service -->
                                <button wire:click="$dispatch('show-service', { id: {{ $service->id }} })"
                                    class="btn btn-square btn-ghost btn-xs text-primary" title="Visualizar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>

                                <!-- Duplicate Service -->
                                <button wire:click="$dispatch('duplicate-service', { id: {{ $service->id }} })"
                                    class="btn btn-square btn-ghost btn-xs text-warning" title="Duplicar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75" />
                                    </svg>
                                </button>

                                @if ($service->canEdit())
                                    <button wire:click="$dispatch('edit-service', { id: {{ $service->id }} })"
                                        class="btn btn-square btn-ghost btn-xs" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                @endif

                                @if ($service->canDelete())
                                    <button wire:click="$dispatch('delete-service', { id: {{ $service->id }} })"
                                        class="btn btn-square btn-ghost btn-xs text-error" title="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="m14.74 9-.34 7m-4.74 0-.34-7m10 4.634V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6.634m12 0a2 2 0 0 0-2-2h-3.366a2 2 0 0 0-1.268.464L9.08 6.634a2 2 0 0 0-2 2h10.74Z" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-8">Nenhum serviço encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $services->links() }}
    </div>
</div>
