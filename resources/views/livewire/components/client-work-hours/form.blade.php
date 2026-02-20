<div>
    @if ($isOpen)
        <div class="modal modal-open">
            <div class="modal-box max-w-2xl">
                <h3 class="font-bold text-lg mb-6">
                    {{ $workHourId ? 'Editar Horas' : 'Lançar Horas' }}
                </h3>

                <div class="space-y-4">
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

                    {{-- Tipo --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Tipo</span></label>
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

                    {{-- Horas Executadas --}}
                    @if ($mode === 'hhh_mm')
                        <div class="form-control">
                            <label class="label">
                                <span class="label-text font-semibold">Horas executadas <span
                                        class="opacity-50 text-xs">(HHH:MM)</span></span>
                            </label>
                            <input wire:model="hh_mm" type="text" placeholder="Ex: 40:30"
                                class="input input-bordered w-full font-mono" />
                        </div>
                    @else
                        <div>
                            <label class="label"><span class="label-text font-semibold">Horas executadas <span
                                        class="opacity-50 text-xs">(W:D:H:M)</span></span></label>
                            <div class="grid grid-cols-4 gap-2">
                                <div class="form-control">
                                    <label class="label py-0"><span
                                            class="label-text text-xs opacity-60">Semanas</span></label>
                                    <input wire:model="weeks" type="number" min="0"
                                        class="input input-bordered input-sm font-mono text-center" />
                                </div>
                                <div class="form-control">
                                    <label class="label py-0"><span
                                            class="label-text text-xs opacity-60">Dias</span></label>
                                    <input wire:model="days" type="number" min="0"
                                        class="input input-bordered input-sm font-mono text-center" />
                                </div>
                                <div class="form-control">
                                    <label class="label py-0"><span
                                            class="label-text text-xs opacity-60">Horas</span></label>
                                    <input wire:model="hours" type="number" min="0"
                                        class="input input-bordered input-sm font-mono text-center" />
                                </div>
                                <div class="form-control">
                                    <label class="label py-0"><span
                                            class="label-text text-xs opacity-60">Minutos</span></label>
                                    <input wire:model="minutes" type="number" min="0" max="59"
                                        class="input input-bordered input-sm font-mono text-center" />
                                </div>
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <div class="form-control">
                                    <label class="label py-0"><span class="label-text text-xs opacity-50">Horas/Dia
                                            (base_d)</span></label>
                                    <input wire:model="base_d" type="number" min="1" max="24"
                                        class="input input-bordered input-sm font-mono" />
                                </div>
                                <div class="form-control">
                                    <label class="label py-0"><span class="label-text text-xs opacity-50">Dias/Semana
                                            (base_w)</span></label>
                                    <input wire:model="base_w" type="number" min="1" max="7"
                                        class="input input-bordered input-sm font-mono" />
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Horas do Contrato --}}
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text font-semibold">Horas contratadas <span
                                    class="opacity-50 text-xs">(opcional, HHH:MM)</span></span>
                        </label>
                        <input wire:model="contract_hh_mm" type="text" placeholder="Ex: 80:00"
                            class="input input-bordered w-full font-mono" />
                    </div>

                    {{-- Serviços vinculados --}}
                    @if ($availableServices->isNotEmpty())
                        <div class="form-control">
                            <label class="label"><span class="label-text font-semibold">Serviços vinculados <span
                                        class="opacity-50 text-xs">(opcional)</span></span></label>
                            <div class="max-h-36 overflow-y-auto border border-base-300 rounded-lg p-2 space-y-1">
                                @foreach ($availableServices as $service)
                                    <label
                                        class="flex items-center gap-2 cursor-pointer hover:bg-base-200 p-1 rounded text-sm">
                                        <input type="checkbox" wire:model="service_ids" value="{{ $service->id }}"
                                            class="checkbox checkbox-sm checkbox-primary" />
                                        <span
                                            class="font-mono opacity-70 text-xs">#{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <span>{{ $service->client->name ?? '' }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notas --}}
                    <div class="form-control">
                        <label class="label"><span class="label-text font-semibold">Notas <span
                                    class="opacity-50 text-xs">(opcional)</span></span></label>
                        <textarea wire:model="notes" rows="2" class="textarea textarea-bordered w-full" placeholder="Observações..."></textarea>
                    </div>
                </div>

                <div class="modal-action">
                    <button wire:click="close" class="btn btn-ghost">Cancelar</button>
                    <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary">
                        <span wire:loading wire:target="save" class="loading loading-spinner loading-sm"></span>
                        Salvar
                    </button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="close"></div>
        </div>
    @endif
</div>
