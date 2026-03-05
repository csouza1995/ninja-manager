<?php

namespace App\Livewire\Components\Executors;

use App\Models\Executor;
use App\Models\Role;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    public ?int $executorId = null;

    public bool $showModal = false;

    public bool $readOnly = false;

    #[Validate('required|min:3')]
    public string $name = '';

    #[Validate('required')]
    public string $document = '';

    #[Validate('nullable|array')]
    public array $selectedRoles = [];

    #[Computed]
    public function executor()
    {
        return $this->executorId ? Executor::find($this->executorId) : null;
    }

    #[Computed]
    public function roles()
    {
        return Role::orderBy('name')->get();
    }

    #[On('create-executor')]
    public function create()
    {
        $this->reset();
        $this->showModal = true;
    }

    #[On('show-executor')]
    public function show(int $id)
    {
        $this->edit($id, true);
    }

    #[On('edit-executor')]
    public function edit(int $id, bool $readOnly = false)
    {
        $this->executorId = $id;
        $this->readOnly = $readOnly;
        $executor = $this->executor;

        if ($executor) {
            $this->name = $executor->name;
            $this->document = $executor->document;
            $this->selectedRoles = $executor->roles->pluck('id')->toArray();
        }

        $this->showModal = true;
    }

    public function save()
    {
        if ($this->readOnly) {
            return;
        }
        $this->validate();

        $executor = Executor::updateOrCreate(
            ['id' => $this->executorId],
            [
                'name' => $this->name,
                'document' => $this->document,
            ]
        );

        $executor->roles()->sync($this->selectedRoles);

        $this->dispatch('executor-saved');
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
        return view('livewire.components.executors.form');
    }
}
