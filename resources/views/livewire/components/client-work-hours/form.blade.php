<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg mb-6">
                    {{ $workHourId ? 'Editar Horas' : 'Lançar Horas' }}
                </h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Cliente --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Cliente</span></label>
                            <select wire:model.live="client_id" class="select select-bordered w-full"
                                {{ $workHourId ? 'disabled' : '' }}>
                                <option value="">Selecione o cliente...</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <span class="text-error text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Valor Hora --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Valor Hora (R$)</span></label>
                            <input wire:model="hourly_rate" type="number" step="0.01" placeholder="0.00"
                                class="input input-bordered w-full font-mono" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- Tipo de Lançamento --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Tipo de
                                    Lançamento</span></label>
                            <div class="join w-full">
                                @foreach ($types as $t)
                                    <button type="button" wire:click="$set('type', '{{ $t->value }}')"
                                        @class([
                                            'join-item btn btn-sm flex-1',
                                            'btn-primary' => $type === $t->value,
                                        ])>
                                        {{ $t->label() }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Modo de entrada --}}
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Modo de entrada</span></label>
                            <div class="join w-full">
                                @foreach ($modes as $mo)
                                    <button type="button" wire:click="$set('mode', '{{ $mo->value }}')"
                                        @class([
                                            'join-item btn btn-sm flex-1',
                                            'btn-primary' => $mode === $mo->value,
                                        ])>
                                        {{ $mo->label() }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Horas Executadas --}}
                    <div class="bg-base-300/20 p-4 rounded-xl border border-base-300/30">
                        @if ($mode === 'hhh_mm')
                            <div class="form-control">
                                <label class="label py-0">
                                    <span class="label-text font-semibold text-xs uppercase opacity-70">
                                        {{ $type === 'paid' ? 'Horas Pagas' : 'Horas Executadas' }} <span
                                            class="opacity-50">(HHH:MM)</span>
                                    </span>
                                </label>
                                <input wire:model="hh_mm" type="text" placeholder="Ex: 40:30"
                                    class="input input-bordered w-full font-mono mt-1" />
                            </div>
                        @else
                            <div>
                                <label class="label py-0"><span
                                        class="label-text font-semibold text-xs uppercase opacity-70">
                                        {{ $type === 'paid' ? 'Horas Pagas' : 'Horas Executadas' }} <span
                                            class="opacity-50">(W:D:H:M)</span>
                                    </span></label>
                                <div class="grid grid-cols-4 gap-2 mt-1">
                                    <div class="form-control">
                                        <label class="label py-0"><span
                                                class="label-text text-[10px] opacity-60">W</span></label>
                                        <input wire:model="weeks" type="number" min="0"
                                            class="input input-bordered input-sm font-mono text-center" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-0"><span
                                                class="label-text text-[10px] opacity-60">D</span></label>
                                        <input wire:model="days" type="number" min="0"
                                            class="input input-bordered input-sm font-mono text-center" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-0"><span
                                                class="label-text text-[10px] opacity-60">H</span></label>
                                        <input wire:model="hours" type="number" min="0"
                                            class="input input-bordered input-sm font-mono text-center" />
                                    </div>
                                    <div class="form-control">
                                        <label class="label py-0"><span
                                                class="label-text text-[10px] opacity-60">M</span></label>
                                        <input wire:model="minutes" type="number" min="0" max="59"
                                            class="input input-bordered input-sm font-mono text-center" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Configuração do Contrato (Ocultar se for Pago) --}}
                    @if ($type === 'executed')
                        <div class="bg-primary/5 p-4 rounded-xl border border-primary/20">
                            <div class="flex justify-between items-center mb-2">
                                <label class="label py-0"><span
                                        class="label-text font-semibold text-xs uppercase text-primary">Contrato /
                                        Limite</span></label>
                                <div class="join">
                                    @foreach ($contractTypes as $ct)
                                        <button type="button"
                                            wire:click="$set('contract_type', '{{ $ct->value }}')"
                                            @class([
                                                'join-item btn btn-[10px] px-2 h-6 min-h-0',
                                                'btn-primary' => $contract_type === $ct->value,
                                                'btn-ghost opacity-50' => $contract_type !== $ct->value,
                                            ])>
                                            {{ $ct->label() }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            @if ($mode === 'hhh_mm')
                                <div class="form-control">
                                    <label class="label py-0">
                                        <span class="label-text text-xs opacity-70">Capacidade <span
                                                class="opacity-50">(opcional, HHH:MM)</span></span>
                                    </label>
                                    <input wire:model="contract_hh_mm" type="text" placeholder="Ex: 80:00"
                                        class="input input-bordered w-full font-mono input-sm mt-1" />
                                </div>
                            @else
                                <div>
                                    <label class="label py-0"><span class="label-text text-xs opacity-70">Capacidade
                                            <span class="opacity-50">(W:D:H:M)</span></span></label>
                                    <div class="grid grid-cols-4 gap-2 mt-1">
                                        <div class="form-control">
                                            <label class="label py-0"><span
                                                    class="label-text text-[10px] opacity-60">W</span></label>
                                            <input wire:model="contract_weeks" type="number" min="0"
                                                class="input input-bordered input-sm font-mono text-center" />
                                        </div>
                                        <div class="form-control">
                                            <label class="label py-0"><span
                                                    class="label-text text-[10px] opacity-60">D</span></label>
                                            <input wire:model="contract_days" type="number" min="0"
                                                class="input input-bordered input-sm font-mono text-center" />
                                        </div>
                                        <div class="form-control">
                                            <label class="label py-0"><span
                                                    class="label-text text-[10px] opacity-60">H</span></label>
                                            <input wire:model="contract_hours_raw" type="number" min="0"
                                                class="input input-bordered input-sm font-mono text-center" />
                                        </div>
                                        <div class="form-control">
                                            <label class="label py-0"><span
                                                    class="label-text text-[10px] opacity-60">M</span></label>
                                            <input wire:model="contract_minutes_raw" type="number" min="0"
                                                max="59"
                                                class="input input-bordered input-sm font-mono text-center" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <p class="text-[10px] mt-2 opacity-50 italic">
                                {{ \App\Enums\WorkHourContractType::from($contract_type)->description() }}
                            </p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control">
                            <label class="label py-0"><span
                                    class="label-text text-xs opacity-50 font-semibold uppercase">H/Dia (Base
                                    D)</span></label>
                            <input wire:model="base_d" type="number" min="1" max="24"
                                class="input input-bordered input-sm font-mono mt-1" />
                        </div>
                        <div class="form-control">
                            <label class="label py-0"><span
                                    class="label-text text-xs opacity-50 font-semibold uppercase">D/Semana (Base
                                    W)</span></label>
                            <input wire:model="base_w" type="number" min="1" max="7"
                                class="input input-bordered input-sm font-mono mt-1" />
                        </div>
                    </div>

                    {{-- Serviços vinculados --}}
                    @if ($availableServices->isNotEmpty())
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Serviços
                                    vinculados</span></label>
                            <div
                                class="max-h-36 overflow-y-auto border border-base-300 rounded-lg p-2 space-y-1 bg-base-300/10">
                                @foreach ($availableServices as $service)
                                    <label
                                        class="flex items-center gap-2 cursor-pointer hover:bg-base-200 p-1 rounded text-sm">
                                        <input type="checkbox" wire:model="service_ids" value="{{ $service->id }}"
                                            class="checkbox checkbox-sm checkbox-primary" />
                                        <span
                                            class="font-mono opacity-70 text-xs">#{{ str_pad((string) $service->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span class="truncate">{{ $service->description ?? 'Sem descrição' }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notas --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Notas</span></label>
                        <textarea wire:model="notes" rows="2" class="textarea textarea-bordered w-full text-sm"
                            placeholder="Observações..."></textarea>
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="close" class="btn btn-ghost">Cancelar</button>
                    <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary min-w-[100px]">
                        <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
                        <span wire:loading.remove wire:target="save">Salvar</span>
                    </button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="close"></div>
        </div>
    @endif
</div>
