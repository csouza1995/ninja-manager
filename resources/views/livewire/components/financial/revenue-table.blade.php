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

            <select wire:model.live="status" class="select select-sm select-bordered bg-base-100">
                <option value="">Status</option>
                <option value="pending">Pendente</option>
                <option value="paid">Recebido</option>
            </select>

            <input type="text" wire:model.live.debounce.300ms="classification"
                class="input input-sm input-bordered bg-base-100" placeholder="Classificação..." />

            <select wire:model.live="bank_account_id" class="select select-sm select-bordered bg-base-100">
                <option value="">Conta Bancária</option>
                @foreach ($settingsBankAccounts as $bank)
                    <option value="{{ $bank->id }}">{{ $bank->nickname ?: $bank->bank_name }}</option>
                @endforeach
            </select>

            <div class="join">
                <span class="join-item btn btn-sm btn-disabled bg-base-100 border-base-300">Inicial</span>
                <input type="date" wire:model.live="start_date"
                    class="input input-sm input-bordered join-item bg-base-100" />
                <span class="join-item btn btn-sm btn-disabled bg-base-100 border-base-300 border-l-0">Final</span>
                <input type="date" wire:model.live="end_date"
                    class="input input-sm input-bordered join-item bg-base-100" />
            </div>
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('due_date')">
                        <div class="flex items-center gap-1">
                            Vencimento
                            @if ($sortField === 'due_date')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('description')">
                        <div class="flex items-center gap-1">
                            Descrição / Origem
                            @if ($sortField === 'description')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Nota Fiscal</th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('classification')">
                        <div class="flex items-center gap-1">
                            Classificação
                            @if ($sortField === 'classification')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('amount')">
                        <div class="flex items-center gap-1">
                            Valor Bruto
                            @if ($sortField === 'amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('tax_amount')">
                        <div class="flex items-center gap-1">
                            Imposto
                            @if ($sortField === 'tax_amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('net_amount')">
                        <div class="flex items-center gap-1">
                            Valor Liq.
                            @if ($sortField === 'net_amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>Banco</th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('paid_at')">
                        <div class="flex items-center gap-1">
                            Status
                            @if ($sortField === 'paid_at')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
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
                                @if ($revenue->service)
                                    <a href="{{ route('clients.index', ['showId' => $revenue->service->client_id]) }}"
                                        class="link link-hover text-primary"
                                        title="Ver Cliente">{{ $revenue->service->client->name }}</a>
                                @else
                                    {{ $revenue->origin_name ?: '---' }}
                                @endif
                            </div>
                        </td>
                        <td>
                            @if ($revenue->invoice)
                                <a href="{{ route('financial.invoices', ['showId' => $revenue->invoice->id]) }}"
                                    class="badge badge-xs badge-primary hover:scale-105 transition-transform"
                                    title="Visualizar Nota Fiscal" wire:navigate>
                                    NF: {{ $revenue->invoice->number }}
                                </a>
                            @else
                                <span class="text-xs opacity-30">---</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap">
                            <span class="badge badge-outline">{{ $revenue->classification }}</span>
                        </td>
                        <td class="font-mono font-bold opacity-70">
                            R$ {{ number_format($revenue->gross_amount, 2, ',', '.') }}
                        </td>
                        <td class="font-mono font-bold text-error/70">
                            @if ($revenue->tax_amount > 0)
                                @if ($revenue->expenditure)
                                    <a href="{{ route('financial.expenditures', ['showId' => $revenue->expenditure->id]) }}"
                                        class="link link-hover text-error/70" title="Ver Despesa Lançada"
                                        wire:navigate>
                                        R$ {{ number_format($revenue->tax_amount, 2, ',', '.') }}
                                    </a>
                                @else
                                    R$ {{ number_format($revenue->tax_amount, 2, ',', '.') }}
                                @endif
                            @else
                                <span class="opacity-30">---</span>
                            @endif
                        </td>
                        <td class="font-mono font-bold text-success">
                            <div class="flex items-center gap-1">
                                R$ {{ number_format($revenue->net_amount, 2, ',', '.') }}
                                @if ($revenue->adjustment_amount != 0)
                                    <div class="tooltip tooltip-left"
                                        data-tip="Ajuste: R$ {{ number_format($revenue->adjustment_amount, 2, ',', '.') }} - {{ $revenue->adjustment_reason }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-warning"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="whitespace-nowrap">
                            <a href="{{ route('financial.bank-accounts', ['showId' => $revenue->bank_account_id]) }}"
                                class="link link-hover" title="Ver Conta">
                                {{ $revenue->bankAccount->nickname ?: $revenue->bankAccount->bank_name }}
                            </a>
                        </td>
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
                                </button>
                                <!-- Expenditure Link (Only if revenue has tax and no expenditure yet) -->
                                @if ($revenue->tax_amount > 0 && !$revenue->expenditure)
                                    <a href="{{ route('financial.expenditures', [
                                        'launch' => 'tax',
                                        'fromModelType' => 'App\Models\Revenue',
                                        'fromModelId' => $revenue->id,
                                        'launchAmount' => $revenue->tax_amount,
                                    ]) }}"
                                        wire:navigate class="btn btn-square btn-ghost btn-xs text-warning"
                                        title="Lançar Despesa de Imposto">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                    </a>
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
