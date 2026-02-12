<?php

namespace App\Livewire\Pages\Executors;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('executor-saved')]
    #[On('executor-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pages.executors.index');
    }
}
