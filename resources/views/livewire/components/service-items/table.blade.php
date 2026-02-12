<div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Valor</th>
                    <th>Criado em</th>
                    <th class="text-right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr class="hover">
                        <td class="font-medium">{{ $item->code }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-base-content/60">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                        <td class="text-base-content/60">{{ $item->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <button wire:click="$dispatch('edit-service-item', { id: {{ $item->id }} })"
                                    type="button" class="btn btn-sm btn-ghost">
                                    Editar
                                </button>
                                <button wire:click="$dispatch('delete-service-item', { id: {{ $item->id }} })"
                                    type="button" class="btn btn-sm btn-error btn-outline">
                                    Excluir
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-8 text-base-content/60">
                            Nenhum item encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
