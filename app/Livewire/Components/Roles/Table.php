<?php

namespace App\Livewire\Components\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Table extends Component
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
        $roles = Role::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.roles.table', [
            'roles' => $roles,
        ]);
    }
}
