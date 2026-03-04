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

        <select wire:model.live="role_id" class="select select-sm select-bordered bg-base-100">
            <option value="">Função</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="cursor-pointer hover:bg-base-200" wire:click="sortBy('name')">
                        <div class="flex items-center gap-1">
                            Nome
                            @if ($sortField === 'name')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th>CPF</th>
                    <th>Funções</th>
                    <th class="cursor-pointer hover:bg-base-200" wire:click="sortBy('created_at')">
                        <div class="flex items-center gap-1">
                            Criado em
                            @if ($sortField === 'created_at')
                                <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                            @endif
                        </div>
                    </th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($executors as $executor)
                    <tr class="hover">
                        <td class="font-medium">{{ $executor->name }}</td>
                        <td class="text-base-content/60">{{ $executor->document }}</td>
                        <td>
                            @if ($executor->roles->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($executor->roles as $role)
                                        <span class="badge badge-success badge-sm">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-base-content/40">Sem funções</span>
                            @endif
                        </td>
                        <td class="text-base-content/60">{{ $executor->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="flex justify-end gap-1">
                                <button wire:click="$dispatch('edit-executor', { id: {{ $executor->id }} })"
                                    class="btn btn-square btn-ghost btn-xs" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </button>
                                <button wire:click="$dispatch('delete-executor', { id: {{ $executor->id }} })"
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
                        <td colspan="5" class="text-center py-8 text-base-content/60">
                            Nenhum executante encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $executors->links() }}
    </div>
</div>
