<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Taxes;

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
            $this->dispatch('open-tax-form', id: $this->showId, readOnly: true);
        }
    }

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
