<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\WorkHourContractType;
use App\Enums\WorkHourMode;
use App\Enums\WorkHourType;
use App\Models\Client;
use App\Models\ClientWorkHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClientWorkHour>
 */
class ClientWorkHourFactory extends Factory
{
    protected $model = ClientWorkHour::class;

    public function definition(): array
    {
        $mode = $this->faker->randomElement(WorkHourMode::cases());
        $contractType = $this->faker->randomElement(WorkHourContractType::cases());

        $baseD = 8;
        $baseW = 5;

        // Executed logic
        $w = $this->faker->numberBetween(0, 2);
        $d = $this->faker->numberBetween(0, $baseW - 1);
        $h = $this->faker->numberBetween(0, $baseD - 1);
        $m = $this->faker->numberBetween(0, 59);
        $executedMinutes = ClientWorkHour::wdhmToMinutes($w, $d, $h, $m, $baseD, $baseW);

        // Contract logic
        $cw = $this->faker->numberBetween(0, 4);
        $cd = $this->faker->numberBetween(0, $baseW - 1);
        $ch = $this->faker->numberBetween(0, $baseD - 1);
        $cm = $this->faker->numberBetween(0, 59);
        $contractMinutes = ClientWorkHour::wdhmToMinutes($cw, $cd, $ch, $cm, $baseD, $baseW);

        return [
            'client_id' => Client::factory(),
            'type' => WorkHourType::Executed,
            'mode' => $mode,
            'contract_type' => $contractType,
            'contract_minutes' => $contractMinutes,
            'executed_minutes' => $executedMinutes,
            'weeks' => $w,
            'days' => $d,
            'hours' => $h,
            'minutes' => $m,
            'contract_weeks' => $cw,
            'contract_days' => $cd,
            'contract_hours_raw' => $ch,
            'contract_minutes_raw' => $cm,
            'base_d' => $baseD,
            'base_w' => $baseW,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function paid(): self
    {
        return $this->state(fn (array $attributes) => [
            'type' => WorkHourType::Paid,
        ]);
    }
}
