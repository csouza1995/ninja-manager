<?php

namespace App\Livewire\Components\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('service-saved')]
    #[On('service-deleted')]
    #[On('receipt-generated')]
    public function refresh()
    {
        $this->resetPage();
    }

    #[On('generate-receipt')]
    public function generateReceipt(int $serviceId)
    {
        $service = \App\Models\Service::find($serviceId);
        if ($service) {
            $receiptService = new \App\Services\ReceiptService();
            $receiptService->generate($service);
            $this->dispatch('receipt-generated');
            $this->dispatch('service-saved');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $services = Service::with(['client', 'executor', 'role', 'revenue', 'receipt'])
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })
                ->orWhereHas('executor', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.services.table', [
            'services' => $services,
        ]);
    }
}
