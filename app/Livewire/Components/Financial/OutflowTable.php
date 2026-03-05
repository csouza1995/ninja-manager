<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Outflow;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class OutflowTable extends Component
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
    public $type = '';

    public $status = '';

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

    #[On('outflow-saved')]
    #[On('outflow-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedStatus()
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
        Outflow::findOrFail($id)->delete();
        session()->flash('success', 'Registro excluído!');
        $this->dispatch('outflow-deleted');
    }

    public function render()
    {
        $outflows = Outflow::with(['originBankAccount', 'destinationBankAccount', 'expenditure'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', "%{$this->search}%")
                        ->orWhere('person_name', 'like', "%{$this->search}%")
                        ->orWhere('type', 'like', "%{$this->search}%");
                });
            })
            ->when($this->type, function ($query) {
                $query->where('type', $this->type);
            })
            ->when($this->status, function ($query) {
                if ($this->status === 'paid') {
                    $query->whereNotNull('paid_at');
                } elseif ($this->status === 'pending') {
                    $query->whereNull('paid_at');
                }
            })
            ->when($this->bank_account_id, function ($query) {
                $query->where(function ($q) {
                    $q->where('origin_bank_account_id', $this->bank_account_id)
                        ->orWhere('destination_bank_account_id', $this->bank_account_id);
                });
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('due_date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('due_date', '<=', $this->end_date);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.financial.outflow-table', [
            'outflows' => $outflows,
            'settingsBankAccounts' => \App\Models\BankAccount::orderBy('bank_name')->get(),
        ]);
    }
}
