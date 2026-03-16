<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->decimal('opening_balance', 15, 2)->default(0)->after('nickname');
            $table->date('opening_balance_date')->nullable()->after('opening_balance');
        });

        // Migração de dados
        $revenues = \DB::table('revenues')
            ->where('classification', 'Inicial')
            ->orWhere('description', 'like', '%Saldo Inicial%')
            ->get();

        foreach ($revenues as $revenue) {
            \DB::table('bank_accounts')
                ->where('id', $revenue->bank_account_id)
                ->update([
                    'opening_balance' => $revenue->gross_amount,
                    'opening_balance_date' => $revenue->paid_at ?? $revenue->due_date,
                    'updated_at' => now(),
                ]);
        }

        // Remover os registros de saldo inicial das receitas
        \DB::table('revenues')
            ->where('classification', 'Inicial')
            ->orWhere('description', 'like', '%Saldo Inicial%')
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn(['opening_balance', 'opening_balance_date']);
        });
    }
};
