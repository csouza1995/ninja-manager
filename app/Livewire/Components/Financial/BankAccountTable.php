<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class BankAccountTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    // Sorting
    public string $sortField = 'bank_name';

    public string $sortDirection = 'asc';

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('bank-account-saved')]
    #[On('bank-account-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        BankAccount::findOrFail($id)->delete();
        session()->flash('success', 'Conta bancária excluída.');
        $this->dispatch('bank-account-deleted');
    }

    public function render()
    {
        $accounts = BankAccount::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('bank_name', 'like', "%{$this->search}%")
                        ->orWhere('owner_name', 'like', "%{$this->search}%")
                        ->orWhere('nickname', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.financial.bank-account-table', [
            'accounts' => $accounts,
        ]);
    }
}
