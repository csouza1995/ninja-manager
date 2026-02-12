<div>
    @if ($showDialog)
        <div class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg text-error">Confirmar Exclusão</h3>
                <p class="py-4">Tem certeza que deseja excluir este serviço? Esta ação não pode ser desfeita e todos os
                    itens vinculados serão removidos.</p>
                <div class="modal-action">
                    <button wire:click="closeDialog" class="btn btn-ghost">Cancelar</button>
                    <button wire:click="delete" class="btn btn-error text-white">Excluir</button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeDialog"></div>
        </div>
    @endif
</div>
