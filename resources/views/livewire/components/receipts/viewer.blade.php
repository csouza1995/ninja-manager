<div>
    @if ($showModal)
        <div class="modal modal-open">
            <div class="modal-box max-w-5xl h-[90vh] flex flex-col p-0 overflow-hidden">
                <div class="flex justify-between items-center p-4 border-b border-base-300">
                    <h3 class="font-bold text-lg">Visualizar Recibo</h3>
                    <button wire:click="closeModal" class="btn btn-sm btn-circle btn-ghost">✕</button>
                </div>

                <div class="flex-1 bg-base-300 relative">
                    @if ($pdfUrl)
                        <iframe src="{{ $pdfUrl }}" class="w-full h-full border-none"></iframe>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <span class="loading loading-spinner loading-lg"></span>
                        </div>
                    @endif
                </div>

                <div class="p-4 border-t border-base-300 flex justify-end gap-2">
                    <button wire:click="closeModal" class="btn">Fechar</button>
                    @if ($pdfUrl)
                        <a href="{{ $pdfUrl }}" target="_blank" class="btn btn-primary">Abrir em nova aba</a>
                    @endif
                </div>
            </div>
            <div class="modal-backdrop" wire:click="closeModal"></div>
        </div>
    @endif
</div>
