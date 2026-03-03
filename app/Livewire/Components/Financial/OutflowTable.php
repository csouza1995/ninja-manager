<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Outflow;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class OutflowTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[On('outflow-saved')]
    #[On('outflow-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        Outflow::findOrFail($id)->delete();
        session()->flash('success', 'Registro excluído!');
        $this->dispatch('outflow-deleted');
    }

    public function render()
    {
        $outflows = Outflow::with(['originBankAccount', 'destinationBankAccount', 'expenditure'])
            ->when($this->search, function ($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('person_name', 'like', "%{$this->search}%")
                    ->orWhere('type', 'like', "%{$this->search}%");
            })
            ->orderBy('due_date', 'desc')
            ->paginate(10);

        return view('livewire.components.financial.outflow-table', [
            'outflows' => $outflows,
        ]);
    }
}
