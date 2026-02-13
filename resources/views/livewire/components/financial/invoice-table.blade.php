<div>
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
