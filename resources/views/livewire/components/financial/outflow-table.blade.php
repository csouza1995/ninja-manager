<div>
    <div class="flex flex-wrap items-center gap-2 mb-6 p-2 bg-base-100/50 rounded-box border border-base-200">
        <span class="text-sm font-medium text-base-content/70 px-2 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
            </svg>
            Filtros
        </span>

        <select wire:model.live="status" class="select select-sm select-bordered bg-base-100">
            <option value="">Status</option>
            <option value="pending">Pendente</option>
            <option value="paid">Pago</option>
        </select>

        <select wire:model.live="type" class="select select-sm select-bordered bg-base-100">
            <option value="">Tipo</option>
            <option value="imposto">Imposto</option>
            <option value="prolabore">Pró-labore</option>
            <option value="distribuicao">Distribuição</option>
            <option value="rescisao">Rescisão</option>
            <option value="bonus">Bônus</option>
            <option value="ajuste">Ajuste</option>
            <option value="outro">Outro</option>
        </select>

        <select wire:model.live="owner" class="select select-sm select-bordered bg-base-100">
            <option value="">Favorecido</option>
            <option value="other">Outro Favorecido</option>
        </select>

        <select wire:model.live="bank" class="select select-sm select-bordered bg-base-100">
            <option value="">Conta Bancária</option>
            @foreach ($this->bankAccounts as $bankAccount)
                <option value="{{ $bankAccount->id }}">{{ $bankAccount->bank_name }}</option>
            @endforeach
        </select>

        <div class="join">
            <span class="join-item btn btn-sm btn-disabled bg-base-100 border-base-300">Data</span>
            <input type="date" wire:model.live="dateRange"
                class="input input-sm input-bordered join-item bg-base-100" />
        </div>
    </div>

    <div class="overflow-hidden">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="cursor-pointer hover:bg-base-200" wire:click="sortBy('due_date')">
                        <div class="flex items-center gap-1">
                            Data
                            @if ($sortField === 'due_date')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Tipo</th>
                    <th>Descrição / Favorecido</th>
                    <th>Banco</th>
                    <th class="cursor-pointer hover:bg-base-200" wire:click="sortBy('amount')">
                        <div class="flex items-center gap-1">
                            Valor
                            @if ($sortField === 'amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Imposto (Provisão)</th>
                    <th>Valor Efetivo</th>
                    <th>Status</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($outflows as $outflow)
                    <tr>
                        <td class="font-mono text-xs">
                            {{ $outflow->due_date->format('d/m/Y') }}
                        </td>
                        <td>
                            <span @class([
                                'badge badge-sm font-bold truncate max-w-[120px]',
                                'badge-error' => $outflow->type === 'Prolabore',
                                'badge-warning' => $outflow->type === 'Lucro/Dividendos',
                                'badge-info' => $outflow->type === 'Transferência',
                                'badge-secondary' => $outflow->type === 'Investimento',
                            ])>
                                {{ $outflow->type }}
                            </span>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-sm font-bold">{{ $outflow->description }}</span>
                                @if ($outflow->person_name)
                                    <span class="text-[10px] opacity-50 uppercase tracking-wider">Favorecido:
                                        {{ $outflow->person_name }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="flex flex-col">
                                <span class="text-xs font-mono font-bold">{{ $outflow->originBankAccount->name }}</span>
                                @if ($outflow->type === 'Transferência' && $outflow->destinationBankAccount)
                                    <span class="text-[10px] opacity-40">➔
                                        {{ $outflow->destinationBankAccount->name }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="font-bold font-mono text-error">
                            R$ {{ number_format($outflow->amount, 2, ',', '.') }}
                        </td>
                        <td class="font-mono text-xs text-error/60 italic">
                            @if ($outflow->tax_amount > 0)
                                R$ {{ number_format($outflow->tax_amount, 2, ',', '.') }}
                                <span class="text-[9px]">({{ number_format($outflow->tax_percentage, 0) }}%)</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="font-bold font-mono text-error">
                            R$ {{ number_format($outflow->amount - $outflow->tax_amount, 2, ',', '.') }}
                        </td>
                        <td>
                            @if ($outflow->paid_at)
                                <div class="badge badge-success badge-outline gap-1 text-[10px] h-auto py-1">
                                    EFETUADO
                                </div>
                            @else
                                <div class="badge badge-warning badge-outline gap-1 text-[10px] h-auto py-1 italic">
                                    PREVISTO
                                </div>
                            @endif
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <!-- Expenditure Link -->
                                @if ($outflow->tax_amount > 0)
                                    @if (!$outflow->expenditure)
                                        <a href="{{ route('financial.expenditures', ['launch' => 'tax', 'fromModelType' => 'App\Models\Outflow', 'fromModelId' => $outflow->id, 'launchAmount' => $outflow->tax_amount, 'launchDescription' => 'Imposto Ref. ' . $outflow->description]) }}"
                                            wire:navigate class="btn btn-square btn-ghost btn-xs text-warning"
                                            title="Lançar Despesa de Imposto">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                            </svg>
                                        </a>
                                    @endif
                                @endif

                                <button wire:click="$dispatch('open-outflow-form', { id: {{ $outflow->id }} })"
                                    class="btn btn-square btn-ghost btn-xs" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button wire:confirm="Excluir este registro?" wire:click="delete({{ $outflow->id }})"
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
                        <td colspan="9" class="text-center py-8 opacity-50 italic">Nenhuma saída registrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $outflows->links() }}
    </div>
</div>
