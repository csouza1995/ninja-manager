<div>
@if ($showDialog)
    <div class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-error mb-4">Confirmar Exclusão</h3>
            <p class="py-4">Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.</p>

            <div class="modal-action">
                <button type="button" wire:click="closeDialog" class="btn btn-ghost">Cancelar</button>
                <button type="button" wire:click="delete" class="btn btn-error">Excluir</button>
            </div>
        </div>
        <div class="modal-backdrop" wire:click="closeDialog"></div>
    </div>
@endif
</div>
