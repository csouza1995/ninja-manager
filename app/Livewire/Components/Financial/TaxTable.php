<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Tax;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class TaxTable extends Component
{
    use WithPagination;

    public bool $showFilters = false;

    #[On('toggle-filters')]
    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    #[Reactive]
    public string $search = '';

    // Filters
    public $status = '';

    // Sorting
    public string $sortField = 'name';

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

    #[On('tax-saved')]
    #[On('tax-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        Tax::findOrFail($id)->delete();
        session()->flash('success', 'Imposto excluído.');
        $this->dispatch('tax-deleted');
    }

    public function render()
    {
        $taxes = Tax::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->when($this->status !== '', function ($query) {
                $query->where('is_active', $this->status === 'active');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.financial.tax-table', [
            'taxes' => $taxes,
        ]);
    }
}
