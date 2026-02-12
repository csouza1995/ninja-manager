<?php

namespace App\Livewire\Components\Receipts;

use App\Models\Receipt;
use Livewire\Component;
use Livewire\Attributes\On;

class Viewer extends Component
{
    public ?int $receiptId = null;
    public bool $showModal = false;

    #[On('view-receipt')]
    public function view(int $id)
    {
        $this->receiptId = $id;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->receiptId = null;
    }

    public function render()
    {
        $receipt = $this->receiptId ? Receipt::find($this->receiptId) : null;
        $pdfUrl = $receipt ? $receipt->getPdfUrl() : null;

        return view('livewire.components.receipts.viewer', [
            'pdfUrl' => $pdfUrl,
        ]);
    }
}
