<div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Executante / Função</th>
                    <th>Status Principal</th>
                    <th>Status Extras</th>
                    <th>Total</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
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
                                    'negotiating' => 'badge-neutral',
                                    'cancelled' => 'badge-error',
                                    'in_progress' => 'badge-info',
                                    'delivered' => 'badge-primary',
                                    'finalized' => 'badge-success',
                                ];
                                $statusLabels = [
                                    'negotiating' => 'Negociando',
                                    'cancelled' => 'Cancelado',
                                    'in_progress' => 'Em andamento',
                                    'delivered' => 'Entregue',
                                    'finalized' => 'Finalizado',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$service->status] ?? '' }}">
                                {{ $statusLabels[$service->status] ?? $service->status }}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @if ($service->is_paid)
                                    <span class="badge badge-success badge-sm" title="Pago">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Pago
                                    </span>
                                @endif
                                @if ($service->is_documented)
                                    <span class="badge badge-info badge-sm" title="Com Recibo">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Recibo
                                    </span>
                                @endif
                                @if ($service->is_invoiced)
                                    <span class="badge badge-primary badge-sm" title="Faturado (NF)">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        NF
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="font-bold">R$ {{ number_format($service->total, 2, ',', '.') }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                @if ($service->status !== 'cancelled')
                                    @if ($service->is_documented && $service->receipt)
                                        <a href="{{ route('receipts.print', $service->receipt) }}" target="_blank"
                                            class="btn btn-square btn-ghost btn-sm text-success"
                                            title="Visualizar Recibo">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    @else
                                        <button
                                            wire:click="$dispatch('generate-receipt', { serviceId: {{ $service->id }} })"
                                            class="btn btn-square btn-ghost btn-sm text-info" title="Gerar Recibo">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    @endif
                                @endif

                                <button wire:click="$dispatch('edit-service', { id: {{ $service->id }} })"
                                    class="btn btn-square btn-ghost btn-sm" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                @if ($service->canDelete())
                                    <button wire:click="$dispatch('delete-service', { id: {{ $service->id }} })"
                                        class="btn btn-square btn-ghost btn-sm text-error" title="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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
