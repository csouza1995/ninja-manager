<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Expenditure;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenditureTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

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
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('expenditure-saved')]
    #[On('expenditure-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
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
        Expenditure::findOrFail($id)->delete();
        session()->flash('success', 'Despesa excluída.');
        $this->dispatch('expenditure-deleted');
    }

    public function render()
    {
        $expenditures = Expenditure::with('bankAccount')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('destination', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('classification', 'like', "%{$this->search}%");
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

        return view('livewire.components.financial.expenditure-table', [
            'expenditures' => $expenditures,
            'settingsBankAccounts' => \App\Models\BankAccount::orderBy('bank_name')->get(),
        ]);
    }
}
