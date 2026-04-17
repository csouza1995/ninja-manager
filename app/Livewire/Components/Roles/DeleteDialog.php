<?php

namespace App\Livewire\Components\Roles;

use App\Models\Role;
use Livewire\Attributes\On;
use Livewire\Component;

class DeleteDialog extends Component
{
    public ?int $roleId = null;

    public bool $showDialog = false;

    #[On('delete-role')]
    public function confirmDelete(int $id)
    {
        $this->roleId = $id;
        $this->showDialog = true;
    }

    public function delete()
    {
        if ($this->roleId) {
            Role::destroy($this->roleId);
            $this->dispatch('role-deleted');
        }

        $this->closeDialog();
    }

    public function closeDialog()
    {
        $this->showDialog = false;
        $this->roleId = null;
    }

    public function render()
    {
        return view('livewire.components.roles.delete-dialog');
    }
}
