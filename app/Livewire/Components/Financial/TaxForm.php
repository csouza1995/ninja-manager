<?php

declare(strict_types=1);

namespace App\Livewire\Components\Financial;

use App\Models\Tax;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TaxForm extends Component
{
    public bool $isOpen = false;

    public bool $readOnly = false;

    public ?int $taxId = null;

    #[Validate('required|min:2')]
    public string $name = '';

    #[Validate('required|numeric|min:0|max:100')]
    public $percentage = 0;

    public bool $is_active = true;

    #[On('open-tax-form')]
    public function open(?int $id = null, bool $readOnly = false)
    {
        $this->resetForm();
        $this->readOnly = $readOnly;
        $this->isOpen = true;

        if ($id) {
            $this->loadTax($id);
        }
    }

    public function loadTax(int $id)
    {
        $tax = Tax::findOrFail($id);
        $this->taxId = $id;
        $this->name = $tax->name;
        $this->percentage = $tax->percentage;
        $this->is_active = $tax->is_active;
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
        $this->validate();

        Tax::updateOrCreate(
            ['id' => $this->taxId],
            [
                'name' => $this->name,
                'percentage' => $this->percentage,
                'is_active' => $this->is_active,
            ]
        );

        $this->dispatch('tax-saved');
        session()->flash('success', 'Imposto salvo com sucesso!');
        $this->close();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['taxId', 'name', 'percentage', 'is_active']);
    }

    public function render()
    {
        return view('livewire.components.financial.tax-form');
    }
}
