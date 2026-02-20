<div>
    @if ($entries->isEmpty())
        <div class="text-center py-12 text-base-content/40 italic text-sm">
            Nenhum lançamento de horas registrado.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="table table-sm w-full">
                <thead>
                    <tr class="text-[11px] uppercase opacity-50 tracking-wider">
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th class="text-center">Duração (H:M)</th>
                        <th class="text-center">Valor Hora</th>
                        <th class="text-center">Subtotal</th>
                        <th class="text-center">Contrato/Limite</th>
                        <th>Serviços</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        @php
                            $isPaid = $entry->type === \App\Enums\WorkHourType::Paid;
                            $contractType = $entry->contract_type ?? \App\Enums\WorkHourContractType::Fixed;
                        @endphp
                        <tr wire:key="wh-{{ $entry->id }}" class="hover">
                            <td class="font-semibold text-sm">{{ $entry->client->name }}</td>
                            <td>
                                <span class="badge badge-sm badge-{{ $entry->type->color() }}">
                                    {{ $entry->type->label() }}
                                </span>
                            </td>
                            <td class="text-center font-mono text-sm">{{ $entry->toFormattedHhMm() }}</td>
                            <td class="text-center font-mono text-sm opacity-70">
                                R$ {{ number_format((float) $entry->hourly_rate, 2, ',', '.') }}
                            </td>
                            <td class="text-center font-mono text-sm font-bold">
                                R$ {{ number_format($entry->executed_value, 2, ',', '.') }}
                            </td>
                            <td class="text-center">
                                @if (!$isPaid && $entry->contract_minutes)
                                    <div class="flex flex-col items-center">
                                        <span @class([
                                            'font-mono text-sm',
                                            'text-primary font-bold' =>
                                                $contractType === \App\Enums\WorkHourContractType::Fixed,
                                            'text-secondary' =>
                                                $contractType === \App\Enums\WorkHourContractType::Current,
                                        ])>
                                            {{ $entry->toFormattedContractHhMm() }}
                                        </span>
                                        <span class="text-[9px] uppercase font-bold opacity-40">
                                            {{ $contractType->label() }}
                                            ({{ $entry->achievementPercent() }}%)
                                        </span>
                                    </div>
                                @else
                                    <span class="opacity-20">—</span>
                                @endif
                            </td>
                            <td class="text-xs opacity-70">
                                @foreach ($entry->services as $svc)
                                    <span class="badge badge-ghost badge-xs font-mono"
                                        title="{{ $svc->description }}">#{{ str_pad((string) $svc->id, 5, '0', STR_PAD_LEFT) }}</span>
                                @endforeach
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="edit({{ $entry->id }})"
                                        class="btn btn-ghost btn-xs btn-square">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button wire:click="delete({{ $entry->id }})"
                                        wire:confirm="Remover este lançamento?"
                                        class="btn btn-ghost btn-xs btn-square text-error">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
