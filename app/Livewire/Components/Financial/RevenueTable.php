<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Revenue;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class RevenueTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[On('revenue-saved')]
    #[On('revenue-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        Revenue::findOrFail($id)->delete();
        session()->flash('success', 'Receita excluída.');
        $this->dispatch('revenue-deleted');
    }

    public function render()
    {
        $revenues = Revenue::with(['service', 'bankAccount', 'expenditure'])
            ->when($this->search, function ($query) {
                $query->where('origin_name', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%")
                    ->orWhere('classification', 'like', "%{$this->search}%")
                    ->orWhere('nf_id', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.financial.revenue-table', [
            'revenues' => $revenues,
        ]);
    }
}
