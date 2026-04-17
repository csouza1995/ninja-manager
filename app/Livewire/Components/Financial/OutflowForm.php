<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\BankAccount;
use App\Models\Outflow;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class OutflowForm extends Component
{
    public bool $isOpen = false;

    public bool $readOnly = false;

    public ?int $outflowId = null;

    #[Validate]
    public $type = 'Prolabore';

    #[Validate('required|string|min:3')]
    public $description;

    #[Validate('required|exists:bank_accounts,id')]
    public $origin_bank_account_id;

    #[Validate]
    public $destination_bank_account_id;

    #[Validate('nullable|string')]
    public $person_name;

    #[Validate('required|numeric|min:0.01')]
    public $amount;

    #[Validate('required|numeric|min:0|max:100')]
    public $tax_percentage = 11.00;

    #[Validate('required|numeric|min:0')]
    public $tax_amount = 0;

    #[Validate('required|date')]
    public $due_date;

    #[Validate('nullable|date')]
    public $paid_at;

    #[Validate('nullable|date')]
    public $reference_date;

    #[Validate('nullable|numeric')]
    public $adjustment_amount = 0;

    #[Validate('nullable|string|max:255')]
    public $adjustment_reason = '';

    // Suggestions
    public $peopleSuggestions = [];

    public function mount()
    {
        $this->due_date = Carbon::now()->format('Y-m-d');
    }

    public function rules()
    {
        return [
            'type' => 'required|in:Lucro/Dividendos,Prolabore,Investimento,Transferência',
            'destination_bank_account_id' => 'nullable|exists:bank_accounts,id',
        ];
    }

    #[On('open-outflow-form')]
    public function open(?int $id = null, bool $readOnly = false)
    {
        $this->resetForm();
        $this->readOnly = $readOnly;
        $this->isOpen = true;
        $this->updateSuggestions();

        if ($id) {
            $this->loadOutflow($id);
        } else {
            // Default values for new
            $this->type = 'Prolabore';
            $this->amount = 1621.00;
            $this->tax_percentage = 11.00;
            $this->calculateTax();

            $firstBank = BankAccount::first();
            $this->origin_bank_account_id = $firstBank ? $firstBank->id : null;
        }
    }

    public function loadOutflow(int $id)
    {
        $outflow = Outflow::findOrFail($id);
        $this->outflowId = $id;
        $this->type = $outflow->type;
        $this->description = $outflow->description;
        $this->origin_bank_account_id = $outflow->origin_bank_account_id;
        $this->destination_bank_account_id = $outflow->destination_bank_account_id;
        $this->person_name = $outflow->person_name;
        $this->amount = $outflow->amount;
        $this->tax_percentage = $outflow->tax_percentage;
        $this->tax_amount = $outflow->tax_amount;
        $this->due_date = $outflow->due_date->format('Y-m-d');
        $this->paid_at = $outflow->paid_at ? $outflow->paid_at->format('Y-m-d') : null;
        $this->reference_date = $outflow->reference_date ? $outflow->reference_date->format('Y-m-d') : null;
        $this->adjustment_amount = $outflow->adjustment_amount;
        $this->adjustment_reason = $outflow->adjustment_reason;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'type' && $this->type === 'Prolabore' && ! $this->outflowId) {
            $this->amount = 1621.00;
        }

        if (in_array($propertyName, ['amount', 'tax_percentage', 'type'])) {
            $this->calculateTax();
        }
    }

    public function calculateTax()
    {
        if ($this->type !== 'Prolabore') {
            $this->tax_percentage = 0;
            $this->tax_amount = 0;

            return;
        }

        if (! $this->tax_percentage) {
            $this->tax_percentage = 11.00;
        }

        $calc = ($this->amount ?: 0) * ($this->tax_percentage / 100);
        $this->tax_amount = round(min($calc, 932.00), 2);
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
        $this->calculateTax();
        $this->validate();

        if ($this->type === 'Transferência' && ! $this->destination_bank_account_id) {
            $this->addError('destination_bank_account_id', 'Para transferências, selecione o banco de destino.');

            return;
        }

        if ($this->origin_bank_account_id == $this->destination_bank_account_id) {
            $this->addError('destination_bank_account_id', 'O banco de destino deve ser diferente da origem.');

            return;
        }

        Outflow::updateOrCreate(
            ['id' => $this->outflowId],
            [
                'type' => $this->type,
                'description' => $this->description,
                'origin_bank_account_id' => $this->origin_bank_account_id,
                'destination_bank_account_id' => $this->type === 'Transferência' ? $this->destination_bank_account_id : null,
                'person_name' => $this->person_name,
                'amount' => round((float) $this->amount, 2),
                'tax_percentage' => round((float) $this->tax_percentage, 2),
                'tax_amount' => round((float) $this->tax_amount, 2),
                'due_date' => $this->due_date,
                'paid_at' => $this->paid_at ?: null,
                'reference_date' => $this->reference_date ?: null,
                'adjustment_amount' => round((float) ($this->adjustment_amount ?: 0), 2),
                'adjustment_reason' => $this->adjustment_reason ?: null,
            ]
        );

        $this->dispatch('outflow-saved');
        session()->flash('success', 'Registro de saída salvo com sucesso!');
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
            'outflowId', 'description', 'destination_bank_account_id',
            'person_name', 'amount', 'tax_amount', 'reference_date',
            'adjustment_amount', 'adjustment_reason',
        ]);
        $this->type = 'Prolabore';
        $this->due_date = Carbon::now()->format('Y-m-d');
    }

    private function updateSuggestions()
    {
        $this->peopleSuggestions = Outflow::whereNotNull('person_name')
            ->distinct()
            ->pluck('person_name')
            ->toArray();
    }

    public function render()
    {
        return view('livewire.components.financial.outflow-form', [
            'bankAccounts' => BankAccount::all(),
        ]);
    }
}
