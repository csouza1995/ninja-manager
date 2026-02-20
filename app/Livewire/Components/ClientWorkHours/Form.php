<?php

declare(strict_types=1);

namespace App\Livewire\Components\ClientWorkHours;

use App\Enums\WorkHourContractType;
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

    public string $contract_type = 'fixed';

    /** Hourly rate value */
    public float $hourly_rate = 0;

    /** HHH:MM mode — raw input string */
    public string $hh_mm = '0:00';

    /** WDHM mode — raw inputs for Executed */
    public int $weeks = 0;

    public int $days = 0;

    public int $hours = 0;

    public int $minutes = 0;

    /** Contract hours input (HHH:MM string or components) */
    public string $contract_hh_mm = '';

    /** WDHM mode — raw inputs for Contracted */
    public int $contract_weeks = 0;

    public int $contract_days = 0;

    public int $contract_hours_raw = 0;

    public int $contract_minutes_raw = 0;

    public int $base_d = 8;

    public int $base_w = 5;

    #[Validate('nullable|array')]
    public array $service_ids = [];

    public string $notes = '';

    #[On('open-work-hour-form')]
    public function open(?int $id = null, ?int $clientId = null): void
    {
        $this->reset(['workHourId', 'hh_mm', 'weeks', 'days', 'hours', 'minutes',
            'contract_hh_mm', 'contract_weeks', 'contract_days', 'contract_hours_raw', 'contract_minutes_raw',
            'service_ids', 'notes', 'hourly_rate']);
        $this->type = 'executed';
        $this->mode = 'hhh_mm';
        $this->contract_type = 'fixed';
        $this->base_d = 8;
        $this->base_w = 5;
        $this->hh_mm = '0:00';
        $this->hourly_rate = 0;

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
        $this->contract_type = ($wh->contract_type ?? WorkHourContractType::Fixed)->value;
        $this->hourly_rate = (float) $wh->hourly_rate;
        $this->base_d = $wh->base_d;
        $this->base_w = $wh->base_w;

        // Executed
        $this->weeks = $wh->weeks;
        $this->days = $wh->days;
        $this->hours = $wh->hours;
        $this->minutes = $wh->minutes;
        $this->hh_mm = $wh->toFormattedHhMm();

        // Contracted
        $this->contract_weeks = $wh->contract_weeks ?? 0;
        $this->contract_days = $wh->contract_days ?? 0;
        $this->contract_hours_raw = $wh->contract_hours_raw ?? 0;
        $this->contract_minutes_raw = $wh->contract_minutes_raw ?? 0;
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

        /** Contract is only allowed for 'executed' type */
        $contractMinutes = null;
        if ($this->type === WorkHourType::Executed->value) {
            $contractMinutes = $this->computeContractMinutes();
        }

        /** Decompose for Executed if in HHH:MM mode for internal storage consistency */
        $wdhm = $this->mode === WorkHourMode::Wdhm->value
            ? ['weeks' => $this->weeks, 'days' => $this->days, 'hours' => $this->hours, 'minutes' => $this->minutes]
            : $this->decomposeToWdhm($executedMinutes);

        /** Decompose for Contracted if in HHH:MM mode */
        $contractWdhm = [
            'weeks' => 0, 'days' => 0, 'hours' => 0, 'minutes' => 0,
        ];

        if ($this->type === WorkHourType::Executed->value) {
            $contractWdhm = $this->mode === WorkHourMode::Wdhm->value
                ? ['weeks' => $this->contract_weeks, 'days' => $this->contract_days, 'hours' => $this->contract_hours_raw, 'minutes' => $this->contract_minutes_raw]
                : ($contractMinutes !== null ? $this->decomposeToWdhm($contractMinutes) : $contractWdhm);
        }

        $wh = ClientWorkHour::updateOrCreate(
            ['id' => $this->workHourId],
            [
                'client_id' => $this->client_id,
                'type' => $this->type,
                'mode' => $this->mode,
                'contract_type' => $this->type === WorkHourType::Executed->value ? $this->contract_type : null,
                'hourly_rate' => $this->hourly_rate,
                'contract_minutes' => $contractMinutes,
                'executed_minutes' => $executedMinutes,
                'weeks' => $wdhm['weeks'],
                'days' => $wdhm['days'],
                'hours' => $wdhm['hours'],
                'minutes' => $wdhm['minutes'],
                'contract_weeks' => $this->type === WorkHourType::Executed->value ? $contractWdhm['weeks'] : null,
                'contract_days' => $this->type === WorkHourType::Executed->value ? $contractWdhm['days'] : null,
                'contract_hours_raw' => $this->type === WorkHourType::Executed->value ? $contractWdhm['hours'] : null,
                'contract_minutes_raw' => $this->type === WorkHourType::Executed->value ? $contractWdhm['minutes'] : null,
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
        if ($this->mode === WorkHourMode::Wdhm->value) {
            if ($this->contract_weeks === 0 && $this->contract_days === 0 && $this->contract_hours_raw === 0 && $this->contract_minutes_raw === 0) {
                return null;
            }

            return ClientWorkHour::wdhmToMinutes(
                $this->contract_weeks, $this->contract_days, $this->contract_hours_raw, $this->contract_minutes_raw,
                $this->base_d, $this->base_w
            );
        }

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
            'contractTypes' => WorkHourContractType::cases(),
        ]);
    }
}
