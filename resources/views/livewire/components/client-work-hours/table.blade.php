@php
    use App\Enums\WorkHourType;
    use App\Enums\WorkHourContractType;
@endphp

<div>
    <div class="flex flex-wrap items-center gap-2 mb-6 p-2 bg-base-100/50 rounded-box border border-base-200">
        <span class="text-sm font-medium text-base-content/70 px-2 flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
            </svg>
            Filtros
        </span>

        <select wire:model.live="type" class="select select-sm select-bordered bg-base-100">
            <option value="">Tipo</option>
            <option value="bonus">Bônus</option>
            <option value="service">Serviço/Projeto</option>
            <option value="package">Pacote de Horas</option>
            <option value="recurring">Manutenção</option>
        </select>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr class="text-[11px] uppercase opacity-50 tracking-wider">
                    <th>Cliente</th>
                    <th>Tipo</th>
                    <th class="text-center">Duração (H:M)</th>
                    <th class="text-center">Valor Hora</th>
                    <th class="text-center">Valor</th>
                    <th class="text-center">Contrato</th>
                    <th>Serviços</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($entries as $entry)
                    @php
                        $isPaid = $entry->type === WorkHourType::Paid;
                        $contractType = $entry->contract_type ?? WorkHourContractType::Fixed;
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
                        <td class="text-center">
                            <div class="flex flex-col items-center">
                                <span @class([
                                    'font-mono text-sm font-bold',
                                    'text-success' => $isPaid, {{-- Pago fica verde --}}
                                    'text-secondary' => !$isPaid && $contractType === WorkHourContractType::Current
                                ])>
                                    R$ {{ number_format($entry->executed_value, 2, ',', '.') }}
                                </span>
                                @if (!$isPaid && $contractType === WorkHourContractType::Current && $entry->contract_minutes > 0)
                                    <span class="text-[9px] uppercase font-bold opacity-30 mt-[-2px]">
                                        de R$ {{ number_format($entry->contract_value, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            @if (!$isPaid && $entry->contract_minutes)
                                <div class="flex flex-col items-center">
                                    <span @class([
                                        'font-mono text-sm',
                                        'text-primary font-bold' => $contractType === WorkHourContractType::Fixed,
                                        'opacity-50' => $contractType === WorkHourContractType::Current {{-- Branquinho/Neutro --}}
                                    ])>
                                        {{ $entry->toFormattedContractHhMm() }}
                                    </span>
                                    
                                    {{-- Só mostra % se for do tipo Fixo (Contrato) --}}
                                    @if ($contractType === WorkHourContractType::Fixed)
                                        <div class="flex items-center gap-1 mt-0.5">
                                            <div class="w-12 bg-base-300 h-1 rounded-full overflow-hidden">
                                                <div class="bg-primary h-full rounded-full" style="width: {{ $entry->achievementPercent() }}%"></div>
                                            </div>
                                            <span class="text-[9px] font-bold opacity-40">{{ $entry->achievementPercent() }}%</span>
                                        </div>
                                    @endif
                                </div>
                            @elseif(!$isPaid)
                                <span class="opacity-20 text-xs italic">Sem contrato</span>
                            @else
                                <span class="opacity-20">---</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                @foreach ($entry->services as $service)
                                    <a href="{{ route('services.index', ['showId' => $service->id]) }}" 
                                       wire:navigate
                                       class="badge badge-outline badge-xs opacity-70 hover:badge-primary transition-colors font-mono">
                                       #{{ str_pad($service->id, 5, '0', STR_PAD_LEFT) }}
                                    </a>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-1">
                                <button wire:click="$dispatch('open-work-hour-form', { id: {{ $entry->id }} })"
                                    class="btn btn-square btn-ghost btn-xs text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="delete({{ $entry->id }})"
                                    wire:confirm="Tem certeza que deseja excluir este lançamento?"
                                    class="btn btn-square btn-ghost btn-xs text-error">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
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
    <div class="mt-4 p-4 border-t border-base-300">
        {{ $entries->links() }}
    </div>
</div>
