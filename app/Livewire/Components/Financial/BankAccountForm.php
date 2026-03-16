<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BankAccountForm extends Component
{
    public bool $isOpen = false;

    public ?int $bankAccountId = null;

    public bool $readOnly = false;

    #[Validate('required|min:2')]
    public string $bank_name = '';

    #[Validate('required|min:2')]
    public string $owner_name = '';

    #[Validate('nullable|min:2')]
    public string $nickname = '';

    #[Validate('required|numeric|min:0')]
    public float $opening_balance = 0;

    #[Validate('required|date')]
    public string $opening_balance_date = '';

    #[On('open-bank-account-form')]
    public function open(?int $id = null, bool $readOnly = false)
    {
        $this->resetForm();
        $this->readOnly = $readOnly;
        $this->isOpen = true;

        if ($id) {
            $this->loadBankAccount($id);
        }
    }

    public function loadBankAccount(int $id)
    {
        $account = BankAccount::findOrFail($id);
        $this->bankAccountId = $id;
        $this->bank_name = $account->bank_name;
        $this->owner_name = $account->owner_name;
        $this->nickname = $account->nickname ?? '';
        $this->opening_balance = (float) $account->opening_balance;
        $this->opening_balance_date = $account->opening_balance_date ? $account->opening_balance_date->format('Y-m-d') : '';
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
        $this->validate();

        BankAccount::updateOrCreate(
            ['id' => $this->bankAccountId],
            [
                'bank_name' => $this->bank_name,
                'owner_name' => $this->owner_name,
                'nickname' => $this->nickname,
                'opening_balance' => $this->opening_balance,
                'opening_balance_date' => $this->opening_balance_date,
            ]
        );

        $this->dispatch('bank-account-saved');
        $this->close();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['bankAccountId', 'bank_name', 'owner_name', 'nickname', 'opening_balance', 'opening_balance_date']);
    }

    public function render()
    {
        return view('livewire.components.financial.bank-account-form');
    }
}
