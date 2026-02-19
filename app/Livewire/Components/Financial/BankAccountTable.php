<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class BankAccountTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[On('bank-account-saved')]
    #[On('bank-account-deleted')]
    public function refresh()
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
                $query->where('bank_name', 'like', "%{$this->search}%")
                    ->orWhere('owner_name', 'like', "%{$this->search}%")
                    ->orWhere('nickname', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.financial.bank-account-table', [
            'accounts' => $accounts,
        ]);
    }
}
