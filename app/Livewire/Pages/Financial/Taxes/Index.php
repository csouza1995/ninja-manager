<?php

namespace App\Livewire\Pages\Financial\Taxes;

use App\Models\Tax;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class Index extends Component
{
    use WithPagination;

    public bool $isFormOpen = false;
    public ?int $taxId = null;

    #[Validate('required|min:2')]
    public string $name = '';

    #[Validate('required|numeric|min:0|max:100')]
    public $percentage = 0;

    public bool $is_active = true;

    public function create()
    {
        $this->resetForm();
        $this->isFormOpen = true;
    }

    public function edit(int $id)
    {
        $tax = Tax::findOrFail($id);
        $this->taxId = $id;
        $this->name = $tax->name;
        $this->percentage = $tax->percentage;
        $this->is_active = $tax->is_active;
        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate();

        Tax::updateOrCreate(
            ['id' => $this->taxId],
            [
                'name' => $this->name,
                'percentage' => $this->percentage,
                'is_active' => $this->is_active,
            ]
        );

        session()->flash('success', 'Imposto salvo com sucesso!');
        $this->closeForm();
    }

    public function delete(int $id)
    {
        Tax::findOrFail($id)->delete();
        session()->flash('success', 'Imposto excluído.');
    }

    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset(['taxId', 'name', 'percentage', 'is_active']);
    }

    public function render()
    {
        return view('livewire.pages.financial.taxes.index', [
            'taxes' => Tax::latest()->paginate(10)
        ])->layout('components.layouts.app');
    }
}
