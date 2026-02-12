<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('executor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['negotiating', 'cancelled', 'in_progress', 'delivered', 'finalized'])
                ->default('negotiating');
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_documented')->default(false);
            $table->boolean('is_invoiced')->default(false);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'is_paid', 'is_documented', 'is_invoiced']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
