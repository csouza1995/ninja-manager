<?php

namespace App\Livewire\Components\Receipts;

use App\Models\Receipt;
use App\Models\Service;
use App\Services\ReceiptService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Table extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public $file;
    public $uploadingReceiptId;

    #[On('receipt-generated')]
    #[On('receipt-deleted')]
    public function refresh()
    {
        $this->resetPage();
        $this->reset(['file', 'uploadingReceiptId']);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function generateReceipt(int $serviceId)
    {
        $service = Service::find($serviceId);
        if ($service) {
            $receiptService = new ReceiptService();
            $receiptService->generate($service);
            $this->dispatch('receipt-generated');
            $this->dispatch('service-saved'); // To refresh status in services table if needed
        }
    }

    public function startUpload($receiptId)
    {
        $this->uploadingReceiptId = $receiptId;
    }

    public function saveUpload()
    {
        $this->validate([
            'file' => 'required|mimes:pdf|max:10240',
        ]);

        $receipt = Receipt::find($this->uploadingReceiptId);
        
        if ($receipt) {
            $receipt->addMedia($this->file->getRealPath())
                ->usingFileName($this->file->getClientOriginalName())
                ->toMediaCollection('signed_receipts');
                
            $receipt->update(['is_signed' => true]);
            
            $this->dispatch('receipt-signed');
            $this->refresh();
        }
    }

    public function markAsSent(int $id)
    {
        $receipt = Receipt::find($id);
        if ($receipt) {
            $receipt->update(['is_sent' => true]);
            $this->refresh();
        }
    }

    public function delete(int $id)
    {
        $receipt = Receipt::find($id);
        if ($receipt) {
            if ($receipt->is_sent) {
                // Should not happen due to UI restrictions, but for safety:
                return;
            }
            $receiptService = new ReceiptService();
            $receiptService->delete($receipt);
            $this->dispatch('receipt-deleted');
        }
    }

    public function render()
    {
        $receipts = Receipt::with(['service.client', 'service.executor'])
            ->when($this->search, function ($query) {
                $query->where('receipt_number', 'like', "%{$this->search}%")
                    ->orWhereHas('service.client', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.receipts.table', [
            'receipts' => $receipts,
        ]);
    }
}
