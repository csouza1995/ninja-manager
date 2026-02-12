<?php

namespace App\Livewire\Pages\ServiceItems;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

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
