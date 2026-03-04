<?php

namespace App\Livewire\Components\ServiceItems;

use App\Models\ServiceItem;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public bool $showFilters = false;

    #[On('toggle-filters')]
    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    public string $search = '';

    // Sorting
    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('service-item-saved')]
    #[On('service-item-deleted')]
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
        $items = ServiceItem::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('code', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.service-items.table', [
            'items' => $items,
        ]);
    }
}
