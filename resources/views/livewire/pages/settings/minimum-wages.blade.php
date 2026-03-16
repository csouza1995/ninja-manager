<div class="p-6">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-primary mb-8 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            Salário Mínimo
        </h1>

        <!-- Tabela -->
        <div class="card bg-base-100 shadow-xl border border-base-200">
            <div class="card-body p-0">
                <div class="flex justify-between items-center p-4 border-b border-base-200">
                    <h2 class="text-lg font-bold opacity-70">Histórico de Valores</h2>
                    <button wire:click="openForm" class="btn btn-primary btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Novo
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Vigência</th>
                                <th class="text-right">Valor</th>
                                <th class="text-right w-24">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($wages as $wage)
                                <tr>
                                    <td class="font-mono">{{ $wage->effective_date->format('d/m/Y') }}</td>
                                    <td class="text-right font-mono font-bold">R$
                                        {{ number_format($wage->amount, 2, ',', '.') }}</td>
                                    <td class="text-right">
                                        <div class="flex gap-1 justify-end">
                                            <button wire:click="openForm({{ $wage->id }})"
                                                class="btn btn-ghost btn-xs">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                                </svg>
                                            </button>
                                            <button wire:click="delete({{ $wage->id }})"
                                                wire:confirm="Tem certeza que deseja excluir?"
                                                class="btn btn-ghost btn-xs text-error">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center opacity-40 py-8">Nenhum registro</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Modal -->
        @if ($isFormOpen)
            <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click.self="closeForm">
                <div class="card bg-base-100 w-full max-w-sm shadow-2xl">
                    <div class="card-body">
                        <h3 class="card-title text-primary">
                            {{ $editingId ? 'Editar' : 'Novo' }} Salário Mínimo
                        </h3>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Data de Vigência</span>
                            </label>
                            <input type="date" wire:model="effective_date"
                                class="input input-bordered @error('effective_date') input-error @enderror" />
                            @error('effective_date')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="form-control">
                            <label class="label">
                                <span class="label-text">Valor (R$)</span>
                            </label>
                            <input type="number" step="0.01" wire:model="amount"
                                class="input input-bordered @error('amount') input-error @enderror" />
                            @error('amount')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div class="card-actions justify-end mt-4">
                            <button wire:click="closeForm" class="btn btn-ghost btn-sm">Cancelar</button>
                            <button wire:click="save" class="btn btn-primary btn-sm">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
