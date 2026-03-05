<?php

namespace App\Livewire\Pages\ServiceItems;

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[Url]
    public ?int $showId = null;

    public function mount()
    {
        if ($this->showId) {
            $this->dispatch('show-service-item', id: $this->showId);
        }
    }

    #[On('service-item-saved')]
    #[On('service-item-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pages.service-items.index');
    }
}
