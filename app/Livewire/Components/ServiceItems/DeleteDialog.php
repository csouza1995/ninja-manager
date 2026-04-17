<?php

namespace App\Livewire\Components\ServiceItems;

use App\Models\ServiceItem;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDialog extends Component
{
    public ?int $itemId = null;

    public bool $showDialog = false;

    #[On('delete-service-item')]
    public function confirmDelete(int $id)
    {
        $this->itemId = $id;
        $this->showDialog = true;
    }

    public function delete()
    {
        if ($this->itemId) {
            ServiceItem::destroy($this->itemId);
            $this->dispatch('service-item-deleted');
        }

        $this->closeDialog();
    }

    public function closeDialog()
    {
        $this->showDialog = false;
        $this->itemId = null;
    }

    public function render()
    {
        return view('livewire.components.service-items.delete-dialog');
    }
}
