<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Expenditure;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class ExpenditureTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[On('expenditure-saved')]
    #[On('expenditure-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        Expenditure::findOrFail($id)->delete();
        session()->flash('success', 'Despesa excluída.');
        $this->dispatch('expenditure-deleted');
    }

    public function render()
    {
        $expenditures = Expenditure::with('bankAccount')
            ->when($this->search, function ($query) {
                $query->where('destination', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhere('classification', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.financial.expenditure-table', [
            'expenditures' => $expenditures,
        ]);
    }
}
