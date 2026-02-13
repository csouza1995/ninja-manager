<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class InvoiceTable extends Component
{
    use WithPagination;

    #[Reactive]
    public string $search = '';

    #[On('invoice-saved')]
    #[On('invoice-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function delete(int $id)
    {
        Invoice::findOrFail($id)->delete();
        session()->flash('success', 'Nota Fiscal excluída!');
        $this->dispatch('invoice-deleted');
    }

    public function render()
    {
        $invoices = Invoice::with('service.client')
            ->when($this->search, function ($query) {
                $query->where('number', 'like', "%{$this->search}%")
                    ->orWhere('access_key', 'like', "%{$this->search}%")
                    ->orWhere('notes', 'like', "%{$this->search}%");
            })
            ->orderBy('issued_at', 'desc')
            ->paginate(10);

        return view('livewire.components.financial.invoice-table', [
            'invoices' => $invoices,
        ]);
    }
}
