<?php

namespace App\Livewire\Pages\Receipts;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('receipt-generated')]
    #[On('receipt-deleted')]
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
        return view('livewire.pages.receipts.index');
    }
}
