<?php

namespace App\Livewire\Components\Executors;

use App\Models\Executor;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Table extends Component
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
        $executors = Executor::query()
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('document', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.executors.table', [
            'executors' => $executors,
        ]);
    }
}
