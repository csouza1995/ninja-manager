<?php

namespace App\Livewire\Components\Receipts;

use App\Models\Receipt;
use App\Models\Service;
use App\Services\ReceiptService;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('receipt-generated')]
    #[On('receipt-deleted')]
    public function refresh()
    {
        $this->resetPage();
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

    public function delete(int $id)
    {
        $receipt = Receipt::find($id);
        if ($receipt) {
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
