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
        Schema::create('outflows', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Lucro/Dividendos', 'Prolabore', 'Investimento', 'Transferência']);
            $table->string('description');
            $table->foreignId('origin_bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->foreignId('destination_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('person_name')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('due_date');
            $table->date('paid_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outflows');
    }
};
