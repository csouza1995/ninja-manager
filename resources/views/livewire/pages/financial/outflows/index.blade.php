<div>
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-primary">Saídas & Retiradas</h2>
        <p class="mt-1 text-sm text-base-content/60">Controle retiradas de pró-labore, lucros e investimentos</p>
    </div>

    <div class="card bg-base-200 border border-base-300 shadow-xl">
        <div class="card-body">
            <div class="flex justify-between items-center mb-6 gap-4">
                <div class="flex-1">
                </div>
                <button wire:click="openCreateModal" class="btn btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Nova Saída
                </button>
            </div>

            <div class="overflow-hidden">
                <table class="table w-full">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Descrição / Favorecido</th>
                            <th>Banco</th>
                            <th>Valor</th>
                            <th>Imposto (Provisão)</th>
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
                                        <span
                                            class="text-xs font-mono font-bold">{{ $outflow->originBankAccount->name }}</span>
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
                                        <span
                                            class="text-[9px]">({{ number_format($outflow->tax_percentage, 0) }}%)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($outflow->paid_at)
                                        <div class="badge badge-success badge-outline gap-1 text-[10px] h-auto py-1">
                                            EFETUADO
                                        </div>
                                    @else
                                        <div
                                            class="badge badge-warning badge-outline gap-1 text-[10px] h-auto py-1 italic">
                                            PREVISTO
                                        </div>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-1">
                                        <button wire:click="edit({{ $outflow->id }})"
                                            class="btn btn-ghost btn-xs">Editar</button>
                                        <button wire:confirm="Excluir este registro?"
                                            wire:click="delete({{ $outflow->id }})"
                                            class="btn btn-ghost btn-xs text-error">Excluir</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 opacity-50 italic">Nenhuma saída registrada.
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
    </div>

    <!-- Modal Form -->
    <div @class(['modal', 'modal-open' => $showModal])>
        <div class="modal-box max-w-2xl border border-primary/20">
            <h3 class="font-bold text-lg mb-6 flex items-center gap-2">
                <span class="text-primary">{{ $editingId ? 'Editar Registro' : 'Registrar Nova Saída' }}</span>
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Tipo de Saída</span></label>
                    <select wire:model.live="type" class="select select-bordered w-full">
                        <option value="Prolabore">Pró-labore</option>
                        <option value="Lucro/Dividendos">Distribuição de Lucros</option>
                        <option value="Investimento">Investimento</option>
                        <option value="Transferência">Transferência entre Contas</option>
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text font-bold text-primary">Valor Bruto</span></label>
                    <div class="join">
                        <span class="join-item btn btn-disabled">R$</span>
                        <input type="number" step="0.01" wire:model.live="amount"
                            class="input input-bordered w-full join-item font-mono" placeholder="0.00" />
                    </div>
                </div>

                @if ($type === 'Prolabore')
                    <div class="form-control md:col-span-2 bg-error/5 p-3 rounded-lg border border-error/10 mb-2">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-xs font-bold text-error uppercase">Provisão de Imposto (11% cap R$
                                932)</span>
                            <span class="font-mono text-error font-black">R$
                                {{ number_format($tax_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <label class="label py-0"><span class="text-[10px] opacity-60">Alíquota
                                        (%)</span></label>
                                <input type="number" step="0.5" wire:model.live="tax_percentage"
                                    class="input input-bordered input-xs w-full font-mono" />
                            </div>
                            <div class="flex-1 text-right">
                                <label class="label py-0 justify-end"><span class="text-[10px] opacity-60">Valor
                                        Imposto</span></label>
                                <div class="text-sm font-mono font-bold mt-1">R$
                                    {{ number_format($tax_amount, 2, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="form-control md:col-span-2">
                    <label class="label"><span class="label-text">Descrição</span></label>
                    <input type="text" wire:model="description" class="input input-bordered w-full"
                        placeholder="Ex: Retirada mensal para despesas pessoais" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Conta Bancária</span></label>
                    <select wire:model="origin_bank_account_id" class="select select-bordered w-full font-mono text-sm">
                        <option value="">--- Selecione a Conta ---</option>
                        @foreach ($bankAccounts as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->nickname ?: $bank->bank_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">
                            {{ $type === 'Transferência' ? 'Banco Destino' : 'Favorecido (Pessoa)' }}
                        </span></label>

                    @if ($type === 'Transferência')
                        <select wire:model="destination_bank_account_id"
                            class="select select-bordered w-full font-mono text-sm">
                            <option value="">--- Selecione Destino ---</option>
                            @foreach ($bankAccounts as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nickname ?: $bank->bank_name }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" wire:model="person_name" list="people-suggestions"
                            class="input input-bordered w-full" placeholder="Ninja Admin" />
                        <datalist id="people-suggestions">
                            @foreach ($peopleSuggestions as $name)
                                <option value="{{ $name }}">
                            @endforeach
                        </datalist>
                    @endif
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Data Prevista</span></label>
                    <input type="date" wire:model="due_date" class="input input-bordered w-full" />
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Data Realizada (Efetivação)</span></label>
                    <input type="date" wire:model="paid_at" class="input input-bordered w-full" />
                    <span class="label-text-alt opacity-50 mt-1">Deixe vazio se for apenas agendamento</span>
                </div>
            </div>

            <div class="modal-action">
                <button wire:click="$set('showModal', false)" class="btn btn-ghost">Cancelar</button>
                <button wire:click="save" class="btn btn-primary px-8">Salvar Saída</button>
            </div>
        </div>
    </div>
</div>
