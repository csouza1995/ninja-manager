<?php

namespace App\Livewire\Components\ServiceItems;

use App\Models\ServiceItem;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Table extends Component
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
        $items = ServiceItem::query()
            ->when($this->search, function ($query) {
                $query->where('code', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.service-items.table', [
            'items' => $items,
        ]);
    }
}
