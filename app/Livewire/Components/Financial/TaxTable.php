<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Tax;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class TaxTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[On('tax-saved')]
    #[On('tax-deleted')]
    public function refresh()
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
            ->latest()
            ->paginate(10);

        return view('livewire.components.financial.tax-table', [
            'taxes' => $taxes,
        ]);
    }
}
