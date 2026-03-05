<?php

namespace App\Livewire\Components\Roles;

use App\Models\Role;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    public ?int $roleId = null;

    public bool $showModal = false;

    public bool $readOnly = false;

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

    #[On('show-role')]
    public function show(int $id)
    {
        $this->edit($id, true);
    }

    #[On('edit-role')]
    public function edit(int $id, bool $readOnly = false)
    {
        $this->roleId = $id;
        $this->readOnly = $readOnly;
        $role = $this->role;

        if ($role) {
            $this->name = $role->name;
        }

        $this->showModal = true;
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
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
