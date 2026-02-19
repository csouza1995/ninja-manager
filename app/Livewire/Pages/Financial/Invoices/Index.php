<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Invoices;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    public string $search = '';

    public function create()
    {
        $this->dispatch('open-invoice-form');
    }

    public function updatedSearch()
    {
        // Reactive in children
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.financial.invoices.index');
    }
}
