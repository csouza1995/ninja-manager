<?php

declare(strict_types=1);

namespace App\Livewire\Components\ClientWorkHours;

use App\Models\ClientWorkHour;
use Livewire\Attributes\On;
use Livewire\Component;

class Table extends Component
{
    public ?int $clientId = null;

    #[On('work-hour-saved')]
    public function refresh(): void {}

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
            ->orderBy('created_at', 'desc');

        if ($this->clientId) {
            $query->where('client_id', $this->clientId);
        }

        return view('livewire.components.client-work-hours.table', [
            'entries' => $query->get(),
        ]);
    }
}
