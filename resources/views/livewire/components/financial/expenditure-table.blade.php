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
            <option value="overdue">Atrasado</option>
        </select>

        <select wire:model.live="class" class="select select-sm select-bordered bg-base-100">
            <option value="">Classificação</option>
            <option value="services">Serviços</option>
            <option value="products">Produtos</option>
            <option value="other">Outros</option>
        </select>

        <select wire:model.live="destination" class="select select-sm select-bordered bg-base-100">
            <option value="">Destino</option>
            <option value="other">Outro Destino</option>
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
                    <th>Descrição / Destino</th>
                    <th>Classificação</th>
                    <th class="cursor-pointer hover:bg-base-300" wire:click="sortBy('amount')">
                        <div class="flex items-center gap-1">
                            Valor
                            @if ($sortField === 'amount')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
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
