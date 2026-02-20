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
    public function metrics(): array
    {
        $query = ClientWorkHour::query();

        if ($this->clientId) {
            $query->where('client_id', $this->clientId);
        }

        $entries = (clone $query)->get();

        $executedMinutes = $entries->where('type', WorkHourType::Executed)->sum('executed_minutes');
        $paidMinutes = $entries->where('type', WorkHourType::Paid)->sum('executed_minutes');
        $pendingMinutes = max(0, $executedMinutes - $paidMinutes);

        $executedValue = $entries->where('type', WorkHourType::Executed)->sum('executed_value');
        $paidValue = $entries->where('type', WorkHourType::Paid)->sum('executed_value');
        $pendingValue = max(0, $executedValue - $paidValue);

        // Achievement: Sum of executed / Sum of contract (only for Fixed types that have contract set)
        $fixedEntries = $entries->where('type', WorkHourType::Executed)
            ->where('contract_type', \App\Enums\WorkHourContractType::Fixed)
            ->whereNotNull('contract_minutes');

        $totalContractedMin = $fixedEntries->sum('contract_minutes');
        $totalExecutedForFixedMin = $fixedEntries->sum('executed_minutes');

        $totalContractValue = $fixedEntries->sum(fn ($e) => ($e->contract_minutes / 60) * (float) $e->hourly_rate);

        $achievement = $totalContractedMin > 0
            ? round(($totalExecutedForFixedMin / $totalContractedMin) * 100, 1)
            : 0;

        return [
            'pending_minutes' => $pendingMinutes,
            'pending_formatted' => sprintf('%d:%02d', intdiv($pendingMinutes, 60), $pendingMinutes % 60),
            'pending_value' => $pendingValue,
            'executed_value' => $executedValue,
            'total_contract_value' => $totalContractValue,
            'achievement' => $achievement,
        ];
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
