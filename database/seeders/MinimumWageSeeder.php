<?php

namespace Database\Seeders;

use App\Models\MinimumWage;
use Illuminate\Database\Seeder;

class MinimumWageSeeder extends Seeder
{
    public function run(): void
    {
        $wages = [
            ['effective_date' => '2024-01-01', 'amount' => 1412.00],
            ['effective_date' => '2025-01-01', 'amount' => 1518.00],
            ['effective_date' => '2026-01-01', 'amount' => 1621.00],
        ];

        foreach ($wages as $wage) {
            MinimumWage::updateOrCreate(
                ['effective_date' => $wage['effective_date']],
                $wage
            );
        }
    }
}
