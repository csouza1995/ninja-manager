<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Taxes;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Index extends Component
{
    public string $search = '';

    public function create()
    {
        $this->dispatch('open-tax-form');
    }

    public function updatedSearch()
    {
        // Reactive in children
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.financial.taxes.index');
    }
}
