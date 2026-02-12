<div>
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-5xl">
                <h3 class="font-bold text-lg text-primary mb-6">
                    {{ $serviceId ? 'Editar Serviço' : 'Novo Serviço' }}
                </h3>

                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Coluna 1: Cadastros Base -->
                        <div class="space-y-4">
                            <h4 class="font-semibold border-b border-base-300 pb-2">Informações Básicas</h4>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Cliente</span></label>
                                <select wire:model="client_id"
                                    class="select select-bordered w-full @error('client_id') select-error @enderror"
                                    @disabled(!$this->canEdit)>
                                    <option value="">Selecione um cliente</option>
                                    @foreach ($this->clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Executante</span></label>
                                <select wire:model="executor_id"
                                    class="select select-bordered w-full @error('executor_id') select-error @enderror"
                                    @disabled(!$this->canEdit)>
                                    <option value="">Selecione um executante</option>
                                    @foreach ($this->executors as $executor)
                                        <option value="{{ $executor->id }}">{{ $executor->name }}</option>
                                    @endforeach
                                </select>
                                @error('executor_id')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Função</span></label>
                                <select wire:model="role_id"
                                    class="select select-bordered w-full @error('role_id') select-error @enderror"
                                    @disabled(!$this->canEdit)>
                                    <option value="">Selecione uma função</option>
                                    @foreach ($this->roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Coluna 2: Status Principal -->
                        <div class="space-y-4">
                            <h4 class="font-semibold border-b border-base-300 pb-2">Status do Serviço</h4>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Status Principal</span></label>
                                <select wire:model="status"
                                    class="select select-bordered w-full @error('status') select-error @enderror">
                                    <option value="negotiating">Negociando</option>
                                    <option value="in_progress">Em andamento</option>
                                    <option value="delivered">Entregue</option>
                                    <option value="finalized">Finalizado</option>
                                    <option value="cancelled">Cancelado</option>
                                </select>
                                @error('status')
                                    <span class="label-text-alt text-error mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="divider">Período de Execução</div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Data/Hora Início</span></label>
                                <input type="datetime-local" wire:model="started_at" class="input input-bordered w-full"
                                    @disabled(!$this->canEdit) />
                            </div>

                            <div class="form-control w-full">
                                <label class="label"><span class="label-text">Data/Hora Fim</span></label>
                                <input type="datetime-local" wire:model="finished_at"
                                    class="input input-bordered w-full" @disabled(!$this->canEdit) />
                            </div>

                            <div class="alert alert-info text-sm py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    class="stroke-current shrink-0 w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Após finalizar ou faturar, os itens não podem ser alterados.</span>
                            </div>
                        </div>

                        <!-- Coluna 3: Status Adjacentes -->
                        <div class="space-y-4">
                            <h4 class="font-semibold border-b border-base-300 pb-2">Status Adjacentes</h4>

                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-4">
                                    <input type="checkbox" wire:model.live="is_paid"
                                        class="checkbox checkbox-success" />
                                    <span class="label-text font-medium text-success">Pago</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-4">
                                    <input type="checkbox" wire:model.live="is_documented"
                                        class="checkbox checkbox-info" />
                                    <span class="label-text font-medium text-info">Documentado (Recibo)</span>
                                </label>
                            </div>

                            <div class="form-control">
                                <label class="label cursor-pointer justify-start gap-4">
                                    <input type="checkbox" wire:model.live="is_invoiced"
                                        class="checkbox checkbox-primary" @disabled($is_invoiced) />
                                    <span class="label-text font-medium text-primary">Faturado (NF)</span>
                                </label>
                                @if ($is_invoiced)
                                    <span class="text-xs opacity-50 italic">O faturamento não pode ser desfeito.</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Seção de Itens -->
                    <div class="divider">Itens do Serviço</div>

                    @if ($this->canEdit)
                        <div class="flex flex-wrap gap-4 items-end mb-6 bg-base-300 p-4 rounded-lg">
                            <div class="form-control w-full md:flex-1">
                                <label class="label"><span class="label-text">Adicionar Item</span></label>
                                <select wire:model.live="selectedServiceItemId" class="select select-bordered w-full">
                                    <option value="">Selecione um item disponível</option>
                                    @foreach ($this->serviceItems as $serviceItem)
                                        <option value="{{ $serviceItem->id }}">[{{ $serviceItem->code }}]
                                            {{ $serviceItem->description }} - R$
                                            {{ number_format($serviceItem->unit_price, 2, ',', '.') }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-control w-32">
                                <label class="label"><span class="label-text">Qtd</span></label>
                                <input type="number" wire:model.live="quantity" min="0" step="0.0001"
                                    class="input input-bordered w-full" />
                            </div>

                            <button type="button" wire:click="addItem" class="btn btn-neutral"
                                @disabled(!$selectedServiceItemId)>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Adicionar
                            </button>
                        </div>
                    @endif

                    @error('items')
                        <div class="alert alert-error mb-4"><span>{{ $message }}</span></div>
                    @enderror

                    <div class="overflow-x-auto mb-6">
                        <table
                            class="table table-zebra w-full bg-base-100 rounded-lg overflow-hidden border border-base-300">
                            <thead class="bg-base-300">
                                <tr>
                                    <th>Cód.</th>
                                    <th>Descrição</th>
                                    <th>Valor Unit.</th>
                                    <th class="w-32">Quantidade</th>
                                    <th>Total</th>
                                    @if ($this->canEdit)
                                        <th class="w-16"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                    <tr class="hover">
                                        <td class="font-mono text-xs">{{ $item['code'] }}</td>
                                        <td>{{ $item['description'] }}</td>
                                        <td>R$ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                                        <td>
                                            @if ($this->canEdit)
                                                <input type="number" value="{{ $item['quantity'] }}"
                                                    wire:change="updateItemQuantity({{ $index }}, $event.target.value)"
                                                    min="0" step="0.0001"
                                                    class="input input-bordered input-sm w-24 text-center" />
                                            @else
                                                {{ number_format($item['quantity'], 4, ',', '.') }}
                                            @endif
                                        </td>
                                        <td class="font-bold">R$
                                            {{ number_format($item['total_price'], 2, ',', '.') }}</td>
                                        @if ($this->canEdit)
                                            <td>
                                                <button type="button" wire:click="removeItem({{ $index }})"
                                                    class="btn btn-ghost btn-xs btn-square text-error">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $this->canEdit ? 6 : 5 }}"
                                            class="text-center py-4 bg-base-200 opacity-50">Nenhum item adicionado
                                            ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (!empty($items))
                                <tfoot>
                                    <tr class="bg-base-300 font-bold">
                                        <td colspan="4" class="text-right uppercase">Total do Serviço:</td>
                                        <td colspan="{{ $this->canEdit ? 2 : 1 }}" class="text-lg">R$
                                            {{ number_format(collect($items)->sum('total_price'), 2, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>

                    <div class="modal-action">
                        <button type="button" wire:click="closeModal" class="btn btn-ghost">Cancelar</button>
                        <button type="submit" class="btn btn-primary px-8">Salvar Serviço</button>
                    </div>
                </form>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
