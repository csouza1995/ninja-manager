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

    public function test_wdhm_to_minutes_respects_custom_bases(): void
    {
        // base_d=6, base_w=4 → 1w = 4d * 6h * 60m = 1440
        $this->assertSame(1440, ClientWorkHour::wdhmToMinutes(1, 0, 0, 0, 6, 4));
    }

    public function test_to_formatted_hh_mm_on_model(): void
    {
        $wh = ClientWorkHour::factory()->make(['executed_minutes' => 125]);
        $this->assertSame('2:05', $wh->toFormattedHhMm());
    }

    public function test_to_wdhm_decomposition(): void
    {
        // 2400 min with base_d=8, base_w=5 → 1w 0d 0h 0m
        $wh = ClientWorkHour::factory()->make([
            'executed_minutes' => 2400,
            'base_d' => 8,
            'base_w' => 5,
        ]);

        $this->assertSame(['w' => 1, 'd' => 0, 'h' => 0, 'm' => 0], $wh->toWdhm());
    }

    // ──────────────────────────────────────────────
    // Pending balance calculation
    // ──────────────────────────────────────────────

    public function test_index_page_computes_pending_balance(): void
    {
        $client = Client::factory()->create();

        ClientWorkHour::factory()->create([
            'client_id' => $client->id,
            'type' => WorkHourType::Executed,
            'executed_minutes' => 300,
        ]);
        ClientWorkHour::factory()->create([
            'client_id' => $client->id,
            'type' => WorkHourType::Paid,
            'executed_minutes' => 120,
        ]);

        Livewire::test(Index::class, ['clientId' => $client->id])
            ->assertSee('3:00') // 180 min pending = 3:00
            ->assertSee('pendentes');
    }

    public function test_pending_balance_is_zero_when_fully_paid(): void
    {
        $client = Client::factory()->create();

        ClientWorkHour::factory()->create([
            'client_id' => $client->id,
            'type' => WorkHourType::Executed,
            'executed_minutes' => 480,
        ]);
        ClientWorkHour::factory()->create([
            'client_id' => $client->id,
            'type' => WorkHourType::Paid,
            'executed_minutes' => 480,
        ]);

        Livewire::test(Index::class, ['clientId' => $client->id])
            ->assertSee('0:00');
    }

    // ──────────────────────────────────────────────
    // Form component
    // ──────────────────────────────────────────────

    public function test_form_creates_work_hour_in_hhm_mode(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class)
            ->call('open', null, $client->id)
            ->set('mode', WorkHourMode::HoursMinutes->value)
            ->set('hh_mm', '2:30')
            ->call('save');

        $this->assertDatabaseHas('client_work_hours', [
            'client_id' => $client->id,
            'executed_minutes' => 150,
        ]);
    }

    public function test_form_creates_work_hour_in_wdhm_mode(): void
    {
        $client = Client::factory()->create();

        Livewire::test(Form::class)
            ->call('open', null, $client->id)
            ->set('mode', WorkHourMode::Wdhm->value)
            ->set('weeks', 1)
            ->set('days', 0)
            ->set('hours', 0)
            ->set('minutes', 0)
            ->call('save');

        // 1w * 5d * 8h * 60m = 2400
        $this->assertDatabaseHas('client_work_hours', [
            'client_id' => $client->id,
            'executed_minutes' => 2400,
            'weeks' => 1,
        ]);
    }
}
