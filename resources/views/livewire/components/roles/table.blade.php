<div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Criado em</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr class="hover">
                        <td class="font-medium">{{ $role->name }}</td>
                        <td class="text-base-content/60">{{ $role->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <button wire:click="$dispatch('edit-role', { id: {{ $role->id }} })" type="button"
                                    class="btn btn-sm btn-ghost">
                                    Editar
                                </button>
                                <button wire:click="$dispatch('delete-role', { id: {{ $role->id }} })"
                                    type="button" class="btn btn-sm btn-error btn-outline">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-8 text-base-content/60">
                            Nenhuma função encontrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>
</div>
