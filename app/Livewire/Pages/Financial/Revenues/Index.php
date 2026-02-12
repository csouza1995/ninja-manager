<?php

namespace App\Livewire\Pages\Financial\Revenues;

use App\Models\Revenue;
use App\Models\Service;
use App\Models\Tax;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class Index extends Component
{
    use WithPagination;

    public bool $isFormOpen = false;
    public bool $readOnly = false;
    public ?int $revenueId = null;
    public string $search = '';

    #[Validate('nullable|exists:services,id')]
    public $service_id = null;

    #[Validate('nullable|required_without:service_id|string|min:2')]
    public $origin_name = '';

    #[Validate('required|min:2')]
    public $description = '';

    #[Validate('required')]
    public $classification = '';

    #[Validate('required|exists:bank_accounts,id')]
    public $bank_account_id = null;

    #[Validate('required|date')]
    public $due_date;

    #[Validate('required|numeric|min:0')]
    public $gross_amount = 0;

    public $invoice_id = null;
    
    #[Validate('numeric|min:0|max:100')]
    public $tax_percentage = 0;
    
    public $tax_amount = 0;
    public $net_amount = 0;
    public $paid_at = null;
    public $notes = '';

    // Search/Suggestions
    public $classificationSuggestions = [];

    public function mount()
    {
        $this->due_date = now()->format('Y-m-d');
        $this->classificationSuggestions = Revenue::distinct()->pluck('classification')->toArray();

        if ($id = request()->query('showId')) {
            $this->show((int) $id);
        }

        if ($serviceId = request()->query('createFromService')) {
            $this->create();
            $this->service_id = (int) $serviceId;
            $this->updatedServiceId($this->service_id);
        }
    }

    public function recalculateFromService()
    {
        if ($this->service_id) {
            $service = \App\Models\Service::find($this->service_id);
            if ($service) {
                $this->gross_amount = $service->total;
                $this->description = "Serviço #{$service->id} - {$service->client->name}";
                $this->calculateTotals();
                session()->flash('info', 'Valores atualizados conforme o serviço.');
            }
        }
    }

    public function updatedServiceId($value)
    {
        if ($value) {
            $service = \App\Models\Service::find($value);
            if ($service) {
                $this->gross_amount = $service->total;
                $this->description = "Serviço #{$service->id} - {$service->client->name}";
                $this->origin_name = $service->client->name;
                
                // If service has an invoice (via the link handled by other logics or manual)
                // Actually invoices aren't directly linked to services anymore in DB, 
                // but the user might have them in the system.
                
                $this->calculateTotals();
            }
        }
    }

    public function updatedGrossAmount() { $this->calculateTotals(); }
    public function updatedTaxPercentage() { $this->calculateTotals(); }

    public function calculateTotals()
    {
        $this->tax_amount = ($this->gross_amount * $this->tax_percentage) / 100;
        $this->net_amount = $this->gross_amount - $this->tax_amount;
    }

    public function applyTax($taxId)
    {
        $tax = Tax::find($taxId);
        if ($tax) {
            $this->tax_percentage = $tax->percentage;
            $this->calculateTotals();
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->readOnly = false;
        $this->isFormOpen = true;
    }

    public function show(int $id)
    {
        $this->edit($id);
        $this->readOnly = true;
    }

    public function edit(int $id)
    {
        $this->readOnly = false;
        $revenue = Revenue::findOrFail($id);
        $this->revenueId = $id;
        $this->service_id = $revenue->service_id;
        $this->origin_name = $revenue->origin_name;
        $this->description = $revenue->description;
        $this->classification = $revenue->classification;
        $this->bank_account_id = $revenue->bank_account_id;
        $this->due_date = $revenue->due_date->format('Y-m-d');
        $this->gross_amount = $revenue->gross_amount;
        $this->invoice_id = $revenue->invoice_id;
        $this->tax_percentage = $revenue->tax_percentage;
        $this->tax_amount = $revenue->tax_amount;
        $this->net_amount = $revenue->net_amount;
        $this->paid_at = $revenue->paid_at ? $revenue->paid_at->format('Y-m-d') : null;
        $this->notes = $revenue->notes;
        $this->isFormOpen = true;
    }

    public function save()
    {
        if ($this->readOnly) return;
        $this->validate();
        $this->calculateTotals();

        Revenue::updateOrCreate(
            ['id' => $this->revenueId],
            [
                'service_id' => $this->service_id,
                'origin_name' => $this->origin_name,
                'description' => $this->description,
                'classification' => $this->classification,
                'bank_account_id' => $this->bank_account_id,
                'due_date' => $this->due_date,
                'gross_amount' => $this->gross_amount,
                'invoice_id' => $this->invoice_id,
                'tax_percentage' => $this->tax_percentage,
                'tax_amount' => $this->tax_amount,
                'net_amount' => $this->net_amount,
                'paid_at' => $this->paid_at,
                'notes' => $this->notes,
            ]
        );

        session()->flash('success', 'Receita salva com sucesso!');
        $this->closeForm();
        $this->classificationSuggestions = Revenue::distinct()->pluck('classification')->toArray();
    }

    public function delete(int $id)
    {
        Revenue::findOrFail($id)->delete();
        session()->flash('success', 'Receita excluída.');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'revenueId', 'service_id', 'origin_name', 'description', 'classification',
            'bank_account_id', 'gross_amount', 'invoice_id', 'tax_percentage', 
            'tax_amount', 'net_amount', 'paid_at', 'notes'
        ]);
        $this->due_date = now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.pages.financial.revenues.index', [
            'revenues' => Revenue::with(['service', 'bankAccount'])
                ->when($this->search, function ($query) {
                    $query->where('origin_name', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%")
                        ->orWhere('classification', 'like', "%{$this->search}%")
                        ->orWhere('nf_id', 'like', "%{$this->search}%");
                })
                ->latest()
                ->paginate(10),
            'services' => Service::whereNotIn('status', ['negotiating', 'cancelled'])
                ->whereDoesntHave('revenue')
                ->latest()
                ->get(),
            'bankAccounts' => \App\Models\BankAccount::all(),
            'availableTaxes' => \App\Models\Tax::where('is_active', true)->get(),
            'invoices' => \App\Models\Invoice::latest()->get(),
        ])->layout('components.layouts.app');
    }
}
