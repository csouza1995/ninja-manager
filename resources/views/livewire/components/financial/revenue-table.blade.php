<div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Vencimento</th>
                    <th>Descrição / Origem</th>
                    <th>Classificação</th>
                    <th>Valor Bruto</th>
                    <th>Imposto</th>
                    <th>Valor Liq.</th>
                    <th>Banco</th>
                    <th>Status</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenues as $revenue)
                    <tr>
                        <td class="whitespace-nowrap">{{ $revenue->due_date->format('d/m/Y') }}</td>
                        <td>
                            <div class="font-bold">{{ $revenue->description }}</div>
                            <div class="text-xs opacity-50">
                                {{ $revenue->origin_name ?: ($revenue->service ? $revenue->service->client->name : '---') }}
                                @if ($revenue->invoice)
                                    <span class="badge badge-xs badge-ghost ml-2">NF:
                                        {{ $revenue->invoice->number }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="badge badge-outline">{{ $revenue->classification }}</span>
                        </td>
                        <td class="font-mono font-bold opacity-70">
                            R$ {{ number_format($revenue->gross_amount, 2, ',', '.') }}
                        </td>
                        <td class="font-mono font-bold text-error/70">
                            R$ {{ number_format($revenue->tax_amount, 2, ',', '.') }}
                        </td>
                        <td class="font-mono font-bold text-success">
                            R$ {{ number_format($revenue->net_amount, 2, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap">
                            {{ $revenue->bankAccount->nickname ?: $revenue->bankAccount->bank_name }}</td>
                        <td>
                            @if ($revenue->paid_at)
                                <span class="badge badge-success">Pago</span>
                            @else
                                <span class="badge badge-warning">Pendente</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <!-- View Revenue -->
                                <button
                                    wire:click="$dispatch('open-revenue-form', { id: {{ $revenue->id }}, readOnly: true })"
                                    class="btn btn-square btn-ghost btn-xs text-primary" title="Visualizar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <!-- Expenditure Link -->
                                    @if ($revenue->tax_amount > 0)
                                        @if (!$revenue->expenditure)
                                            <button
                                                wire:click="$dispatch('open-expenditure-form', { fromModelType: 'App\\Models\\Revenue', fromModelId: {{ $revenue->id }}, amount: {{ $revenue->tax_amount }}, description: '{{ addslashes('Imposto Ref. ' . $revenue->description) }}' })"
                                                class="btn btn-square btn-ghost btn-xs text-warning"
                                                title="Lançar Despesa de Imposto">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                                </svg>
                                            </button>
                                        @endif
                                    @endif

                                    <button wire:click="$dispatch('open-revenue-form', { id: {{ $revenue->id }} })"
                                        class="btn btn-square btn-ghost btn-xs" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    <button wire:click="delete({{ $revenue->id }})" wire:confirm="Tem certeza?"
                                        class="btn btn-square btn-ghost btn-xs text-error" title="Excluir">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.34 7m-4.74 0-.34-7m10 4.634V20a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6.634m12 0a2 2 0 0 0-2-2h-3.366a2 2 0 0 0-1.268.464L9.08 6.634a2 2 0 0 0-2 2h10.74Z" />
                                        </svg>
                                    </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 opacity-50">Nenhuma receita registrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $revenues->links() }}
    </div>
</div>
