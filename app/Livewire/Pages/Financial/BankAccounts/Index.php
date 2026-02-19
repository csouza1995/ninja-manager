<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\BankAccounts;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    public string $search = '';

    public function create()
    {
        $this->dispatch('open-bank-account-form');
    }

    public function updatedSearch()
    {
        // Reactive
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.financial.bank-accounts.index');
    }
}
