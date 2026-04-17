<?php

namespace App\Livewire\Pages\Clients;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('client-saved')]
    #[On('client-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pages.clients.index');
    }
}
