<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Clients\WorkHours;

use App\Enums\WorkHourType;
use App\Models\Client;
use App\Models\ClientWorkHour;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
class Index extends Component
{
    #[Url]
    public ?int $clientId = null;

    #[On('work-hour-saved')]
    public function refresh(): void {}

    #[Computed]
    public function selectedClient(): ?Client
    {
        return $this->clientId ? Client::find($this->clientId) : null;
    }

    #[Computed]
    public function pendingMinutes(): int
    {
        $query = ClientWorkHour::query();

        if ($this->clientId) {
            $query->where('client_id', $this->clientId);
        }

        $executed = (clone $query)->where('type', WorkHourType::Executed)->sum('executed_minutes');
        $paid = (clone $query)->where('type', WorkHourType::Paid)->sum('executed_minutes');

        return max(0, (int) $executed - (int) $paid);
    }

    #[Computed]
    public function pendingFormatted(): string
    {
        $minutes = $this->pendingMinutes;

        return sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function openForm(?int $clientId = null): void
    {
        $this->dispatch('open-work-hour-form', clientId: $clientId ?? $this->clientId);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.pages.clients.work-hours.index', [
            'clients' => Client::orderBy('name')->get(),
        ]);
    }
}
