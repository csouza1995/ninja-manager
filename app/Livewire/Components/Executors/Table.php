<?php

namespace App\Livewire\Components\Executors;

use App\Models\Executor;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    // Filters
    public $role_id = '';

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

    #[On('executor-saved')]
    #[On('executor-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRoleId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $executors = Executor::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('document', 'like', "%{$this->search}%");
                });
            })
            ->when($this->role_id, function ($query) {
                $query->whereHas('roles', function ($q) {
                    $q->where('role_id', $this->role_id);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(15);

        return view('livewire.components.executors.table', [
            'executors' => $executors,
            'roles' => \App\Models\Role::orderBy('name')->get(),
        ]);
    }
}
