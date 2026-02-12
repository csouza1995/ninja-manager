<?php

namespace App\Livewire\Components\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

class Form extends Component
{
    public ?int $roleId = null;
    public bool $showModal = false;

    #[Validate('required|min:3')]
    public string $name = '';

    #[Computed]
    public function role()
    {
        return $this->roleId ? Role::find($this->roleId) : null;
    }

    #[On('create-role')]
    public function create()
    {
        $this->reset();
        $this->showModal = true;
    }

    #[On('edit-role')]
    public function edit(int $id)
    {
        $this->roleId = $id;
        $role = $this->role;
        
        if ($role) {
            $this->name = $role->name;
        }
        
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        Role::updateOrCreate(
            ['id' => $this->roleId],
            ['name' => $this->name]
        );

        $this->dispatch('role-saved');
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
        return view('livewire.components.roles.form');
    }
}
