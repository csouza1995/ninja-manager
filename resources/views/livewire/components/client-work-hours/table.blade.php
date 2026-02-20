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
                        <th class="text-center">HHH:MM</th>
                        <th class="text-center">W:D:H:M</th>
                        <th class="text-center">Contrato</th>
                        <th>Serviços</th>
                        <th>Notas</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($entries as $entry)
                        @php $wdhm = $entry->toWdhm(); @endphp
                        <tr wire:key="wh-{{ $entry->id }}" class="hover">
                            <td class="font-semibold text-sm">{{ $entry->client->name }}</td>
                            <td>
                                <span class="badge badge-sm badge-{{ $entry->type->color() }}">
                                    {{ $entry->type->label() }}
                                </span>
                            </td>
                            <td class="text-center font-mono text-sm">{{ $entry->toFormattedHhMm() }}</td>
                            <td class="text-center font-mono text-sm">
                                {{ $wdhm['w'] }}w {{ $wdhm['d'] }}d {{ $wdhm['h'] }}h {{ $wdhm['m'] }}m
                            </td>
                            <td class="text-center font-mono text-xs opacity-60">
                                @if ($entry->contract_minutes)
                                    {{ sprintf('%d:%02d', intdiv($entry->contract_minutes, 60), $entry->contract_minutes % 60) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-xs opacity-70">
                                @foreach ($entry->services as $svc)
                                    <span
                                        class="badge badge-ghost badge-xs font-mono">#{{ str_pad($svc->id, 5, '0', STR_PAD_LEFT) }}</span>
                                @endforeach
                            </td>
                            <td class="text-xs opacity-60 max-w-[120px] truncate">{{ $entry->notes ?? '—' }}</td>
                            <td class="text-right">
                                <button wire:click="edit({{ $entry->id }})" class="btn btn-ghost btn-xs">
                                    Editar
                                </button>
                                <button wire:click="delete({{ $entry->id }})"
                                    wire:confirm="Remover este lançamento?" class="btn btn-ghost btn-xs text-error">
                                    Excluir
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
