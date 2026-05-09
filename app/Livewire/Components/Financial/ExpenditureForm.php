<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use App\Models\Expenditure;
use App\Models\Invoice;
use App\Models\Revenue;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ExpenditureForm extends Component
{
    public bool $isOpen = false;

    public bool $readOnly = false;

    public ?int $expenditureId = null;

    // Direct Launch via Query Parameter
    #[Url]
    public ?string $launch = null;

    #[Url]
    public ?string $fromModelType = null;

    #[Url]
    public ?int $fromModelId = null;

    #[Url]
    public ?float $launchAmount = null;

    #[Url]
    public ?string $launchDescription = null;

    // Morph link fields
    public ?string $model_type = null;

    public ?int $model_id = null;

    #[Validate('required|min:2')]
    public $destination = '';

    #[Validate('required|min:2')]
    public $description = '';

    #[Validate('required')]
    public $classification = '';

    #[Validate('required|exists:bank_accounts,id')]
    public $bank_account_id = null;

    #[Validate('required|date')]
    public $due_date;

    #[Validate('required|numeric|min:0')]
    public $amount = 0;

    public $paid_at = null;

    #[Validate('nullable|numeric')]
    public $adjustment_amount = 0;

    #[Validate('nullable|string|max:255')]
    public $adjustment_reason = '';

    #[Validate('nullable|date')]
    public $reference_date = null;

    // Suggestions and Linkables
    public $destinationSuggestions = [];

    public $classificationSuggestions = [];

    public $linkables = [];

    public function mount()
    {
        $this->due_date = now()->format('Y-m-d');

        if ($this->launch === 'tax' && $this->fromModelType && $this->fromModelId) {
            $this->open(null, $this->fromModelType, $this->fromModelId, $this->launchAmount, $this->launchDescription);

            // Clear the URL trace but keep the form open
            $this->launch = null;
            $this->fromModelType = null;
            $this->fromModelId = null;
            $this->launchAmount = null;
            $this->launchDescription = null;
        }
    }

    #[On('open-expenditure-form')]
    public function open(?int $id = null, ?string $fromModelType = null, ?int $fromModelId = null, ?float $amount = null, ?string $description = null, bool $readOnly = false)
    {
        $this->resetForm();
        $this->readOnly = $readOnly;
        $this->updateSuggestions();

        if ($fromModelType && $fromModelId) {
            $this->model_type = $fromModelType;
            $this->model_id = $fromModelId;
            $this->amount = $amount ?? 0;
            $this->description = $description ?? '';
            $this->classification = 'Imposto';
            $this->destination = 'Receita Federal';

            if ($this->model_type === 'App\Models\Outflow') {
                $this->classification = 'Imposto S/ Prolabore';
                $this->destination = 'INSS / Receita Federal';
            }

            $modelClass = $this->model_type;
            $model = $modelClass::find($this->model_id);
            if ($model) {
                $baseDate = $model->paid_at ?? $model->due_date ?? now();
                $this->due_date = Carbon::parse($baseDate)->addMonth()->setDay(15)->format('Y-m-d');
                $this->description = $this->formatTaxDescription($model, $baseDate);
            }
        }

        $this->isOpen = true;

        if ($id) {
            $this->loadExpenditure($id);
        }

        $this->loadLinkables();
    }

    public function loadExpenditure(int $id)
    {
        $exp = Expenditure::findOrFail($id);
        $this->expenditureId = $id;
        $this->model_type = $exp->model_type;
        $this->model_id = $exp->model_id;
        $this->destination = $exp->destination;
        $this->description = $exp->description;
        $this->classification = $exp->classification;
        $this->bank_account_id = $exp->bank_account_id;
        $this->due_date = $exp->due_date->format('Y-m-d');
        $this->amount = $exp->amount;
        $this->paid_at = $exp->paid_at ? $exp->paid_at->format('Y-m-d') : null;
        $this->reference_date = $exp->reference_date ? $exp->reference_date->format('Y-m-d') : null;
        $this->adjustment_amount = $exp->adjustment_amount;
        $this->adjustment_reason = $exp->adjustment_reason;
    }

    public function updatedModelType($value)
    {
        if (! $value) {
            $this->model_id = null;
        }
    }

    public function updatedModelId($value)
    {
        if ($this->model_type && $value) {
            $modelClass = $this->model_type;
            $model = $modelClass::find($value);
            if ($model) {

                $this->amount = $model->tax_amount ?? 0;

                $this->classification = 'Imposto';
                $this->destination = 'Receita Federal';

                if ($this->model_type === 'App\Models\Outflow') {
                    $this->classification = 'Imposto S/ Prolabore';
                    $this->destination = 'INSS / Receita Federal';
                }

                $baseDate = $model->paid_at ?? $model->due_date ?? now();
                $this->due_date = Carbon::parse($baseDate)->addMonth()->setDay(15)->format('Y-m-d');
                $this->description = $this->formatTaxDescription($model, $baseDate);
            }
        }
    }

    private function formatTaxDescription($model, $baseDate): string
    {
        $baseCarbon = Carbon::parse($baseDate);
        $dueStr = $baseCarbon->copy()->addMonth()->format('m/Y');
        $baseStr = $baseCarbon->format('m/Y');

        if ($model instanceof Revenue) {
            $model->loadMissing(['service.client']);
            $ref = $model->service ? "Serviço #{$model->service->id}" : ($model->description ?? '');
            if ($model->service?->client) {
                $ref .= " - {$model->service->client->name}";
            }
        } elseif ($model instanceof Invoice) {
            $model->loadMissing(['service', 'client']);
            $ref = $model->service ? "Serviço #{$model->service->id}" : ($model->description ?? '');
            if ($model->client) {
                $ref .= " - {$model->client->name}";
            }
        } else {
            $ref = $model->description;
        }

        return "Imposto Ref. {$ref} {$dueStr} ({$baseStr})";
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
        $this->validate();

        Expenditure::updateOrCreate(
            ['id' => $this->expenditureId],
            [
                'model_type' => $this->model_type,
                'model_id' => $this->model_id,
                'destination' => $this->destination,
                'description' => $this->description,
                'classification' => $this->classification,
                'bank_account_id' => $this->bank_account_id,
                'due_date' => $this->due_date,
                'amount' => round((float) $this->amount, 2),
                'paid_at' => $this->paid_at === '' ? null : $this->paid_at,
                'reference_date' => $this->reference_date === '' ? null : $this->reference_date,
                'adjustment_amount' => round((float) ($this->adjustment_amount ?: 0), 2),
                'adjustment_reason' => $this->adjustment_reason ?: null,
            ]
        );

        $this->dispatch('expenditure-saved');
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
            'expenditureId', 'model_type', 'model_id', 'destination', 'description',
            'classification', 'bank_account_id', 'amount', 'paid_at', 'reference_date', 'linkables',
            'adjustment_amount', 'adjustment_reason',
        ]);
        $this->due_date = now()->format('Y-m-d');
    }

    private function loadLinkables()
    {
        // Revenues with tax > 0 and no linked expenditure
        $revenues = Revenue::where('tax_percentage', '>', 0)
            ->whereDoesntHave('expenditure')
            ->with(['service.client'])
            ->get()
            ->map(fn ($r) => [
                'type' => 'App\Models\Revenue',
                'id' => $r->id,
                'label' => $this->revenueLabel($r),
            ]);

        // Invoices with tax_amount > 0 and no linked expenditure
        $invoices = Invoice::where('tax_amount', '>', 0)
            ->whereDoesntHave('expenditure')
            ->get()
            ->map(fn ($r) => [
                'type' => 'App\Models\Invoice',
                'id' => $r->id,
                'label' => "NF {$r->number} (R$ ".number_format((float) $r->tax_amount, 2, ',', '.').')',
            ]);

        // Outflows with tax_amount > 0 and no linked expenditure
        $outflows = \App\Models\Outflow::where('tax_amount', '>', 0)
            ->whereDoesntHave('expenditure')
            ->get()
            ->map(fn ($o) => [
                'type' => 'App\Models\Outflow',
                'id' => $o->id,
                'label' => "Saída: {$o->description} (R$ ".number_format((float) $o->tax_amount, 2, ',', '.').')',
            ]);

        // Include currently selected item when editing (may already have expenditure)
        $current = [];
        if ($this->model_type && $this->model_id) {
            $modelClass = $this->model_type;
            $model = $modelClass::find($this->model_id);
            if ($model) {
                $label = match ($this->model_type) {
                    'App\Models\Revenue' => $this->revenueLabel($model->loadMissing(['service.client'])),
                    'App\Models\Invoice' => "NF {$model->number} (R$ ".number_format((float) $model->tax_amount, 2, ',', '.').')',
                    default => "Saída: {$model->description} (R$ ".number_format((float) $model->tax_amount, 2, ',', '.').')',
                };
                $current[] = ['type' => $this->model_type, 'id' => $this->model_id, 'label' => $label, 'selected' => true];
            }
        }

        $this->linkables = collect($current)
            ->merge($revenues)
            ->merge($invoices)
            ->merge($outflows)
            ->unique(fn ($item) => $item['type'].'-'.$item['id'])
            ->toArray();
    }

    private function revenueLabel(Revenue $revenue): string
    {
        if ($revenue->service) {
            $label = "Serviço #{$revenue->service->id}";
            if ($revenue->service->client) {
                $label .= " - {$revenue->service->client->name}";
            }
        } else {
            $label = $revenue->description ?? '';
        }

        return $label.' (R$ '.number_format((float) $revenue->tax_amount, 2, ',', '.').')';
    }

    private function updateSuggestions()
    {
        $this->destinationSuggestions = Expenditure::distinct()->pluck('destination')->toArray();
        $this->classificationSuggestions = Expenditure::distinct()
            ->pluck('classification')
            ->push('Imposto', 'Imposto S/ Prolabore', 'Guia INSS')
            ->unique()
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.financial.expenditure-form', [
            'bankAccounts' => BankAccount::all(),
        ]);
    }
}
