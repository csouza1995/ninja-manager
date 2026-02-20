<?php

declare(strict_types=1);

namespace App\Livewire\Components\ClientWorkHours;

use App\Enums\WorkHourMode;
use App\Enums\WorkHourType;
use App\Models\Client;
use App\Models\ClientWorkHour;
use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Form extends Component
{
    public bool $isOpen = false;

    public ?int $workHourId = null;

    #[Validate('required|exists:clients,id')]
    public ?int $client_id = null;

    public string $type = 'executed';

    public string $mode = 'hhh_mm';

    /** HHH:MM mode — raw input string */
    public string $hh_mm = '0:00';

    /** WDHM mode — raw inputs */
    public int $weeks = 0;

    public int $days = 0;

    public int $hours = 0;

    public int $minutes = 0;

    /** Contract hours input (HHH:MM string) */
    public string $contract_hh_mm = '';

    public int $base_d = 8;

    public int $base_w = 5;

    #[Validate('nullable|array')]
    public array $service_ids = [];

    public string $notes = '';

    #[On('open-work-hour-form')]
    public function open(?int $id = null, ?int $clientId = null): void
    {
        $this->reset(['workHourId', 'hh_mm', 'weeks', 'days', 'hours', 'minutes',
            'contract_hh_mm', 'service_ids', 'notes']);
        $this->type = 'executed';
        $this->mode = 'hhh_mm';
        $this->base_d = 8;
        $this->base_w = 5;
        $this->hh_mm = '0:00';

        if ($clientId) {
            $this->client_id = $clientId;
        }

        if ($id) {
            $this->load($id);
        }

        $this->isOpen = true;
    }

    private function load(int $id): void
    {
        $wh = ClientWorkHour::with('services')->findOrFail($id);
        $this->workHourId = $id;
        $this->client_id = $wh->client_id;
        $this->type = $wh->type->value;
        $this->mode = $wh->mode->value;
        $this->base_d = $wh->base_d;
        $this->base_w = $wh->base_w;
        $this->weeks = $wh->weeks;
        $this->days = $wh->days;
        $this->hours = $wh->hours;
        $this->minutes = $wh->minutes;
        $this->hh_mm = $wh->toFormattedHhMm();
        $this->contract_hh_mm = $wh->contract_minutes
            ? sprintf('%d:%02d', intdiv($wh->contract_minutes, 60), $wh->contract_minutes % 60)
            : '';
        $this->service_ids = $wh->services->pluck('id')->map(fn ($v) => (string) $v)->toArray();
        $this->notes = $wh->notes ?? '';
    }

    public function save(): void
    {
        $this->validate();

        $executedMinutes = $this->computeExecutedMinutes();
        $contractMinutes = $this->computeContractMinutes();

        /** @var array{w:int,d:int,h:int,m:int} */
        $wdhm = $this->mode === WorkHourMode::Wdhm->value
            ? ['weeks' => $this->weeks, 'days' => $this->days, 'hours' => $this->hours, 'minutes' => $this->minutes]
            : $this->decomposeToWdhm($executedMinutes);

        $wh = ClientWorkHour::updateOrCreate(
            ['id' => $this->workHourId],
            [
                'client_id' => $this->client_id,
                'type' => $this->type,
                'mode' => $this->mode,
                'contract_minutes' => $contractMinutes,
                'executed_minutes' => $executedMinutes,
                'weeks' => $wdhm['weeks'],
                'days' => $wdhm['days'],
                'hours' => $wdhm['hours'],
                'minutes' => $wdhm['minutes'],
                'base_d' => $this->base_d,
                'base_w' => $this->base_w,
                'notes' => $this->notes ?: null,
            ]
        );

        $wh->services()->sync(array_map('intval', $this->service_ids));

        $this->dispatch('work-hour-saved');
        $this->close();
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->resetValidation();
    }

    private function computeExecutedMinutes(): int
    {
        if ($this->mode === WorkHourMode::Wdhm->value) {
            return ClientWorkHour::wdhmToMinutes(
                $this->weeks, $this->days, $this->hours, $this->minutes,
                $this->base_d, $this->base_w
            );
        }

        return ClientWorkHour::hhMmToMinutes($this->hh_mm ?: '0:00');
    }

    private function computeContractMinutes(): ?int
    {
        if (! $this->contract_hh_mm) {
            return null;
        }

        return ClientWorkHour::hhMmToMinutes($this->contract_hh_mm);
    }

    /** @return array{weeks:int,days:int,hours:int,minutes:int} */
    private function decomposeToWdhm(int $totalMinutes): array
    {
        $minutesPerDay = $this->base_d * 60;
        $minutesPerWeek = $this->base_w * $minutesPerDay;

        $w = intdiv($totalMinutes, $minutesPerWeek);
        $totalMinutes -= $w * $minutesPerWeek;
        $d = intdiv($totalMinutes, $minutesPerDay);
        $totalMinutes -= $d * $minutesPerDay;
        $h = intdiv($totalMinutes, 60);
        $m = $totalMinutes % 60;

        return ['weeks' => $w, 'days' => $d, 'hours' => $h, 'minutes' => $m];
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.components.client-work-hours.form', [
            'clients' => Client::orderBy('name')->get(),
            'availableServices' => $this->client_id
                ? Service::where('client_id', $this->client_id)->orderBy('id', 'desc')->get()
                : collect(),
            'types' => WorkHourType::cases(),
            'modes' => WorkHourMode::cases(),
        ]);
    }
}
