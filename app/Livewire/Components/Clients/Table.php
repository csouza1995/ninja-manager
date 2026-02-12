<?php

namespace App\Livewire\Components\Clients;

use App\Models\Client;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Table extends Component
{
    use WithPagination;

    public string $search = '';

    #[On('client-saved')]
    #[On('client-deleted')]
    public function refresh()
    {
        $this->resetPage();
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('nickname', 'like', "%{$this->search}%")
                    ->orWhere('document', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.components.clients.table', [
            'clients' => $clients,
        ]);
    }
}
