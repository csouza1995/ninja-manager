<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Settings;

use App\Models\MinimumWage;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MinimumWages extends Component
{
    public ?int $editingId = null;

    #[Validate('required|date')]
    public string $effective_date = '';

    #[Validate('required|numeric|min:0')]
    public float $amount = 0;

    public bool $isFormOpen = false;

    public function openForm(?int $id = null): void
    {
        $this->resetValidation();

        if ($id) {
            $wage = MinimumWage::findOrFail($id);
            $this->editingId = $wage->id;
            $this->effective_date = $wage->effective_date->format('Y-m-d');
            $this->amount = (float) $wage->amount;
        } else {
            $this->editingId = null;
            $this->effective_date = now()->startOfYear()->format('Y-m-d');
            $this->amount = 0;
        }

        $this->isFormOpen = true;
    }

    public function save(): void
    {
        $this->validate();

        MinimumWage::updateOrCreate(
            ['id' => $this->editingId],
            [
                'effective_date' => $this->effective_date,
                'amount' => $this->amount,
            ]
        );

        $this->closeForm();
    }

    public function delete(int $id): void
    {
        MinimumWage::findOrFail($id)->delete();
    }

    public function closeForm(): void
    {
        $this->isFormOpen = false;
        $this->reset(['editingId', 'effective_date', 'amount']);
    }

    public function render()
    {
        return view('livewire.pages.settings.minimum-wages', [
            'wages' => MinimumWage::orderByDesc('effective_date')->get(),
        ])->layout('components.layouts.app');
    }
}
