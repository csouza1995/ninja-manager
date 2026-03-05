<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Invoice;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceTable extends Component
{
    use WithPagination;

    public bool $showFilters = false;

    #[On('toggle-filters')]
    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    #[Reactive]
    public string $search = '';

    // Filters
    public $client_id = '';

    public $start_date = '';

    public $end_date = '';

    // Sorting
    public string $sortField = 'issued_at';

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

    #[On('invoice-saved')]
    #[On('invoice-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
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

    public function delete(int $id)
    {
        Invoice::findOrFail($id)->delete();
        session()->flash('success', 'Nota Fiscal excluída!');
        $this->dispatch('invoice-deleted');
    }

    public function render()
    {
        $invoices = Invoice::with('service.client')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('number', 'like', "%{$this->search}%")
                        ->orWhere('access_key', 'like', "%{$this->search}%")
                        ->orWhere('notes', 'like', "%{$this->search}%");
                });
            })
            ->when($this->client_id, function ($query) {
                $query->whereHas('service', function ($q) {
                    $q->where('client_id', $this->client_id);
                });
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('issued_at', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('issued_at', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->orderBy('number', 'desc')
            ->paginate(15);

        return view('livewire.components.financial.invoice-table', [
            'invoices' => $invoices,
            'clients' => \App\Models\Client::orderBy('name')->get(),
        ]);
    }
}
