<?php

namespace Tests\Feature;

use App\Livewire\Pages\Financial\Dashboard;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinancialDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_correctly_identifies_february_when_today_is_march_30th()
    {
        // Mock current date to March 30th, 2026
        Carbon::setTestNow(Carbon::create(2026, 3, 30));

        // Use English locale for the test consistency
        app()->setLocale('en');

        $component = Livewire::test(Dashboard::class);
        $component->assertSet('activeFilter', 'month')
            ->assertSet('periodCount', 3)
            ->assertOk();

        $periods = $component->get('periods');

        // Debugging output if needed:
        // fwrite(STDERR, print_r(array_keys($periods), TRUE));

        $this->assertArrayHasKey('Feb/2026', $periods, 'February 2026 should be in the periods.');
        $this->assertArrayHasKey('Mar/2026', $periods, 'March 2026 should be in the periods.');
        $this->assertArrayHasKey('Apr/2026', $periods, 'April 2026 should be in the periods.');

        Carbon::setTestNow(); // Reset
    }

    /** @test */
    public function it_correctly_identifies_quarters_when_today_is_march_30th()
    {
        // Mock current date to March 30th, 2026
        Carbon::setTestNow(Carbon::create(2026, 3, 30));

        // Use English locale for the test consistency
        app()->setLocale('en');

        $component = Livewire::test(Dashboard::class);
        $component->call('setFilter', 'quarter');

        $periods = $component->get('periods');

        // Quarter labels are defined as ucfirst(start month)-ucfirst(end month/year)
        // Today is March 30th (Q1).
        // With periodCount=3: Q4/2025, Q1/2026, Q2/2026

        $this->assertArrayHasKey('Oct-Dec/2025', $periods, 'Q4 2025 should be in the periods.');
        $this->assertArrayHasKey('Jan-Mar/2026', $periods, 'Q1 2026 should be in the periods.');
        $this->assertArrayHasKey('Apr-Jun/2026', $periods, 'Q2 2026 should be in the periods.');

        Carbon::setTestNow(); // Reset
    }
}
