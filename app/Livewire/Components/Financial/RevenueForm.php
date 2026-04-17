<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use App\Models\Invoice;
use App\Models\Revenue;
use App\Models\Service;
use App\Models\Tax;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RevenueForm extends Component
{
    public bool $isOpen = false;

    public bool $readOnly = false;

    public ?int $revenueId = null;

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

    #[Validate('required|numeric|min:0|max:100')]
    public $tax_percentage = 0;

    public $invoice_id = null;

    public $paid_at = null;

    public $notes = '';

    public $linkable_type;

    public $linkable_id;

    #[Validate('nullable|numeric')]
    public $adjustment_amount = 0;

    #[Validate('nullable|string|max:255')]
    public $adjustment_reason = '';

    public bool $hasExpenditure = false;

    public $classificationSuggestions = [];

    public function mount()
    {
        $this->due_date = now()->format('Y-m-d');
        $this->loadSuggestions();
    }

    #[On('open-revenue-form')]
    public function open(?int $id = null, bool $readOnly = false, ?int $createFromServiceId = null)
    {
        $this->resetForm();
        $this->isOpen = true;
        $this->readOnly = $readOnly;

        if ($id) {
            $this->loadRevenue($id);
        } elseif ($createFromServiceId) {
            $this->service_id = $createFromServiceId;
            $this->updatedServiceId($this->service_id);
        }
    }

    public function loadRevenue(int $id)
    {
        $revenue = Revenue::findOrFail($id);
        $this->revenueId = $id;
        $this->service_id = $revenue->service_id;
        $this->origin_name = $revenue->origin_name;
        $this->description = $revenue->description;
        $this->classification = $revenue->classification;
        $this->bank_account_id = $revenue->bank_account_id;
        $this->due_date = $revenue->due_date->format('Y-m-d');
        $this->gross_amount = $revenue->gross_amount;
        $this->tax_percentage = $revenue->tax_percentage;
        $this->invoice_id = $revenue->invoice_id;
        $this->paid_at = $revenue->paid_at ? $revenue->paid_at->format('Y-m-d') : null;
        $this->notes = $revenue->notes;
        $this->linkable_type = $revenue->linkable_type;
        $this->linkable_id = $revenue->linkable_id;
        $this->adjustment_amount = $revenue->adjustment_amount;
        $this->adjustment_reason = $revenue->adjustment_reason;

        $this->hasExpenditure = $revenue->expenditure()->exists() || ($revenue->invoice_id && $revenue->invoice->expenditure()->exists());
    }

    public function recalculateFromService()
    {
        if ($this->service_id) {
            $service = Service::find($this->service_id);
            if ($service) {
                $this->gross_amount = $service->total;
                $this->description = "Serviço #{$service->id} - {$service->client->name}";
            }
        }
    }

    public function updatedServiceId($value)
    {
        if ($value) {
            $service = Service::find($value);
            if ($service) {
                $this->gross_amount = $service->total;
                $this->description = "Serviço #{$service->id} - {$service->client->name}";
                $this->origin_name = $service->client->name;
            }
        }
    }

    public function updatedGrossAmount()
    {
        // Totals recalculation is no longer needed since tax was moved to Invoice
    }

    public function applyTax($taxId): void
    {
        if ($taxId == 0) {
            $this->tax_percentage = 0;

            return;
        }

        $tax = Tax::find($taxId);
        if ($tax) {
            $this->tax_percentage = $tax->percentage;
        }
    }

    #[Computed]
    public function linkableOutflows()
    {
        return \App\Models\Outflow::query()
            ->latest()
            ->limit(50)
            ->get();
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
        $this->validate();

        $revenue = Revenue::updateOrCreate(
            ['id' => $this->revenueId],
            [
                'service_id' => $this->service_id,
                'origin_name' => $this->origin_name,
                'description' => $this->description,
                'classification' => $this->classification,
                'bank_account_id' => $this->bank_account_id,
                'due_date' => $this->due_date,
                'gross_amount' => round((float) $this->gross_amount, 2),
                'tax_percentage' => round((float) $this->tax_percentage, 2),
                'invoice_id' => $this->invoice_id ?: null,
                'paid_at' => $this->paid_at ?: null,
                'notes' => $this->notes,
                'linkable_type' => $this->linkable_type ?: null,
                'linkable_id' => $this->linkable_id ?: null,
                'adjustment_amount' => round((float) ($this->adjustment_amount ?: 0), 2),
                'adjustment_reason' => $this->adjustment_reason ?: null,
            ]
        );

        // Sync is_paid on the linked Service whenever paid_at changes
        if ($revenue->service_id) {
            $service = Service::find($revenue->service_id);
            if ($service) {
                $service->update(['is_paid' => ! is_null($revenue->paid_at)]);
            }
        }

        $this->dispatch('revenue-saved');
        $this->close();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'revenueId', 'service_id', 'origin_name', 'description', 'classification',
            'bank_account_id', 'gross_amount', 'tax_percentage', 'invoice_id',
            'paid_at', 'notes', 'adjustment_amount', 'adjustment_reason',
        ]);
        $this->due_date = now()->format('Y-m-d');
        $this->loadSuggestions();
    }

    private function loadSuggestions()
    {
        $this->classificationSuggestions = Revenue::distinct()->pluck('classification')->toArray();
    }

    public function render()
    {
        return view('livewire.components.financial.revenue-form', [
            'services' => Service::whereNotIn('status', [\App\Enums\ServiceStatus::Negotiating, 'cancelled'])
                ->whereDoesntHave('revenue')
                ->latest()
                ->get(),
            'bankAccounts' => BankAccount::all(),
            'availableTaxes' => Tax::where('is_active', true)->get(),
            'invoices' => Invoice::latest()->get(),
        ]);
    }
}
