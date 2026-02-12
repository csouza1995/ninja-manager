<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('receipt_number')->unique(); // Format: 000/YY
            $table->integer('year');
            $table->integer('sequence');
            $table->enum('status', ['generated', 'signed', 'sent'])->default('generated');
            $table->timestamps();

            $table->index(['year', 'sequence']);
            $table->unique(['year', 'sequence']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
