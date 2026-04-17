<?php

namespace App\Livewire\Components\Services;

use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDialog extends Component
{
    public ?int $serviceId = null;

    public bool $showDialog = false;

    #[On('delete-service')]
    public function confirmDelete(int $id)
    {
        $this->serviceId = $id;
        $this->showDialog = true;
    }

    public function delete()
    {
        $service = Service::find($this->serviceId);

        if ($service && $service->canDelete()) {
            $service->delete();
            $this->dispatch('service-deleted');
        }

        $this->closeDialog();
    }

    public function closeDialog()
    {
        $this->showDialog = false;
        $this->serviceId = null;
    }

    public function render()
    {
        return view('livewire.components.services.delete-dialog');
    }
}
