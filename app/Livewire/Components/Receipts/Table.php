<?php

namespace App\Livewire\Components\Receipts;

use App\Models\Receipt;
use App\Models\Service;
use App\Services\ReceiptService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Table extends Component
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public bool $showFilters = false;

    #[On('toggle-filters')]
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    // Filters
    public $status = '';

    public $client_id = '';

    public $start_date = '';

    public $end_date = '';

    // Sorting
    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

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

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedClientId()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function generateReceipt(int $serviceId)
    {
        $service = Service::find($serviceId);
        if ($service) {
            $receiptService = new ReceiptService;
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
            $receiptService = new ReceiptService;
            $receiptService->delete($receipt);
            $this->dispatch('receipt-deleted');
        }
    }

    public function render()
    {
        $receipts = Receipt::with(['service.client', 'service.executor'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('receipt_number', 'like', "%{$this->search}%")
                        ->orWhereHas('service.client', function ($q2) {
                            $q2->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, function ($query) {
                if ($this->status === 'generated') {
                    $query->where('is_signed', false)->where('is_sent', false);
                } elseif ($this->status === 'signed') {
                    $query->where('is_signed', true);
                } elseif ($this->status === 'sent') {
                    $query->where('is_sent', true);
                }
            })
            ->when($this->client_id, function ($query) {
                $query->whereHas('service', function ($q) {
                    $q->where('client_id', $this->client_id);
                });
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('created_at', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.receipts.table', [
            'receipts' => $receipts,
            'settingsClients' => \App\Models\Client::orderBy('name')->get(),
        ]);
    }
}
