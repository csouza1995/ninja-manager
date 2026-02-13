<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Invoice;
use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Carbon\Carbon;

class InvoiceForm extends Component
{
    public bool $isOpen = false;
    public ?int $invoiceId = null;

    #[Validate('nullable|string')]
    public $number;

    #[Validate]
    public $access_key;

    #[Validate('required|numeric|min:0')]
    public $amount;

    #[Validate('required|date')]
    public $issued_at;

    #[Validate('nullable|exists:services,id')]
    public $service_id;

    #[Validate('nullable|string')]
    public $notes;

    public function mount()
    {
        $this->issued_at = Carbon::now()->format('Y-m-d');
    }

    public function rules()
    {
        return [
            'access_key' => 'nullable|string|unique:invoices,access_key,' . $this->invoiceId,
        ];
    }

    #[On('open-invoice-form')]
    public function open(int $id = null)
    {
        $this->resetForm();
        $this->isOpen = true;

        if ($id) {
            $this->loadInvoice($id);
        }
    }

    public function loadInvoice(int $id)
    {
        $invoice = Invoice::findOrFail($id);
        $this->invoiceId = $id;
        $this->number = $invoice->number;
        $this->access_key = $invoice->access_key;
        $this->amount = $invoice->amount;
        $this->issued_at = $invoice->issued_at->format('Y-m-d');
        $this->service_id = $invoice->service_id;
        $this->notes = $invoice->notes;
    }

    public function save()
    {
        $this->validate();

        Invoice::updateOrCreate(
            ['id' => $this->invoiceId],
            [
                'number' => $this->number,
                'access_key' => $this->access_key,
                'amount' => $this->amount,
                'issued_at' => $this->issued_at,
                'service_id' => $this->service_id,
                'notes' => $this->notes,
            ]
        );

        $this->dispatch('invoice-saved');
        session()->flash('success', 'Nota Fiscal salva com sucesso!');
        $this->close();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['invoiceId', 'number', 'access_key', 'amount', 'service_id', 'notes']);
        $this->issued_at = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        $services = Service::with('client')
            ->whereIn('status', ['Finalizado', 'Em andamento', 'Entregue'])
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        return view('livewire.components.financial.invoice-form', [
            'services' => $services,
        ]);
    }
}
