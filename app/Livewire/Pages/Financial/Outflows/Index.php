<?php

namespace App\Livewire\Pages\Financial\Outflows;

use App\Models\Outflow;
use App\Models\BankAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingId = null;
    public string $search = '';

    // Form fields
    public $type = 'Prolabore';
    public $description;
    public $origin_bank_account_id;
    public $destination_bank_account_id;
    public $person_name;
    public $amount;
    public $tax_percentage = 11.00;
    public $tax_amount = 0;
    public $due_date;
    public $paid_at;

    protected $rules = [
        'type' => 'required|in:Lucro/Dividendos,Prolabore,Investimento,Transferência',
        'description' => 'required|string|min:3',
        'origin_bank_account_id' => 'required|exists:bank_accounts,id',
        'destination_bank_account_id' => 'nullable|exists:bank_accounts,id',
        'person_name' => 'nullable|string',
        'amount' => 'required|numeric|min:0.01',
        'tax_percentage' => 'required|numeric|min:0|max:100',
        'tax_amount' => 'required|numeric|min:0',
        'due_date' => 'required|date',
        'paid_at' => 'nullable|date',
    ];

    public function mount()
    {
        $this->due_date = Carbon::now()->format('Y-m-d');
        $this->paid_at = Carbon::now()->format('Y-m-d');
    }

    public function openCreateModal()
    {
        $this->resetValidation();
        $this->reset(['description', 'destination_bank_account_id', 'person_name', 'amount', 'tax_amount', 'editingId']);
        $this->type = 'Prolabore';
        $this->amount = 1621.00;
        $this->tax_percentage = 11.00;
        $this->due_date = Carbon::now()->format('Y-m-d');
        $this->paid_at = Carbon::now()->format('Y-m-d');
        
        $this->calculateTax();

        $firstBank = BankAccount::first();
        $this->origin_bank_account_id = $firstBank ? $firstBank->id : null;
        
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->resetValidation();
        $outflow = Outflow::findOrFail($id);
        $this->editingId = $id;
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
        $this->showModal = true;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'type' && $this->type === 'Prolabore' && !$this->editingId) {
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

        if (!$this->tax_percentage) {
            $this->tax_percentage = 11.00;
        }

        $calc = ($this->amount ?: 0) * ($this->tax_percentage / 100);
        $this->tax_amount = min($calc, 932.00);
    }

    public function save()
    {
        $this->calculateTax();
        $this->validate();

        if ($this->type === 'Transferência' && !$this->destination_bank_account_id) {
            $this->addError('destination_bank_account_id', 'Para transferências, selecione o banco de destino.');
            return;
        }

        if ($this->origin_bank_account_id == $this->destination_bank_account_id) {
            $this->addError('destination_bank_account_id', 'O banco de destino deve ser diferente da origem.');
            return;
        }

        $data = [
            'type' => $this->type,
            'description' => $this->description,
            'origin_bank_account_id' => $this->origin_bank_account_id,
            'destination_bank_account_id' => $this->type === 'Transferência' ? $this->destination_bank_account_id : null,
            'person_name' => $this->person_name,
            'amount' => $this->amount,
            'tax_percentage' => $this->tax_percentage,
            'tax_amount' => $this->tax_amount,
            'due_date' => $this->due_date,
            'paid_at' => $this->paid_at,
        ];

        if ($this->editingId) {
            Outflow::find($this->editingId)->update($data);
        } else {
            Outflow::create($data);
        }

        $this->showModal = false;
        $this->dispatch('notify', 'Registro de saída salvo com sucesso!');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        Outflow::findOrFail($id)->delete();
        $this->dispatch('notify', 'Registro excluído!');
    }

    public function render()
    {
        $outflows = Outflow::with(['originBankAccount', 'destinationBankAccount'])
            ->when($this->search, function ($query) {
                $query->where('description', 'like', "%{$this->search}%")
                    ->orWhere('person_name', 'like', "%{$this->search}%")
                    ->orWhere('type', 'like', "%{$this->search}%");
            })
            ->orderBy('due_date', 'desc')
            ->paginate(10);

        $bankAccounts = BankAccount::all();
        
        $peopleSuggestions = Outflow::whereNotNull('person_name')
            ->distinct()
            ->pluck('person_name')
            ->toArray();

        return view('livewire.pages.financial.outflows.index', [
            'outflows' => $outflows,
            'bankAccounts' => $bankAccounts,
            'peopleSuggestions' => $peopleSuggestions,
        ])->layout('components.layouts.app');
    }
}
