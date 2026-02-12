<div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th>Número</th>
                    <th>Serviço / Cliente</th>
                    <th>Executante</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receipts as $receipt)
                    <tr class="hover">
                        <td class="font-mono font-bold">{{ $receipt->receipt_number }}</td>
                        <td>
                            <div class="font-semibold">Serviço
                                #{{ str_pad($receipt->service_id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs opacity-50">{{ $receipt->service->client->name }}</div>
                        </td>
                        <td>{{ $receipt->service->executor->name }}</td>
                        <td>
                            @php
                                $statusClasses = [
                                    'generated' => 'badge-info',
                                    'signed' => 'badge-success',
                                    'sent' => 'badge-primary',
                                ];
                                $statusLabels = [
                                    'generated' => 'Gerado',
                                    'signed' => 'Assinado',
                                    'sent' => 'Enviado',
                                ];
                            @endphp
                            <span class="badge {{ $statusClasses[$receipt->status] ?? '' }}">
                                {{ $statusLabels[$receipt->status] ?? $receipt->status }}
                            </span>
                        </td>
                        <td>{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('receipts.print', $receipt) }}" target="_blank"
                                    class="btn btn-square btn-ghost btn-sm text-info" title="Imprimir Recibo">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                </a>

                                <button wire:click="delete({{ $receipt->id }})"
                                    wire:confirm="Tem certeza que deseja excluir este recibo? O arquivo PDF também será removido."
                                    class="btn btn-square btn-ghost btn-sm text-error" title="Excluir">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-8">Nenhum recibo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $receipts->links() }}
    </div>
</div>
