<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Expenditure;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;

class ExpenditureForm extends Component
{
    public bool $isOpen = false;
    public ?int $expenditureId = null;

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

    // Suggestions
    public $destinationSuggestions = [];
    public $classificationSuggestions = [];

    public function mount()
    {
        $this->due_date = now()->format('Y-m-d');
    }

    #[On('open-expenditure-form')]
    public function open(int $id = null)
    {
        $this->resetForm();
        $this->updateSuggestions();
        $this->isOpen = true;

        if ($id) {
            $this->loadExpenditure($id);
        }
    }

    public function loadExpenditure(int $id)
    {
        $exp = Expenditure::findOrFail($id);
        $this->expenditureId = $id;
        $this->destination = $exp->destination;
        $this->description = $exp->description;
        $this->classification = $exp->classification;
        $this->bank_account_id = $exp->bank_account_id;
        $this->due_date = $exp->due_date->format('Y-m-d');
        $this->amount = $exp->amount;
        $this->paid_at = $exp->paid_at ? $exp->paid_at->format('Y-m-d') : null;
    }

    public function save()
    {
        $this->validate();

        Expenditure::updateOrCreate(
            ['id' => $this->expenditureId],
            [
                'destination' => $this->destination,
                'description' => $this->description,
                'classification' => $this->classification,
                'bank_account_id' => $this->bank_account_id,
                'due_date' => $this->due_date,
                'amount' => $this->amount,
                'paid_at' => $this->paid_at,
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
            'expenditureId', 'destination', 'description', 
            'classification', 'bank_account_id', 'amount', 'paid_at'
        ]);
        $this->due_date = now()->format('Y-m-d');
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
