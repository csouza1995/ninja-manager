<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\BankAccounts;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    #[Url]
    public ?int $showId = null;

    public function mount()
    {
        if ($this->showId) {
            $this->dispatch('open-bank-account-form', id: $this->showId, readOnly: true);
        }
    }

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
