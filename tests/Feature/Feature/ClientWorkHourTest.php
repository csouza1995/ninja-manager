<?php

declare(strict_types=1);

namespace Tests\Feature\Feature;

use App\Enums\WorkHourMode;
use App\Enums\WorkHourType;
use App\Livewire\Components\ClientWorkHours\Form;
use App\Livewire\Pages\Clients\WorkHours\Index;
use App\Models\Client;
use App\Models\ClientWorkHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientWorkHourTest extends TestCase
{
    use RefreshDatabase;

    // ──────────────────────────────────────────────
    // Unit-level conversion helpers
    // ──────────────────────────────────────────────

    public function test_hhm_to_minutes_converts_correctly(): void
    {
        $this->assertSame(0, ClientWorkHour::hhMmToMinutes('0:00'));
        $this->assertSame(60, ClientWorkHour::hhMmToMinutes('1:00'));
        $this->assertSame(90, ClientWorkHour::hhMmToMinutes('1:30'));
        $this->assertSame(2460, ClientWorkHour::hhMmToMinutes('41:00'));
    }

    public function test_wdhm_to_minutes_converts_correctly_with_defaults(): void
    {
        // 1w = 5d * 8h * 60m = 2400 min
        $this->assertSame(2400, ClientWorkHour::wdhmToMinutes(1, 0, 0, 0));
        // 1d = 8h * 60m = 480 min
        $this->assertSame(480, ClientWorkHour::wdhmToMinutes(0, 1, 0, 0));
        // 1h = 60 min
        $this->assertSame(60, ClientWorkHour::wdhmToMinutes(0, 0, 1, 0));
        // 1w 2d 3h 30m = 2400 + 960 + 180 + 30 = 3570
        $this->assertSame(3570, ClientWorkHour::wdhmToMinutes(1, 2, 3, 30));
    }

    public function test_to_formatted_hh_mm_on_model(): void
    {
        $wh = ClientWorkHour::factory()->make(['executed_minutes' => 125]);
        $this->assertSame('2:05', $wh->toFormattedHhMm());
    }

    // ──────────────────────────────────────────────
    // Monetization & Metrics
    // ──────────────────────────────────────────────

    public function test_model_calculates_monetary_values(): void
    {
        // 2 hours @ R$ 100/h = R$ 200
        $wh = ClientWorkHour::factory()->make([
            'executed_minutes' => 120,
            'hourly_rate' => 100.00,
        ]);

        $this->assertEquals(200.00, $wh->executed_value);
    }

    public function test_model_calculates_achievement_percentage(): void
    {
        // 40 min executed / 80 min contract = 50%
        $wh = ClientWorkHour::factory()->make([
            'executed_minutes' => 40,
            'contract_minutes' => 80,
        ]);

        $this->assertEquals(50.0, $wh->achievementPercent());

        // Over achievement caps at 100% in UI logic or shows actual?
        // My implementation uses min(100, ...).
        $wh2 = ClientWorkHour::factory()->make([
            'executed_minutes' => 120,
            'contract_minutes' => 80,
        ]);
        $this->assertEquals(100.0, $wh2->achievementPercent());
    }

    public function test_index_page_computes_monetary_metrics(): void
    {
        $client = Client::factory()->create();

        // 2h Executed @ R$ 50 = R$ 100
        ClientWorkHour::factory()->create([
            'client_id' => $client->id,
            'type' => WorkHourType::Executed,
            'executed_minutes' => 120,
            'hourly_rate' => 50.00,
        ]);

        // 1h Paid @ R$ 50 = R$ 50
        ClientWorkHour::factory()->create([
            'client_id' => $client->id,
            'type' => WorkHourType::Paid,
            'executed_minutes' => 60,
            'hourly_rate' => 50.00,
        ]);

        Livewire::test(Index::class, ['clientId' => $client->id])
            ->assertSee('1:00')                // Pending hours
            ->assertSee('R$ 50,00')             // Pending value
            ->assertSee('R$ 100,00');           // Total Executed value
    }

    // ──────────────────────────────────────────────
    // Form component - Refined Paid logic & Hourly Rate
    // ──────────────────────────────────────────────

    public function test_form_creates_work_hour_with_hourly_rate(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class)
            ->call('open', null, $client->id)
            ->set('type', WorkHourType::Executed->value)
            ->set('hh_mm', '2:00')
            ->set('hourly_rate', 150.50)
            ->call('save');

        $this->assertDatabaseHas('client_work_hours', [
            'client_id' => $client->id,
            'executed_minutes' => 120,
            'hourly_rate' => 150.50,
        ]);
    }

    public function test_form_ignores_contract_when_type_is_paid(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class)
            ->call('open', null, $client->id)
            ->set('type', WorkHourType::Paid->value)
            ->set('hh_mm', '1:00')
            ->set('contract_hh_mm', '40:00') // Should be ignored
            ->call('save');

        $this->assertDatabaseHas('client_work_hours', [
            'client_id' => $client->id,
            'type' => WorkHourType::Paid->value,
            'contract_minutes' => null,
        ]);
    }

    public function test_form_handles_symmetric_wdhm_with_contract(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class)
            ->call('open', null, $client->id)
            ->set('mode', WorkHourMode::Wdhm->value)
            ->set('weeks', 1)           // 2400 min
            ->set('contract_weeks', 2)  // 4800 min
            ->call('save');

        $this->assertDatabaseHas('client_work_hours', [
            'client_id' => $client->id,
            'executed_minutes' => 2400,
            'contract_minutes' => 4800,
        ]);
    }
}
