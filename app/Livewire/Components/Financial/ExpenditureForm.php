<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use App\Models\Expenditure;
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
        $dueCarbon = $baseCarbon->copy()->addMonth();

        $baseStr = $baseCarbon->format('m/Y');
        $dueStr = $dueCarbon->format('m/Y');

        return "Imposto Ref. {$model->description} {$dueStr} ({$baseStr})";
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
                'amount' => $this->amount,
                'paid_at' => $this->paid_at === '' ? null : $this->paid_at,
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
            'classification', 'bank_account_id', 'amount', 'paid_at', 'linkables',
        ]);
        $this->due_date = now()->format('Y-m-d');
    }

    private function loadLinkables()
    {
        // Get Invoices with tax_amount > 0 and no linked expenditure
        $invoices = \App\Models\Invoice::where('tax_amount', '>', 0)
            ->whereDoesntHave('expenditure')
            ->get()
            ->map(fn ($r) => [
                'type' => 'App\Models\Invoice',
                'id' => $r->id,
                'label' => "Fatura: {$r->description} (R$ ".number_format((float) $r->tax_amount, 2, ',', '.').')',
            ]);

        // Get Outflows with tax_amount > 0 and no linked expenditure
        $outflows = \App\Models\Outflow::where('tax_amount', '>', 0)
            ->whereDoesntHave('expenditure')
            ->get()
            ->map(fn ($o) => [
                'type' => 'App\Models\Outflow',
                'id' => $o->id,
                'label' => "Saída: {$o->description} (R$ ".number_format((float) $o->tax_amount, 2, ',', '.').')',
            ]);

        // Include currently selected item if editing an existing expenditure
        $current = [];
        if ($this->model_type && $this->model_id) {
            $modelClass = $this->model_type;
            $model = $modelClass::find($this->model_id);
            if ($model) {
                $prefix = $this->model_type === 'App\Models\Invoice' ? 'Fatura' : 'Saída';
                $current[] = [
                    'type' => $this->model_type,
                    'id' => $this->model_id,
                    'label' => "{$prefix}: {$model->description} (R$ ".number_format((float) $model->tax_amount, 2, ',', '.').')',
                    'selected' => true,
                ];
            }
        }

        $this->linkables = collect($current)->merge($invoices)->merge($outflows)->unique(function ($item) {
            return $item['type'].'-'.$item['id'];
        })->toArray();
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
