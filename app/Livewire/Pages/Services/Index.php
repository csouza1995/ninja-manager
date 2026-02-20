<?php

namespace App\Livewire\Pages\Services;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public ?int $showId = null;

    public string $search = '';

    public function mount()
    {
        if ($this->showId) {
            $this->dispatch('show-service', id: $this->showId);
        }
    }

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
