<?php

namespace App\Livewire\Components\Services;

use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    // Filters
    public $status = '';

    public $client_id = '';

    public $start_date = '';

    public $end_date = '';

    // Sorting
    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

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
            $receiptService = new \App\Services\ReceiptService;
            $receiptService->generate($service);
            $this->dispatch('receipt-generated');
            $this->dispatch('service-saved');
        }
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

    public function render()
    {
        $services = Service::with(['client', 'executor', 'role', 'revenue', 'receipt'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('client', function ($q2) {
                        $q2->where('name', 'like', "%{$this->search}%");
                    })
                        ->orWhereHas('executor', function ($q2) {
                            $q2->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->client_id, function ($query) {
                $query->where('client_id', $this->client_id);
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('created_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('created_at', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.services.table', [
            'services' => $services,
            'settingsClients' => \App\Models\Client::orderBy('name')->get(),
        ]);
    }
}
