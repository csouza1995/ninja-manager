<?php

namespace App\Livewire\Components\ServiceItems;

use App\Models\ServiceItem;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

class Form extends Component
{
    public ?int $itemId = null;
    public bool $showModal = false;

    #[Validate('required')]
    public string $code = '';

    #[Validate('required')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public string $unit_price = '0.00';

    #[Computed]
    public function item()
    {
        return $this->itemId ? ServiceItem::find($this->itemId) : null;
    }

    #[On('create-service-item')]
    public function create()
    {
        $this->reset();
        $this->showModal = true;
    }

    #[On('edit-service-item')]
    public function edit(int $id)
    {
        $this->itemId = $id;
        $item = $this->item;
        
        if ($item) {
            $this->code = $item->code;
            $this->description = $item->description;
            $this->unit_price = number_format($item->unit_price, 2, '.', '');
        }
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        ServiceItem::updateOrCreate(
            ['id' => $this->itemId],
            [
                'code' => $this->code,
                'description' => $this->description,
                'unit_price' => $this->unit_price,
            ]
        );

        $this->dispatch('service-item-saved');
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.components.service-items.form');
    }
}
