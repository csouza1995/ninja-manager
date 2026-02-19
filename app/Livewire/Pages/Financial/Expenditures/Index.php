<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Expenditures;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

class Index extends Component
{

    public string $search = '';

    public function create()
    {
        $this->dispatch('open-expenditure-form');
    }

    public function edit(int $id)
    {
        $this->dispatch('open-expenditure-form', id: $id);
    }

    public function updatedSearch()
    {
        // Search is reactive in child components
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.financial.expenditures.index');
    }
}
