<?php

namespace App\Livewire\Pages\Roles;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('role-saved')]
    #[On('role-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pages.roles.index');
    }
}
