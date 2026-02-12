<div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Apelido</th>
                    <th>Tipo</th>
                    <th>Documento</th>
                    <th>Criado em</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="hover">
                        <td class="font-medium">{{ $client->name }}</td>
                        <td class="text-base-content/70 italic">{{ $client->nickname ?? '---' }}</td>
                        <td>
                            <span class="badge {{ $client->type === 'individual' ? 'badge-success' : 'badge-ghost' }}">
                                {{ $client->type === 'individual' ? 'PF' : 'PJ' }}
                            </span>
                        </td>
                        <td class="text-base-content/60">{{ $client->document }}</td>
                        <td class="text-base-content/60">{{ $client->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <button wire:click="$dispatch('edit-client', { id: {{ $client->id }} })"
                                    type="button" class="btn btn-sm btn-ghost">
                                    Editar
                                </button>
                                <button wire:click="$dispatch('delete-client', { id: {{ $client->id }} })"
                                    type="button" class="btn btn-sm btn-error btn-outline">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-base-content/60">
                            Nenhum cliente encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $clients->links() }}
    </div>
</div>
