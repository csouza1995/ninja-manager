<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Expenditures;

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
            $this->dispatch('open-expenditure-form', id: $this->showId, readOnly: true);
        }
    }

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
