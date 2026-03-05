<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Revenue;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class RevenueTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    public bool $showFilters = false;

    #[On('toggle-filters')]
    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    // Filters
    public $status = '';

    public $classification = '';

    public $bank_account_id = '';

    public $start_date = '';

    public $end_date = '';

    // Sorting
    public string $sortField = 'due_date';

    public string $sortDirection = 'desc';

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'desc' ? 'asc' : 'desc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    #[On('revenue-saved')]
    #[On('revenue-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function updatedClassification()
    {
        $this->resetPage();
    }

    public function updatedBankAccountId()
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
        Revenue::findOrFail($id)->delete();
        session()->flash('success', 'Receita excluída.');
        $this->dispatch('revenue-deleted');
    }

    public function render()
    {
        $revenues = Revenue::with(['service.client', 'bankAccount', 'expenditure'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('origin_name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('classification', 'like', "%{$this->search}%")
                        ->orWhere('nf_id', 'like', "%{$this->search}%")
                        ->orWhereHas('service.client', function ($q2) {
                            $q2->where('name', 'like', "%{$this->search}%");
                        });
                });
            })
            ->when($this->status, function ($query) {
                if ($this->status === 'paid') {
                    $query->whereNotNull('paid_at');
                } elseif ($this->status === 'pending') {
                    $query->whereNull('paid_at');
                }
            })
            ->when($this->classification, function ($query) {
                $query->where('classification', 'like', "%{$this->classification}%");
            })
            ->when($this->bank_account_id, function ($query) {
                $query->where('bank_account_id', $this->bank_account_id);
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('due_date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('due_date', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.financial.revenue-table', [
            'revenues' => $revenues,
            'settingsBankAccounts' => \App\Models\BankAccount::orderBy('bank_name')->get(),
        ]);
    }
}
