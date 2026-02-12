<?php

namespace App\Livewire\Pages\Services;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('service-saved')]
    #[On('service-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pages.services.index');
    }
}
