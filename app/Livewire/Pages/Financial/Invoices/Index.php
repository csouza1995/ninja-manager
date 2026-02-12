<?php

namespace App\Livewire\Pages\Financial\Invoices;

use App\Models\Invoice;
use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public string $search = '';

    // Form fields
    public $number;
    public $access_key;
    public $amount;
    public $issued_at;
    public $service_id;
    public $notes;

    protected $rules = [
        'number' => 'nullable|string',
        'access_key' => 'nullable|string|unique:invoices,access_key',
        'amount' => 'required|numeric|min:0',
        'issued_at' => 'required|date',
        'service_id' => 'nullable|exists:services,id',
        'notes' => 'nullable',
    ];

    public function mount()
    {
        $this->issued_at = Carbon::now()->format('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['number', 'access_key', 'amount', 'service_id', 'notes', 'editingId']);
        $this->issued_at = Carbon::now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $invoice = Invoice::findOrFail($id);
        $this->editingId = $id;
        $this->number = $invoice->number;
        $this->access_key = $invoice->access_key;
        $this->amount = $invoice->amount;
        $this->issued_at = $invoice->issued_at->format('Y-m-d');
        $this->service_id = $invoice->service_id;
        $this->notes = $invoice->notes;
        $this->showModal = true;
    }

    public function save()
    {
        $rules = $this->rules;
        if ($this->editingId) {
            $rules['access_key'] = 'nullable|string|unique:invoices,access_key,' . $this->editingId;
        }

        $this->validate($rules);

        $data = [
            'number' => $this->number,
            'access_key' => $this->access_key,
            'amount' => $this->amount,
            'issued_at' => $this->issued_at,
            'service_id' => $this->service_id,
            'notes' => $this->notes,
        ];

        if ($this->editingId) {
            Invoice::find($this->editingId)->update($data);
        } else {
            Invoice::create($data);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Nota Fiscal salva com sucesso!');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Invoice::findOrFail($id)->delete();
        $this->dispatch('notify', 'Nota Fiscal excluída!');
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

        $services = Service::with('client')
            ->whereIn('status', ['Finalizado', 'Em andamento', 'Entregue'])
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return view('livewire.pages.financial.invoices.index', [
            'invoices' => $invoices,
            'services' => $services,
        ])->layout('components.layouts.app');
    }
}
