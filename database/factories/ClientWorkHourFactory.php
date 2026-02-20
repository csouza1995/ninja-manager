<?php

declare(strict_types=1);

namespace Database\Factories;

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
        $baseD = 8;
        $baseW = 5;
        $w = $this->faker->numberBetween(0, 2);
        $d = $this->faker->numberBetween(0, $baseW - 1);
        $h = $this->faker->numberBetween(0, $baseD - 1);
        $m = $this->faker->numberBetween(0, 59);
        $executedMinutes = ClientWorkHour::wdhmToMinutes($w, $d, $h, $m, $baseD, $baseW);

        return [
            'client_id' => Client::factory(),
            'type' => WorkHourType::Executed,
            'mode' => $mode,
            'contract_minutes' => $this->faker->numberBetween(4800, 14400), // 80–240 h
            'executed_minutes' => $executedMinutes,
            'weeks' => $w,
            'days' => $d,
            'hours' => $h,
            'minutes' => $m,
            'base_d' => $baseD,
            'base_w' => $baseW,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function paid(): static
    {
        return $this->state(['type' => WorkHourType::Paid]);
    }
}
