<?php

namespace App\Livewire\Components\Executors;

use App\Models\Executor;
use Livewire\Component;
use Livewire\Attributes\On;

class DeleteDialog extends Component
{
    public ?int $executorId = null;
    public bool $showDialog = false;

    #[On('delete-executor')]
    public function confirmDelete(int $id)
    {
        $this->executorId = $id;
        $this->showDialog = true;
    }

    public function delete()
    {
        if ($this->executorId) {
            Executor::destroy($this->executorId);
            $this->dispatch('executor-deleted');
        }

        $this->closeDialog();
    }

    public function closeDialog()
    {
        $this->showDialog = false;
        $this->executorId = null;
    }

    public function render()
    {
        return view('livewire.components.executors.delete-dialog');
    }
}
