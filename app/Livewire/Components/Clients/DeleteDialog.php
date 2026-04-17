<?php

namespace App\Livewire\Components\Clients;

use App\Models\Client;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDialog extends Component
{
    public ?int $clientId = null;

    public bool $showDialog = false;

    #[On('delete-client')]
    public function confirmDelete(int $id)
    {
        $this->clientId = $id;
        $this->showDialog = true;
    }

    public function delete()
    {
        if ($this->clientId) {
            Client::destroy($this->clientId);
            $this->dispatch('client-deleted');
        }

        $this->closeDialog();
    }

    public function closeDialog()
    {
        $this->showDialog = false;
        $this->clientId = null;
    }

    public function render()
    {
        return view('livewire.components.clients.delete-dialog');
    }
}
