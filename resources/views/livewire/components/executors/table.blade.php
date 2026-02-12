<div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Funções</th>
                    <th>Criado em</th>
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
                            <div class="flex justify-end gap-2">
                                <button wire:click="$dispatch('edit-executor', { id: {{ $executor->id }} })"
                                    type="button" class="btn btn-sm btn-ghost">
                                    Editar
                                </button>
                                <button wire:click="$dispatch('delete-executor', { id: {{ $executor->id }} })"
                                    type="button" class="btn btn-sm btn-error btn-outline">
                                    Excluir
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
