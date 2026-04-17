<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Financial\Revenues;

use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public string $search = '';

    public function mount()
    {
        if ($id = request()->query('showId')) {
            $this->dispatch('open-revenue-form', id: (int) $id, readOnly: true);
        }

        if ($serviceId = request()->query('createFromService')) {
            $this->dispatch('open-revenue-form', createFromServiceId: (int) $serviceId);
        }
    }

    public function create()
    {
        $this->dispatch('open-revenue-form');
    }

    public function show(int $id)
    {
        $this->dispatch('open-revenue-form', id: $id, readOnly: true);
    }

    public function edit(int $id)
    {
        $this->dispatch('open-revenue-form', id: $id);
    }

    public function updatedSearch()
    {
        // Search is reactive in child components
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.pages.financial.revenues.index');
    }
}
