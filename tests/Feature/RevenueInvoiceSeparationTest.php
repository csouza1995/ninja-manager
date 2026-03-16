<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RevenueInvoiceSeparationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the Dashboard component loads successfully with the new data structure.
     */
    public function test_dashboard_loads_without_errors(): void
    {
        // Assert it doesn't crash when loading the component
        Livewire::test(\App\Livewire\Pages\Financial\Dashboard::class)
            ->assertStatus(200)
            ->assertSeeHtml('Recebimentos');

        // Also check if the old tax_amount was correctly removed from revenues
        $this->assertFalse(\Schema::hasColumn('revenues', 'tax_amount'), 'Revenues should not have tax_amount');
        $this->assertTrue(\Schema::hasColumn('invoices', 'tax_amount'), 'Invoices should have tax_amount');
    }
}
