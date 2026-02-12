<?php

namespace App\Livewire\Pages\Financial\BankAccounts;

use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class Index extends Component
{
    use WithPagination;

    public bool $isFormOpen = false;
    public ?int $bankAccountId = null;

    #[Validate('required|min:2')]
    public string $bank_name = '';

    #[Validate('required|min:2')]
    public string $owner_name = '';

    #[Validate('nullable|min:2')]
    public string $nickname = '';

    public function create()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function edit(int $id)
    {
        $account = BankAccount::findOrFail($id);
        $this->bankAccountId = $id;
        $this->bank_name = $account->bank_name;
        $this->owner_name = $account->owner_name;
        $this->nickname = $account->nickname ?? '';
        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate();

        BankAccount::updateOrCreate(
            ['id' => $this->bankAccountId],
            [
                'bank_name' => $this->bank_name,
                'owner_name' => $this->owner_name,
                'nickname' => $this->nickname,
            ]
        );

        session()->flash('success', 'Conta bancária salva com sucesso!');
        $this->closeForm();
    }

    public function delete(int $id)
    {
        BankAccount::findOrFail($id)->delete();
        session()->flash('success', 'Conta bancária excluída.');
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['bankAccountId', 'bank_name', 'owner_name', 'nickname']);
    }

    public function render()
    {
        return view('livewire.pages.financial.bank-accounts.index', [
            'accounts' => BankAccount::latest()->paginate(10)
        ])->layout('components.layouts.app');
    }
}
