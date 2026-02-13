<div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Vencimento</th>
                    <th>Descrição / Destino</th>
                    <th>Classificação</th>
                    <th>Valor</th>
                    <th>Banco</th>
                    <th>Status</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenditures as $exp)
                    <tr>
                        <td class="whitespace-nowrap">{{ $exp->due_date->format('d/m/Y') }}</td>
                        <td>
                            <div class="font-bold">{{ $exp->description }}</div>
                            <div class="text-xs opacity-50">{{ $exp->destination }}</div>
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="badge badge-outline">{{ $exp->classification }}</span>
                        </td>
                        <td class="font-mono font-bold text-error">
                            R$ {{ number_format($exp->amount, 2, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $exp->bankAccount->nickname ?: $exp->bankAccount->bank_name }}</td>
                        <td>
                            @if ($exp->paid_at)
                                <span class="badge badge-success">Pago</span>
                            @else
                                <span class="badge badge-warning">Pendente</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button wire:click="$dispatch('open-expenditure-form', { id: {{ $exp->id }} })"
                                    class="btn btn-square btn-ghost btn-xs" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $exp->id }})" wire:confirm="Tem certeza?"
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
                        <td colspan="7" class="text-center py-8 opacity-50">Nenhuma despesa registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $expenditures->links() }}
    </div>
</div>
