<?php

declare(strict_types=1);

namespace App\Livewire\Components\ClientWorkHours;

use App\Models\ClientWorkHour;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    public bool $showFilters = false;

    #[On('toggle-filters')]
    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    public ?int $clientId = null;

    public string $type = '';

    // Sorting
    public string $sortField = 'id';

    public string $sortDirection = 'asc';

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    #[On('work-hour-saved')]
    public function refresh(): void
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $this->dispatch('open-work-hour-form', id: $id);
    }

    public function delete(int $id): void
    {
        ClientWorkHour::findOrFail($id)->delete();
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        $query = ClientWorkHour::with(['client', 'services'])
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->clientId) {
            $query->where('client_id', $this->clientId);
        }

        if ($this->type !== '') {
            $query->where('type', $this->type);
        }

        return view('livewire.components.client-work-hours.table', [
            'entries' => $query->paginate(15),
        ]);
    }
}
